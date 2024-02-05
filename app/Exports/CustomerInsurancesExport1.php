<?php

namespace App\Exports;


use App\Models\CustomerInsurance;
use App\Models\Report;
use Carbon\Carbon;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerInsurancesExport1 implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $filters;
    protected $selected_columns;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
        // $this->selected_columns = $selected_columns;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $report = Report::where(['user_id' => auth()->user()->id, 'name' => $this->filters['report_name']])->first();
        // $filteredColumns = collect($report->selected_columns)->filter(function ($column) {
        //     return $column['relation_model'] !== '';
        // });
        // $select_data = collect($report->selected_columns)->filter(function ($column) {
        //     return $column['relation_model'] === '' && ($column['default_visible'] == 'Yes' || $column['selected_column'] == 'Yes');
        // });
        $report->selected_columns = collect($report->selected_columns)->map(function ($item) {
            $item['select'] = $item['table_column_name'] . ' as ' . $item['display_name'];
            return $item;
        })->pluck('select');

        // dd($report->selected_columns);
        $customerInsurances = CustomerInsurance::with(
            'branch',
            'broker',
            'relationshipManager',
            'customer',
            'insuranceCompany',
            'premiumType',
            'policyType',
            'fuelType'
        )
            ->when(!empty($this->filters['record_creation_start_date']), function ($query) {
                return $query->where('created_at', '>=', Carbon::parse($this->filters['record_creation_start_date'])->startOfDay()->format('Y-m-d H:i:s'));
            })
            ->when(!empty($this->filters['record_creation_end_date']), function ($query) {
                return $query->where('created_at', '<=', Carbon::parse($this->filters['record_creation_end_date'])->endOfDay()->format('Y-m-d H:i:s'));
            })
            ->when(!empty($this->filters['issue_start_date']), function ($query) {
                return $query->where('issue_date', '>=', Carbon::parse($this->filters['issue_start_date'])->format('Y-m-d'));
            })
            ->when(!empty($this->filters['issue_end_date']), function ($query) {
                return $query->where('issue_date', '<=', Carbon::parse($this->filters['issue_end_date'])->format('Y-m-d'));
            })
            ->when(!empty($this->filters['branch_id']), function ($query) {
                return $query->whereHas('branch', function ($query) {
                    $query->where('id', $this->filters['branch_id']);
                });
            })
            ->when(!empty($this->filters['broker_id']), function ($query) {
                return $query->whereHas('broker', function ($query) {
                    $query->where('id', $this->filters['broker_id']);
                });
            })
            ->when(!empty($this->filters['relationship_manager_id']), function ($query) {
                return $query->whereHas('relationshipManager', function ($query) {
                    $query->where('id', $this->filters['relationship_manager_id']);
                });
            })
            ->when(!empty($this->filters['insurance_company_id']), function ($query) {
                return $query->whereHas('insuranceCompany', function ($query) {
                    $query->where('id', $this->filters['insurance_company_id']);
                });
            })
            ->when(!empty($this->filters['policy_type_id']), function ($query) {
                return $query->whereHas('policyType', function ($query) {
                    $query->where('id', $this->filters['policy_type_id']);
                });
            })
            ->when(!empty($this->filters['fuel_type_id']), function ($query) {
                return $query->whereHas('fuelType', function ($query) {
                    $query->where('id', $this->filters['fuel_type_id']);
                });
            })
            ->when(!empty($this->filters['premium_type_id']), function ($query) {
                return $query->whereHas('premiumType', function ($query) {
                    $query->where('id', $this->filters['premium_type_id']);
                });
            })
            ->when(!empty($this->filters['customer_id']), function ($query) {
                return $query->whereHas('customer', function ($query) {
                    $query->where('id', $this->filters['customer_id']);
                });
            })

            ->get();

        return $customerInsurances->map(function ($customerInsurance) {
            return [
                'Customer' => $customerInsurance->customer->name,
                'Branch' => $customerInsurance->branch->name,
                'Broker' => $customerInsurance->broker->name,
                'RM' => $customerInsurance->relationshipManager->name,
                'Insurance Company' => $customerInsurance->insuranceCompany->name,
                'Premium Type' => $customerInsurance->premiumType->name,
                'Policy Type' => $customerInsurance->policyType->name,
                'Fuel Type' => @$customerInsurance->fuelType->name,
                'Issue Date' => $customerInsurance->issue_date,
                'Policy Number' => $customerInsurance->policy_no,
                'registration Number' => $customerInsurance->registration_no,
                'RTO' => $customerInsurance->rto,
                'Make & Model' => $customerInsurance->make_mode,
                'Commission On' => $customerInsurance->commission_on,
                'Start Date' => $customerInsurance->start_date,
                'Expired Date' => $customerInsurance->expired_date,
                'TP Expiry Date' => $customerInsurance->tp_expiry_date,
                'OD Premium' => $customerInsurance->od_premium,
                'TP Premium' => $customerInsurance->tp_premium,
                'Net Premium' => $customerInsurance->net_premium,
                'Final Premium With GST' => $customerInsurance->final_premium_with_gst,
                'SGST 1' => $customerInsurance->sgst1,
                'CGST 1' => $customerInsurance->cgst1,
                'CGST 2' => $customerInsurance->cgst2,
                'SGCT 2' => $customerInsurance->sgst2,
                'My Commission Percentage' => $customerInsurance->my_commission_percentage,
                'My Commission Amount' => $customerInsurance->my_commission_amount,
                'Transfer Commission Percentage' => $customerInsurance->transfer_commission_percentage,
                'Transfer Commission Amount' => $customerInsurance->transfer_commission_amount,
                'Actual Earnings' => $customerInsurance->actual_earnings,
                'NCB Percentage' => $customerInsurance->ncb_percentage,
                'Mode Of Payment' => $customerInsurance->mode_of_payment,
                'Cheque Number' => $customerInsurance->cheque_no,
                'Insurance Status' => $customerInsurance->insurance_status,
                'Gross Vehicle Weight' => $customerInsurance->gross_vehicle_weight,
                'MFG. Year' => $customerInsurance->mfg_year,
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Customer',
            'Branch',
            'Broker',
            'RM',
            'Insurance Company',
            'Premium Type',
            'Policy Type',
            'Fuel Type',
            'Issue Date',
            'Policy Number',
            'Registration Number',
            'RTO',
            'Make & Model',
            'Commission On',
            'Start Date',
            'Expired Date',
            'TP Expiry Date',
            'OD Premium',
            'TP Premium',
            'Net Premium',
            'Final Premium With GST',
            'SGST 1',
            'CGST 1',
            'CGST 2',
            'SGCT 2',
            'My Commission Percentage',
            'My Commission Amount',
            'Transfer Commission Percentage',
            'Transfer Commission Amount',
            'Actual Earnings',
            'NCB Percentage',
            'Mode Of Payment',
            'Cheque Number',
            'Insurance Status',
            'Gross Vehicle Weight',
            'MFG. Year',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '58C4A7']],
            ],
        ];
    }
    /**
     * @param Worksheet $sheet
     * @param string $filterRange
     */
    public function applyFilter(Worksheet $sheet, string $filterRange)
    {
        $sheet->setAutoFilter($filterRange);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $filterRange = 'A1:AJ1'; // Adjust the filter range as per your requirements

                // Apply filter
                $this->applyFilter($sheet, $filterRange);

                // Header styles
                $sheet->getStyle('A1:AJ1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '58C4A7',
                        ],
                    ],
                ]);

                // Page setup
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            },
        ];
    }
}
