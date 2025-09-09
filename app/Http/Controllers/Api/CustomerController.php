<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\CustomerServiceInterface;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends BaseApiController
{
    public function __construct(
        private CustomerServiceInterface $customerService
    ) {
        $this->middleware('auth:sanctum');
    }
    
    public function index(Request $request): JsonResponse
    {
        try {
            $customers = $this->customerService->getCustomers($request);
            return $this->successResponse([
                'customers' => CustomerResource::collection($customers->items()),
                'pagination' => [
                    'current_page' => $customers->currentPage(),
                    'last_page' => $customers->lastPage(),
                    'per_page' => $customers->perPage(),
                    'total' => $customers->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'mobile_number' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date',
            'type' => 'nullable|in:Corporate,Retail',
        ]);
        
        try {
            $customer = $this->customerService->createCustomer($request->validated());
            return $this->successResponse(
                new CustomerResource($customer),
                'Customer created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function show(Customer $customer): JsonResponse
    {
        try {
            return $this->successResponse(new CustomerResource($customer));
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function update(Request $request, Customer $customer): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'mobile_number' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date',
            'type' => 'nullable|in:Corporate,Retail',
        ]);
        
        try {
            $updatedCustomer = $this->customerService->updateCustomer($customer, $request->validated());
            return $this->successResponse(
                new CustomerResource($updatedCustomer),
                'Customer updated successfully'
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function destroy(Customer $customer): JsonResponse
    {
        try {
            $this->customerService->deleteCustomer($customer);
            return $this->successResponse(null, 'Customer deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function updateStatus(Request $request, Customer $customer): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:0,1',
        ]);
        
        try {
            $this->customerService->updateCustomerStatus($customer->id, $request->status);
            return $this->successResponse(null, 'Customer status updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
}