<?php

namespace App\Services;

use App\Contracts\Services\CustomerInsuranceServiceInterface;
use App\Contracts\Repositories\CustomerInsuranceRepositoryInterface;
use App\Exports\CustomerInsurancesExport;
use App\Models\CustomerInsurance;
use App\Traits\WhatsAppApiTrait;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CustomerInsuranceService implements CustomerInsuranceServiceInterface
{
    use WhatsAppApiTrait;
    
    public function __construct(
        private CustomerInsuranceRepositoryInterface $customerInsuranceRepository
    ) {}
    
    public function getCustomerInsurances(Request $request): LengthAwarePaginator
    {
        return $this->customerInsuranceRepository->getPaginated($request);
    }
    
    public function createCustomerInsurance(array $data): CustomerInsurance
    {
        DB::beginTransaction();
        try {
            // Calculate commission breakdown
            $data = $this->calculateCommissionFields($data);
            
            $customerInsurance = $this->customerInsuranceRepository->create($data);
            
            // Handle policy document upload if present
            if (isset($data['policy_document']) && $data['policy_document']) {
                $this->handlePolicyDocument($customerInsurance, $data['policy_document']);
            }
            
            DB::commit();
            return $customerInsurance;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function updateCustomerInsurance(CustomerInsurance $customerInsurance, array $data): CustomerInsurance
    {
        DB::beginTransaction();
        try {
            // Calculate commission breakdown
            $data = $this->calculateCommissionFields($data);
            
            $updatedCustomerInsurance = $this->customerInsuranceRepository->update($customerInsurance, $data);
            
            // Handle policy document upload if present
            if (isset($data['policy_document']) && $data['policy_document']) {
                $this->handlePolicyDocument($updatedCustomerInsurance, $data['policy_document']);
            }
            
            DB::commit();
            return $updatedCustomerInsurance;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function deleteCustomerInsurance(CustomerInsurance $customerInsurance): bool
    {
        DB::beginTransaction();
        try {
            // Delete policy document if exists
            if ($customerInsurance->policy_document_path && Storage::exists($customerInsurance->policy_document_path)) {
                Storage::delete($customerInsurance->policy_document_path);
            }
            
            $result = $this->customerInsuranceRepository->delete($customerInsurance);
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function updateStatus(int $customerInsuranceId, int $status): bool
    {
        DB::beginTransaction();
        try {
            $result = $this->customerInsuranceRepository->updateStatus($customerInsuranceId, $status);
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function renewPolicy(CustomerInsurance $customerInsurance, array $data): CustomerInsurance
    {
        DB::beginTransaction();
        try {
            // Create new policy record for renewal
            $renewalData = $this->prepareRenewalData($customerInsurance, $data);
            $newPolicy = $this->customerInsuranceRepository->create($renewalData);
            
            // Mark original policy as renewed
            $this->customerInsuranceRepository->update($customerInsurance, [
                'is_renewed' => true,
                'renewed_date' => now(),
                'new_insurance_id' => $newPolicy->id
            ]);
            
            DB::commit();
            return $newPolicy;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function exportCustomerInsurances(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new CustomerInsurancesExport, 'customer_insurances.xlsx');
    }
    
    public function getCustomerPolicies(int $customerId): Collection
    {
        return $this->customerInsuranceRepository->getByCustomerId($customerId);
    }
    
    public function sendPolicyWhatsApp(int $customerInsuranceId, string $whatsappNumber): array
    {
        $customerInsurance = $this->customerInsuranceRepository->findWithRelations($customerInsuranceId);
        
        if (!$customerInsurance) {
            throw new \Exception('Customer Insurance not found');
        }
        
        if (!$customerInsurance->policy_document_path || !Storage::exists($customerInsurance->policy_document_path)) {
            throw new \Exception('Policy document not found');
        }
        
        $documentPath = Storage::path($customerInsurance->policy_document_path);
        $message = "Dear {$customerInsurance->customer->name}, Please find your policy document for Policy No: {$customerInsurance->policy_no}";
        
        return $this->sendWhatsAppDocument($whatsappNumber, $documentPath, $message);
    }
    
    public function getExpiringPolicies(int $days = 30): Collection
    {
        return $this->customerInsuranceRepository->getExpiringPolicies($days);
    }
    
    public function calculateCommissionBreakdown(CustomerInsurance $customerInsurance): array
    {
        $basePremium = match($customerInsurance->commission_on) {
            'net_premium' => $customerInsurance->net_premium ?? 0,
            'od_premium' => $customerInsurance->od_premium ?? 0,
            'tp_premium' => $customerInsurance->tp_premium ?? 0,
            default => $customerInsurance->net_premium ?? 0
        };
        
        $myCommission = ($basePremium * ($customerInsurance->my_commission_percentage ?? 0)) / 100;
        $transferCommission = ($basePremium * ($customerInsurance->transfer_commission_percentage ?? 0)) / 100;
        $referenceCommission = ($basePremium * ($customerInsurance->reference_commission_percentage ?? 0)) / 100;
        $actualEarnings = $myCommission - $transferCommission - $referenceCommission;
        
        return [
            'base_premium' => $basePremium,
            'my_commission' => $myCommission,
            'transfer_commission' => $transferCommission,
            'reference_commission' => $referenceCommission,
            'actual_earnings' => $actualEarnings
        ];
    }
    
    private function calculateCommissionFields(array $data): array
    {
        if (!isset($data['commission_on'])) {
            return $data;
        }
        
        $basePremium = match($data['commission_on']) {
            'net_premium' => $data['net_premium'] ?? 0,
            'od_premium' => $data['od_premium'] ?? 0,
            'tp_premium' => $data['tp_premium'] ?? 0,
            default => $data['net_premium'] ?? 0
        };
        
        $data['my_commission_amount'] = ($basePremium * ($data['my_commission_percentage'] ?? 0)) / 100;
        $data['transfer_commission_amount'] = ($basePremium * ($data['transfer_commission_percentage'] ?? 0)) / 100;
        $data['reference_commission_amount'] = ($basePremium * ($data['reference_commission_percentage'] ?? 0)) / 100;
        $data['actual_earnings'] = $data['my_commission_amount'] - $data['transfer_commission_amount'] - $data['reference_commission_amount'];
        
        return $data;
    }
    
    private function handlePolicyDocument(CustomerInsurance $customerInsurance, $document): void
    {
        $path = $document->store('policy-documents');
        $customerInsurance->update(['policy_document_path' => $path]);
    }
    
    private function prepareRenewalData(CustomerInsurance $originalPolicy, array $renewalData): array
    {
        return array_merge($originalPolicy->toArray(), [
            'id' => null,
            'start_date' => $renewalData['start_date'],
            'expired_date' => $renewalData['expired_date'],
            'tp_expiry_date' => $renewalData['tp_expiry_date'] ?? null,
            'premium_amount' => $renewalData['premium_amount'],
            'net_premium' => $renewalData['net_premium'],
            'od_premium' => $renewalData['od_premium'] ?? 0,
            'tp_premium' => $renewalData['tp_premium'] ?? 0,
            'gst' => $renewalData['gst'] ?? 0,
            'final_premium_with_gst' => $renewalData['final_premium_with_gst'],
            'is_renewed' => false,
            'renewed_date' => null,
            'new_insurance_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}