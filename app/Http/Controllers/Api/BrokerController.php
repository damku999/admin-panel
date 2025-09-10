<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\BrokerServiceInterface;
use App\Http\Resources\BrokerResource;
use App\Models\Broker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrokerController extends BaseApiController
{
    public function __construct(
        private BrokerServiceInterface $brokerService
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
            $brokers = $this->brokerService->getBrokers($request);
            
            return $this->successResponse([
                'brokers' => BrokerResource::collection($brokers->items()),
                'pagination' => [
                    'current_page' => $brokers->currentPage(),
                    'last_page' => $brokers->lastPage(),
                    'per_page' => $brokers->perPage(),
                    'total' => $brokers->total(),
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
            'email' => 'nullable|email|unique:brokers,email',
            'mobile_number' => 'nullable|string|max:15',
            'license_number' => 'required|string|max:100|unique:brokers,license_number',
            'license_expiry_date' => 'nullable|date|after:today',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:15',
            'pan_number' => 'nullable|string|max:10',
            'status' => 'integer|in:0,1',
        ]);
        
        try {
            $broker = $this->brokerService->createBroker($request->validated());
            
            return $this->successResponse(
                new BrokerResource($broker),
                'Broker created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function show(Broker $broker): JsonResponse
    {
        try {
            return $this->successResponse(new BrokerResource($broker));
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function update(Request $request, Broker $broker): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|unique:brokers,email,' . $broker->id,
            'mobile_number' => 'nullable|string|max:15',
            'license_number' => 'sometimes|required|string|max:100|unique:brokers,license_number,' . $broker->id,
            'license_expiry_date' => 'nullable|date|after:today',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:15',
            'pan_number' => 'nullable|string|max:10',
            'status' => 'integer|in:0,1',
        ]);
        
        try {
            $updatedBroker = $this->brokerService->updateBroker($broker, $request->validated());
            
            return $this->successResponse(
                new BrokerResource($updatedBroker),
                'Broker updated successfully'
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function destroy(Broker $broker): JsonResponse
    {
        try {
            $this->brokerService->deleteBroker($broker);
            
            return $this->successResponse(null, 'Broker deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function updateStatus(Request $request, Broker $broker): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:0,1',
        ]);
        
        try {
            $this->brokerService->updateBrokerStatus($broker->id, $request->status);
            
            return $this->successResponse(null, 'Broker status updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getActiveBrokers(): JsonResponse
    {
        try {
            $activeBrokers = $this->brokerService->getActiveBrokers();
            
            return $this->successResponse(
                BrokerResource::collection($activeBrokers),
                'Active brokers retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getBrokerCommissions(Broker $broker, Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
        ]);
        
        try {
            $commissions = $this->brokerService->getBrokerCommissions($broker->id, $request);
            
            return $this->successResponse($commissions, 'Broker commissions retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getBrokerStatistics(Broker $broker, Request $request): JsonResponse
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);
        
        try {
            $statistics = $this->brokerService->getBrokerStatistics($broker->id, $request);
            
            return $this->successResponse($statistics, 'Broker statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
}