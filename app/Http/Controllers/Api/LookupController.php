<?php

namespace App\Http\Controllers\Api;

use App\Models\PolicyType;
use App\Models\FuelType;
use App\Models\Branch;
use App\Models\PremiumType;
use App\Models\AddonCover;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookupController extends BaseApiController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    
    public function getPolicyTypes(): JsonResponse
    {
        try {
            $policyTypes = PolicyType::where('status', 1)
                ->select('id', 'type', 'description', 'status')
                ->orderBy('type')
                ->get();
            
            return $this->successResponse($policyTypes, 'Policy types retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getFuelTypes(): JsonResponse
    {
        try {
            $fuelTypes = FuelType::where('status', 1)
                ->select('id', 'type', 'description', 'status')
                ->orderBy('type')
                ->get();
            
            return $this->successResponse($fuelTypes, 'Fuel types retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getBranches(): JsonResponse
    {
        try {
            $branches = Branch::where('status', 1)
                ->select('id', 'name', 'code', 'address', 'contact_number', 'status')
                ->orderBy('name')
                ->get();
            
            return $this->successResponse($branches, 'Branches retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getPremiumTypes(): JsonResponse
    {
        try {
            $premiumTypes = PremiumType::where('status', 1)
                ->select('id', 'type', 'description', 'percentage', 'status')
                ->orderBy('type')
                ->get();
            
            return $this->successResponse($premiumTypes, 'Premium types retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getAddonCovers(Request $request): JsonResponse
    {
        $request->validate([
            'policy_type_id' => 'nullable|integer|exists:policy_types,id',
        ]);
        
        try {
            $query = AddonCover::where('status', 1)
                ->select('id', 'name', 'description', 'premium_amount', 'policy_type_id', 'status');
            
            if ($request->has('policy_type_id')) {
                $query->where('policy_type_id', $request->policy_type_id);
            }
            
            $addonCovers = $query->orderBy('name')->get();
            
            return $this->successResponse($addonCovers, 'Addon covers retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getCustomerTypes(): JsonResponse
    {
        try {
            $customerTypes = [
                ['value' => 'Corporate', 'label' => 'Corporate'],
                ['value' => 'Retail', 'label' => 'Retail'],
            ];
            
            return $this->successResponse($customerTypes, 'Customer types retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getQuotationStatuses(): JsonResponse
    {
        try {
            $statuses = [
                ['value' => 'Pending', 'label' => 'Pending', 'color' => '#ffc107'],
                ['value' => 'Confirmed', 'label' => 'Confirmed', 'color' => '#28a745'],
                ['value' => 'Cancelled', 'label' => 'Cancelled', 'color' => '#dc3545'],
            ];
            
            return $this->successResponse($statuses, 'Quotation statuses retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getCommissionTypes(): JsonResponse
    {
        try {
            $commissionTypes = [
                ['value' => 'Percentage', 'label' => 'Percentage'],
                ['value' => 'Fixed', 'label' => 'Fixed Amount'],
            ];
            
            return $this->successResponse($commissionTypes, 'Commission types retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getStatuses(): JsonResponse
    {
        try {
            $statuses = [
                ['value' => 0, 'label' => 'Inactive'],
                ['value' => 1, 'label' => 'Active'],
            ];
            
            return $this->successResponse($statuses, 'Statuses retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    
    public function getAllLookups(): JsonResponse
    {
        try {
            $lookups = [
                'policy_types' => PolicyType::where('status', 1)->select('id', 'type', 'description')->orderBy('type')->get(),
                'fuel_types' => FuelType::where('status', 1)->select('id', 'type', 'description')->orderBy('type')->get(),
                'branches' => Branch::where('status', 1)->select('id', 'name', 'code')->orderBy('name')->get(),
                'premium_types' => PremiumType::where('status', 1)->select('id', 'type', 'description', 'percentage')->orderBy('type')->get(),
                'addon_covers' => AddonCover::where('status', 1)->select('id', 'name', 'description', 'premium_amount', 'policy_type_id')->orderBy('name')->get(),
                'customer_types' => [
                    ['value' => 'Corporate', 'label' => 'Corporate'],
                    ['value' => 'Retail', 'label' => 'Retail'],
                ],
                'quotation_statuses' => [
                    ['value' => 'Pending', 'label' => 'Pending', 'color' => '#ffc107'],
                    ['value' => 'Confirmed', 'label' => 'Confirmed', 'color' => '#28a745'],
                    ['value' => 'Cancelled', 'label' => 'Cancelled', 'color' => '#dc3545'],
                ],
                'commission_types' => [
                    ['value' => 'Percentage', 'label' => 'Percentage'],
                    ['value' => 'Fixed', 'label' => 'Fixed Amount'],
                ],
                'statuses' => [
                    ['value' => 0, 'label' => 'Inactive'],
                    ['value' => 1, 'label' => 'Active'],
                ],
            ];
            
            return $this->successResponse($lookups, 'All lookup data retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
}