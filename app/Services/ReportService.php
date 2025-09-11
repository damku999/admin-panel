<?php

namespace App\Services;

use App\Contracts\Services\ReportServiceInterface;
use App\Exports\CrossSellingExport;
use App\Exports\CustomerInsurancesExport1;
use App\Models\Branch;
use App\Models\Broker;
use App\Models\Customer;
use App\Models\FuelType;
use App\Models\InsuranceCompany;
use App\Models\PolicyType;
use App\Models\PremiumType;
use App\Models\ReferenceUser;
use App\Models\RelationshipManager;
use App\Models\Report;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportService implements ReportServiceInterface
{
    public function __construct(
        private CacheService $cacheService
    ) {
    }

    /**
     * Get initial data for reports page with caching
     */
    public function getInitialData(): array
    {
        return $this->cacheService->cacheQuery('report_initial_data', [], function () {
            return [
                'customers' => Customer::select('id', 'name')->get(),
                'brokers' => $this->cacheService->getBrokers()->map(function ($item) {
                    return (object)['id' => $item->id ?? $item['id'], 'name' => $item->name ?? $item['name']];
                }),
                'relationship_managers' => RelationshipManager::select('id', 'name')->get(),
                'branches' => Branch::select('id', 'name')->get(),
                'insurance_companies' => $this->cacheService->getInsuranceCompanies()->map(function ($item) {
                    return (object)['id' => $item->id ?? $item['id'], 'name' => $item->name ?? $item['name']];
                }),
                'policy_type' => $this->cacheService->getPolicyTypes()->map(function ($item) {
                    return (object)['id' => $item->id ?? $item['id'], 'name' => $item->name ?? $item['name']];
                }),
                'fuel_type' => $this->cacheService->getFuelTypes()->map(function ($item) {
                    return (object)['id' => $item->id ?? $item['id'], 'name' => $item->name ?? $item['name']];
                }),
                'premium_types' => $this->cacheService->getPremiumTypes(),
                'reference_by_user' => ReferenceUser::select('id', 'name')->get(),
                'customerInsurances' => [],
                'crossSelling' => [],
            ];
        });
    }

    /**
     * Generate cross selling report with analysis - cached for performance
     */
    public function generateCrossSellingReport(array $parameters): array
    {
        return $this->cacheService->cacheReport('cross_selling', $parameters, function () use ($parameters) {
            $premiumTypes = PremiumType::select(['id', 'name', 'is_vehicle', 'is_life_insurance_policies']);
            if (!empty($parameters['premium_type_id'])) {
                $premiumTypes = $premiumTypes->whereIn('id', $parameters['premium_type_id']);
            }
            $premiumTypes = $premiumTypes->get();

            $customer_obj = Customer::with(['insurance.premiumType'])
                ->orderBy('name');

            // Apply date filters if provided
            $hasDateFilter = false;
            if (!empty($parameters['issue_start_date']) || !empty($parameters['issue_end_date'])) {
                $customer_obj = $customer_obj->whereHas('insurance', function ($query) use ($parameters) {
                    if (!empty($parameters['issue_start_date'])) {
                        $startDate = \App\Helpers\DateHelper::isValidDatabaseFormat($parameters['issue_start_date'])
                            ? $parameters['issue_start_date']
                            : formatDateForDatabase($parameters['issue_start_date']);
                        $query->where('start_date', '>=', $startDate);
                    }
                    if (!empty($parameters['issue_end_date'])) {
                        $endDate = \App\Helpers\DateHelper::isValidDatabaseFormat($parameters['issue_end_date'])
                            ? $parameters['issue_end_date']
                            : formatDateForDatabase($parameters['issue_end_date']);
                        $query->where('start_date', '<=', $endDate);
                    }
                });
                $hasDateFilter = true;
            }

            $customers = $customer_obj->get();
            $oneYearAgo = Carbon::now()->subYear();

            $results = $customers->map(function ($customer) use ($premiumTypes, $oneYearAgo, $hasDateFilter) {
                return $this->analyzeCustomerCrossSellingData($customer, $premiumTypes, $oneYearAgo, $hasDateFilter);
            });

            return [
                'premiumTypes' => $premiumTypes,
                'crossSelling' => $results,
            ];
        });
    }

    /**
     * Generate customer insurance report
     */
    public function generateCustomerInsuranceReport(array $parameters): array
    {
        $result = Report::getInsuranceReport($parameters);
        return $result ? $result->toArray() : [];
    }

    /**
     * Export cross selling report to Excel
     */
    public function exportCrossSellingReport(array $parameters): BinaryFileResponse
    {
        $timestamp = date('Y-m-d_H-i-s');
        return Excel::download(new CrossSellingExport($parameters), "cross_selling_report_{$timestamp}.xlsx");
    }

    /**
     * Export customer insurance report to Excel
     */
    public function exportCustomerInsuranceReport(array $parameters): BinaryFileResponse
    {
        // Generate filename based on report type
        $reportName = $parameters['report_name'] ?? 'customer_insurances';
        $timestamp = date('Y-m-d_H-i-s');
        
        $filename = match($reportName) {
            'insurance_detail' => "insurance_detail_report_{$timestamp}.xlsx",
            'due_policy_detail' => "due_policy_report_{$timestamp}.xlsx", 
            default => "customer_insurances_{$timestamp}.xlsx"
        };

        return Excel::download(new CustomerInsurancesExport1($parameters), $filename);
    }

    /**
     * Save user's selected columns for a report
     */
    public function saveUserReportColumns(string $reportName, array $selectedColumns, int $userId): void
    {
        $updatedColumns = [];
        foreach (config('constants.INSURANCE_DETAIL') as $column) {
            $selected = in_array($column['table_column_name'], $selectedColumns) ? 'Yes' : 'No';
            $column['selected_column'] = $selected;
            $updatedColumns[] = $column;
        }

        Report::updateOrCreate([
            'name' => $reportName,
            'user_id' => $userId,
        ], [
            'name' => $reportName,
            'user_id' => $userId,
            'selected_columns' => $updatedColumns,
        ]);
    }

    /**
     * Load user's saved columns for a report
     */
    public function loadUserReportColumns(string $reportName, int $userId): ?array
    {
        $report = Report::where([
            'name' => $reportName,
            'user_id' => $userId,
        ])->first();

        return $report ? $report->selected_columns : null;
    }

    /**
     * Analyze customer cross selling data for premium types
     */
    private function analyzeCustomerCrossSellingData($customer, $premiumTypes, $oneYearAgo, $hasDateFilter): array
    {
        $customerData = [
            'customer_name' => $customer->name,
            'id' => $customer->id,
            'total_premium_last_year' => 0,
            'actual_earnings_last_year' => 0,
            'premium_totals' => [],
        ];

        foreach ($premiumTypes as $premiumType) {
            $hasPremiumType = $customer->insurance->contains(function ($insurance) use ($premiumType) {
                return $insurance->premiumType->id === $premiumType->id;
            });

            $premiumTotal = $customer->insurance
                ->where('premium_type_id', $premiumType->id)
                ->when(!$hasDateFilter, function ($query) use ($oneYearAgo) {
                    return $query->where('start_date', '>=', $oneYearAgo);
                })
                ->sum('final_premium_with_gst');

            $customerData['total_premium_last_year'] += $premiumTotal;

            $customerData['actual_earnings_last_year'] = $customerData['actual_earnings_last_year'] + $customer->insurance
                ->where('premium_type_id', $premiumType->id)
                ->when(!$hasDateFilter, function ($query) use ($oneYearAgo) {
                    return $query->where('actual_earnings', '>=', $oneYearAgo);
                })
                ->sum('actual_earnings');

            $customerData['premium_totals'][$premiumType->name] = [
                'has_premium' => $hasPremiumType ? 'Yes' : 'No',
                'amount' => $premiumTotal > 0 ? $premiumTotal : 0,
            ];
        }

        return $customerData;
    }
}