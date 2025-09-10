<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\QuotationServiceInterface;
use App\Http\Resources\QuotationResource;
use App\Models\Quotation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuotationController extends BaseApiController
{
    public function __construct(
        private QuotationServiceInterface $quotationService
    ) {
        $this->middleware('auth:sanctum');
    }
    
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
            'customer_id' => 'integer|exists:customers,id',
            'status' => 'string|in:Pending,Confirmed,Cancelled',
            'from_date' => 'date',
            'to_date' => 'date|after_or_equal:from_date',
        ]);
        
        try {
            $quotations = $this->quotationService->getQuotations($request);
            
            return $this->successResponse([
                'quotations' => QuotationResource::collection($quotations->items()),
                'pagination' => [
                    'current_page' => $quotations->currentPage(),
                    'last_page' => $quotations->lastPage(),
                    'per_page' => $quotations->perPage(),
                    'total' => $quotations->total(),
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
            'policy_type_id' => 'required|integer|exists:policy_types,id',
            'vehicle_number' => 'nullable|string|max:20',
            'vehicle_make' => 'nullable|string|max:100',
            'vehicle_model' => 'nullable|string|max:100',
            'vehicle_variant' => 'nullable|string|max:100',
            'manufacturing_year' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'registration_year' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'fuel_type_id' => 'nullable|integer|exists:fuel_types,id',
            'rto_code' => 'nullable|string|max:10',
            'cc' => 'nullable|integer|min:50|max:10000',
            'seating_capacity' => 'nullable|integer|min:1|max:100',
            'ncb_percentage' => 'nullable|numeric|min:0|max:100',
            'vehicle_age' => 'nullable|integer|min:0|max:50',
            'remarks' => 'nullable|string',
        ]);
        
        try {
            $quotation = $this->quotationService->createQuotation($request->validated());
            
            return $this->successResponse(
                new QuotationResource($quotation),
                'Quotation created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function show(Quotation $quotation): JsonResponse
    {
        try {
            $quotationData = $this->quotationService->getQuotationWithCompanies($quotation->id);
            
            return $this->successResponse(new QuotationResource($quotationData));
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function update(Request $request, Quotation $quotation): JsonResponse
    {
        $request->validate([
            'customer_id' => 'sometimes|required|integer|exists:customers,id',
            'policy_type_id' => 'sometimes|required|integer|exists:policy_types,id',
            'vehicle_number' => 'nullable|string|max:20',
            'vehicle_make' => 'nullable|string|max:100',
            'vehicle_model' => 'nullable|string|max:100',
            'vehicle_variant' => 'nullable|string|max:100',
            'manufacturing_year' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'registration_year' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'fuel_type_id' => 'nullable|integer|exists:fuel_types,id',
            'rto_code' => 'nullable|string|max:10',
            'cc' => 'nullable|integer|min:50|max:10000',
            'seating_capacity' => 'nullable|integer|min:1|max:100',
            'ncb_percentage' => 'nullable|numeric|min:0|max:100',
            'vehicle_age' => 'nullable|integer|min:0|max:50',
            'remarks' => 'nullable|string',
        ]);
        
        try {
            $updatedQuotation = $this->quotationService->updateQuotation($quotation, $request->validated());
            
            return $this->successResponse(
                new QuotationResource($updatedQuotation),
                'Quotation updated successfully'
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function destroy(Quotation $quotation): JsonResponse
    {
        try {
            $this->quotationService->deleteQuotation($quotation);
            
            return $this->successResponse(null, 'Quotation deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function updateStatus(Request $request, Quotation $quotation): JsonResponse
    {
        $request->validate([
            'status' => ['required', Rule::in(['Pending', 'Confirmed', 'Cancelled'])],
            'remarks' => 'nullable|string',
        ]);
        
        try {
            $this->quotationService->updateQuotationStatus($quotation->id, $request->status, $request->remarks);
            
            return $this->successResponse(null, 'Quotation status updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function duplicate(Quotation $quotation): JsonResponse
    {
        try {
            $duplicatedQuotation = $this->quotationService->duplicateQuotation($quotation);
            
            return $this->successResponse(
                new QuotationResource($duplicatedQuotation),
                'Quotation duplicated successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getComparison(Quotation $quotation): JsonResponse
    {
        try {
            $comparison = $this->quotationService->getQuotationComparison($quotation->id);
            
            return $this->successResponse($comparison, 'Quotation comparison retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
}