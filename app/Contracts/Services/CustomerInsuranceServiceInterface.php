<?php

namespace App\Contracts\Services;

use App\Models\CustomerInsurance;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerInsuranceServiceInterface
{
    public function getCustomerInsurances(Request $request): LengthAwarePaginator;
    
    public function createCustomerInsurance(array $data): CustomerInsurance;
    
    public function updateCustomerInsurance(CustomerInsurance $customerInsurance, array $data): CustomerInsurance;
    
    public function deleteCustomerInsurance(CustomerInsurance $customerInsurance): bool;
    
    public function updateStatus(int $customerInsuranceId, int $status): bool;
    
    public function renewPolicy(CustomerInsurance $customerInsurance, array $data): CustomerInsurance;
    
    public function exportCustomerInsurances(): \Symfony\Component\HttpFoundation\BinaryFileResponse;
    
    public function getCustomerPolicies(int $customerId): \Illuminate\Database\Eloquent\Collection;
    
    public function sendPolicyWhatsApp(int $customerInsuranceId, string $whatsappNumber): array;
    
    public function getExpiringPolicies(int $days = 30): \Illuminate\Database\Eloquent\Collection;
    
    public function calculateCommissionBreakdown(CustomerInsurance $customerInsurance): array;
}