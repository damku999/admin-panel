<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ReportServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends BaseApiController
{
    public function __construct(
        private ReportServiceInterface $reportService
    ) {
        $this->middleware('auth:sanctum');
    }
    
    public function getDashboardStats(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);
        
        try {
            $stats = $this->reportService->getDashboardStatistics($request->all());
            
            return $this->successResponse($stats, 'Dashboard statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getCustomerStatistics(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'customer_type' => 'nullable|string|in:Corporate,Retail',
        ]);
        
        try {
            $stats = $this->reportService->getCustomerStatistics($request->all());
            
            return $this->successResponse($stats, 'Customer statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getPolicyStatistics(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'policy_type_id' => 'nullable|integer|exists:policy_types,id',
            'insurance_company_id' => 'nullable|integer|exists:insurance_companies,id',
        ]);
        
        try {
            $stats = $this->reportService->getPolicyStatistics($request->all());
            
            return $this->successResponse($stats, 'Policy statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getCommissionReport(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'broker_id' => 'nullable|integer|exists:brokers,id',
            'insurance_company_id' => 'nullable|integer|exists:insurance_companies,id',
            'group_by' => 'nullable|string|in:broker,company,month,policy_type',
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
        ]);
        
        try {
            $report = $this->reportService->getCommissionReport($request->all());
            
            return $this->successResponse($report, 'Commission report retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getCrossSellingReport(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'customer_id' => 'nullable|integer|exists:customers,id',
            'min_policies' => 'nullable|integer|min:1',
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
        ]);
        
        try {
            $report = $this->reportService->getCrossSellingReport($request->all());
            
            return $this->successResponse($report, 'Cross-selling report retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getExpiringPoliciesReport(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'integer|min:1|max:365',
            'insurance_company_id' => 'nullable|integer|exists:insurance_companies,id',
            'broker_id' => 'nullable|integer|exists:brokers,id',
            'policy_type_id' => 'nullable|integer|exists:policy_types,id',
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
        ]);
        
        try {
            $days = $request->get('days', 30);
            $report = $this->reportService->getExpiringPoliciesReport($days, $request->all());
            
            return $this->successResponse($report, 'Expiring policies report retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getBusinessTrendsReport(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'period' => 'nullable|string|in:daily,weekly,monthly,quarterly,yearly',
            'metric' => 'nullable|string|in:policies,premium,commission,customers',
        ]);
        
        try {
            $period = $request->get('period', 'monthly');
            $metric = $request->get('metric', 'policies');
            $report = $this->reportService->getBusinessTrendsReport($period, $metric, $request->all());
            
            return $this->successResponse($report, 'Business trends report retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getTopPerformersReport(Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'type' => 'required|string|in:brokers,companies,customers',
            'metric' => 'nullable|string|in:policies,premium,commission',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);
        
        try {
            $metric = $request->get('metric', 'premium');
            $limit = $request->get('limit', 10);
            $report = $this->reportService->getTopPerformersReport($request->type, $metric, $limit, $request->all());
            
            return $this->successResponse($report, 'Top performers report retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getCustomReport(Request $request): JsonResponse
    {
        $request->validate([
            'report_type' => 'required|string|in:customer_analysis,policy_analysis,commission_analysis,renewal_analysis',
            'filters' => 'nullable|array',
            'group_by' => 'nullable|array',
            'metrics' => 'nullable|array',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
        ]);
        
        try {
            $report = $this->reportService->generateCustomReport($request->all());
            
            return $this->successResponse($report, 'Custom report generated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
}