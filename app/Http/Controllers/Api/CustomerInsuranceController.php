<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\CustomerInsuranceServiceInterface;
use App\Http\Resources\CustomerInsuranceResource;
use App\Models\CustomerInsurance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerInsuranceController extends BaseApiController
{
    public function __construct(
        private CustomerInsuranceServiceInterface $customerInsuranceService
    ) {
        $this->middleware('auth:sanctum');
    }
    
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
            'customer_id' => 'integer|exists:customers,id',
            'insurance_company_id' => 'integer|exists:insurance_companies,id',
            'status' => 'integer|in:0,1',
            'expiring_days' => 'integer|min:1|max:365',
            'from_date' => 'date',
            'to_date' => 'date|after_or_equal:from_date',
        ]);
        
        try {
            $insurances = $this->customerInsuranceService->getCustomerInsurances($request);
            
            return $this->successResponse([
                'insurances' => CustomerInsuranceResource::collection($insurances->items()),
                'pagination' => [
                    'current_page' => $insurances->currentPage(),
                    'last_page' => $insurances->lastPage(),
                    'per_page' => $insurances->perPage(),
                    'total' => $insurances->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'insurance_company_id' => 'required|integer|exists:insurance_companies,id',
            'policy_type_id' => 'required|integer|exists:policy_types,id',
            'policy_number' => 'required|string|max:100|unique:customer_insurances,policy_number',
            'start_date' => 'required|date',
            'expired_date' => 'required|date|after:start_date',
            'vehicle_number' => 'nullable|string|max:20',
            'vehicle_make' => 'nullable|string|max:100',
            'vehicle_model' => 'nullable|string|max:100',
            'od_premium' => 'nullable|numeric|min:0',
            'tp_premium' => 'nullable|numeric|min:0',
            'net_premium' => 'nullable|numeric|min:0',
            'gst' => 'nullable|numeric|min:0',
            'total_premium' => 'nullable|numeric|min:0',
            'od_brokerage' => 'nullable|numeric|min:0',
            'tp_brokerage' => 'nullable|numeric|min:0',
            'total_brokerage' => 'nullable|numeric|min:0',
        ]);
        
        try {
            $insurance = $this->customerInsuranceService->createCustomerInsurance($request->validated());
            
            return $this->successResponse(
                new CustomerInsuranceResource($insurance),
                'Customer insurance created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function show(CustomerInsurance $customerInsurance): JsonResponse
    {
        try {
            return $this->successResponse(new CustomerInsuranceResource($customerInsurance));
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function update(Request $request, CustomerInsurance $customerInsurance): JsonResponse
    {
        $request->validate([
            'customer_id' => 'sometimes|required|integer|exists:customers,id',
            'insurance_company_id' => 'sometimes|required|integer|exists:insurance_companies,id',
            'policy_type_id' => 'sometimes|required|integer|exists:policy_types,id',
            'policy_number' => 'sometimes|required|string|max:100|unique:customer_insurances,policy_number,' . $customerInsurance->id,
            'start_date' => 'sometimes|required|date',
            'expired_date' => 'sometimes|required|date|after:start_date',
            'vehicle_number' => 'nullable|string|max:20',
            'vehicle_make' => 'nullable|string|max:100',
            'vehicle_model' => 'nullable|string|max:100',
            'od_premium' => 'nullable|numeric|min:0',
            'tp_premium' => 'nullable|numeric|min:0',
            'net_premium' => 'nullable|numeric|min:0',
            'gst' => 'nullable|numeric|min:0',
            'total_premium' => 'nullable|numeric|min:0',
            'od_brokerage' => 'nullable|numeric|min:0',
            'tp_brokerage' => 'nullable|numeric|min:0',
            'total_brokerage' => 'nullable|numeric|min:0',
        ]);
        
        try {
            $updatedInsurance = $this->customerInsuranceService->updateCustomerInsurance($customerInsurance, $request->validated());
            
            return $this->successResponse(
                new CustomerInsuranceResource($updatedInsurance),
                'Customer insurance updated successfully'
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function destroy(CustomerInsurance $customerInsurance): JsonResponse
    {
        try {
            $this->customerInsuranceService->deleteCustomerInsurance($customerInsurance);
            
            return $this->successResponse(null, 'Customer insurance deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function updateStatus(Request $request, CustomerInsurance $customerInsurance): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:0,1',
        ]);
        
        try {
            $this->customerInsuranceService->updateCustomerInsuranceStatus($customerInsurance->id, $request->status);
            
            return $this->successResponse(null, 'Customer insurance status updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getExpiringPolicies(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'integer|min:1|max:365',
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
        ]);
        
        try {
            $days = $request->get('days', 30);
            $expiringPolicies = $this->customerInsuranceService->getExpiringPolicies($days, $request);
            
            return $this->successResponse([
                'expiring_policies' => CustomerInsuranceResource::collection($expiringPolicies->items()),
                'pagination' => [
                    'current_page' => $expiringPolicies->currentPage(),
                    'last_page' => $expiringPolicies->lastPage(),
                    'per_page' => $expiringPolicies->perPage(),
                    'total' => $expiringPolicies->total(),
                ],
                'days_threshold' => $days,
            ], 'Expiring policies retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function renewPolicy(Request $request, CustomerInsurance $customerInsurance): JsonResponse
    {
        $request->validate([
            'new_start_date' => 'required|date|after_or_equal:' . $customerInsurance->expired_date,
            'new_expired_date' => 'required|date|after:new_start_date',
            'new_policy_number' => 'required|string|max:100|unique:customer_insurances,policy_number',
            'new_premium_amount' => 'required|numeric|min:0',
            'renewal_remarks' => 'nullable|string',
        ]);
        
        try {
            $renewedPolicy = $this->customerInsuranceService->renewPolicy($customerInsurance, $request->validated());
            
            return $this->successResponse(
                new CustomerInsuranceResource($renewedPolicy),
                'Policy renewed successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
}