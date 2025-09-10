<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\InsuranceCompanyServiceInterface;
use App\Http\Resources\InsuranceCompanyResource;
use App\Models\InsuranceCompany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InsuranceCompanyController extends BaseApiController
{
    public function __construct(
        private InsuranceCompanyServiceInterface $insuranceCompanyService
    ) {
        $this->middleware('auth:sanctum');
    }
    
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
            'status' => 'integer|in:0,1',
            'search' => 'string|max:100',
        ]);
        
        try {
            $companies = $this->insuranceCompanyService->getInsuranceCompanies($request);
            
            return $this->successResponse([
                'insurance_companies' => InsuranceCompanyResource::collection($companies->items()),
                'pagination' => [
                    'current_page' => $companies->currentPage(),
                    'last_page' => $companies->lastPage(),
                    'per_page' => $companies->perPage(),
                    'total' => $companies->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:insurance_companies,name',
            'company_code' => 'required|string|max:50|unique:insurance_companies,company_code',
            'email' => 'nullable|email|unique:insurance_companies,email',
            'contact_number' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:15',
            'pan_number' => 'nullable|string|max:10',
            'status' => 'integer|in:0,1',
        ]);
        
        try {
            $company = $this->insuranceCompanyService->createInsuranceCompany($request->validated());
            
            return $this->successResponse(
                new InsuranceCompanyResource($company),
                'Insurance company created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function show(InsuranceCompany $insuranceCompany): JsonResponse
    {
        try {
            return $this->successResponse(new InsuranceCompanyResource($insuranceCompany));
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function update(Request $request, InsuranceCompany $insuranceCompany): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:insurance_companies,name,' . $insuranceCompany->id,
            'company_code' => 'sometimes|required|string|max:50|unique:insurance_companies,company_code,' . $insuranceCompany->id,
            'email' => 'nullable|email|unique:insurance_companies,email,' . $insuranceCompany->id,
            'contact_number' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:15',
            'pan_number' => 'nullable|string|max:10',
            'status' => 'integer|in:0,1',
        ]);
        
        try {
            $updatedCompany = $this->insuranceCompanyService->updateInsuranceCompany($insuranceCompany, $request->validated());
            
            return $this->successResponse(
                new InsuranceCompanyResource($updatedCompany),
                'Insurance company updated successfully'
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function destroy(InsuranceCompany $insuranceCompany): JsonResponse
    {
        try {
            $this->insuranceCompanyService->deleteInsuranceCompany($insuranceCompany);
            
            return $this->successResponse(null, 'Insurance company deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function updateStatus(Request $request, InsuranceCompany $insuranceCompany): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:0,1',
        ]);
        
        try {
            $this->insuranceCompanyService->updateInsuranceCompanyStatus($insuranceCompany->id, $request->status);
            
            return $this->successResponse(null, 'Insurance company status updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getActiveCompanies(): JsonResponse
    {
        try {
            $activeCompanies = $this->insuranceCompanyService->getActiveInsuranceCompanies();
            
            return $this->successResponse(
                InsuranceCompanyResource::collection($activeCompanies),
                'Active insurance companies retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getCompanyStatistics(InsuranceCompany $insuranceCompany): JsonResponse
    {
        try {
            $statistics = $this->insuranceCompanyService->getCompanyStatistics($insuranceCompany->id);
            
            return $this->successResponse($statistics, 'Company statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
}