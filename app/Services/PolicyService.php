<?php

namespace App\Services;

use App\Contracts\Repositories\PolicyRepositoryInterface;
use App\Contracts\Services\PolicyServiceInterface;
use App\Models\Customer;
use App\Models\CustomerInsurance;
use App\Traits\WhatsAppApiTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PolicyService extends BaseService implements PolicyServiceInterface
{
    use WhatsAppApiTrait;

    public function __construct(
        private PolicyRepositoryInterface $policyRepository
    ) {
    }

    public function getPolicies(Request $request): LengthAwarePaginator
    {
        $filters = [
            'search' => $request->input('search'),
            'customer_id' => $request->input('customer_id'),
            'insurance_company_id' => $request->input('insurance_company_id'),
            'policy_type_id' => $request->input('policy_type_id'),
            'status' => $request->input('status'),
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
        ];

        return $this->policyRepository->getPaginated($filters, 10);
    }

    public function createPolicy(array $data): CustomerInsurance
    {
        return $this->createInTransaction(
            fn() => $this->policyRepository->create($data)
        );
    }

    public function updatePolicy(CustomerInsurance $policy, array $data): bool
    {
        return $this->updateInTransaction(
            fn() => $this->policyRepository->update($policy, $data)
        );
    }

    public function getCustomerPolicies(Customer $customer): Collection
    {
        return $this->policyRepository->getByCustomer($customer->id);
    }

    public function getPoliciesDueForRenewal(int $daysAhead = 30): Collection
    {
        return $this->policyRepository->getDueForRenewal($daysAhead);
    }

    public function sendRenewalReminder(CustomerInsurance $policy): bool
    {
        try {
            $message = $this->generateRenewalReminderMessage($policy);
            $result = $this->whatsAppSendMessage($message, $policy->customer->mobile_number);
            
            if ($result) {
                // Log successful reminder
                Log::info('Renewal reminder sent successfully', [
                    'policy_id' => $policy->id,
                    'customer_id' => $policy->customer_id,
                    'policy_number' => $policy->policy_number
                ]);
            }
            
            return $result;
        } catch (\Throwable $th) {
            Log::error('Failed to send renewal reminder', [
                'policy_id' => $policy->id,
                'customer_id' => $policy->customer_id,
                'error' => $th->getMessage()
            ]);
            
            return false;
        }
    }

    public function getFamilyPolicies(Customer $customer): Collection
    {
        if (!$customer->hasFamily()) {
            return collect([]);
        }

        if ($customer->isFamilyHead()) {
            return $this->policyRepository->getByFamilyGroup($customer->family_group_id);
        }

        // Non-family head customers can only see their own policies
        return $this->getCustomerPolicies($customer);
    }

    public function canCustomerViewPolicy(Customer $customer, CustomerInsurance $policy): bool
    {
        // Customer can view their own policy
        if ($policy->customer_id === $customer->id) {
            return true;
        }

        // Family head can view family member policies
        if ($customer->isFamilyHead() && $customer->hasFamily()) {
            $policyCustomer = $policy->customer;
            return $policyCustomer->family_group_id === $customer->family_group_id;
        }

        return false;
    }

    public function getPolicyStatistics(): array
    {
        return $this->policyRepository->getStatistics();
    }

    /**
     * Get policies by insurance company.
     */
    public function getPoliciesByCompany(int $companyId): Collection
    {
        return $this->policyRepository->getByInsuranceCompany($companyId);
    }

    /**
     * Get active policies.
     */
    public function getActivePolicies(): Collection
    {
        return $this->policyRepository->getActive();
    }

    /**
     * Get expired policies.
     */
    public function getExpiredPolicies(): Collection
    {
        return $this->policyRepository->getExpired();
    }

    /**
     * Get policies by type.
     */
    public function getPoliciesByType(int $policyTypeId): Collection
    {
        return $this->policyRepository->getByPolicyType($policyTypeId);
    }

    /**
     * Search policies.
     */
    public function searchPolicies(string $query): Collection
    {
        return $this->policyRepository->search($query);
    }

    /**
     * Delete policy.
     */
    public function deletePolicy(CustomerInsurance $policy): bool
    {
        return $this->deleteInTransaction(
            fn() => $this->policyRepository->delete($policy)
        );
    }

    /**
     * Update policy status.
     */
    public function updatePolicyStatus(CustomerInsurance $policy, int $status): bool
    {
        return $this->updateInTransaction(
            fn() => $this->policyRepository->update($policy, ['status' => $status])
        );
    }

    /**
     * Get policy count by status.
     */
    public function getPolicyCountByStatus(): array
    {
        return $this->policyRepository->getCountByStatus();
    }

    /**
     * Check if policy exists.
     */
    public function policyExists(int $policyId): bool
    {
        return $this->policyRepository->exists($policyId);
    }

    /**
     * Get policies for renewal processing.
     */
    public function getPoliciesForRenewalProcessing(): Collection
    {
        // Get policies due for renewal in next 7 days for priority processing
        return $this->getPoliciesDueForRenewal(7);
    }

    /**
     * Send bulk renewal reminders.
     */
    public function sendBulkRenewalReminders(?int $daysAhead = null): array
    {
        $daysAhead = $daysAhead ?? 30;
        $policies = $this->getPoliciesDueForRenewal($daysAhead);
        
        $results = [
            'total' => $policies->count(),
            'sent' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($policies as $policy) {
            $sent = $this->sendRenewalReminder($policy);
            
            if ($sent) {
                $results['sent']++;
            } else {
                $results['failed']++;
                $results['errors'][] = [
                    'policy_id' => $policy->id,
                    'policy_number' => $policy->policy_number,
                    'customer_name' => $policy->customer->name
                ];
            }
        }

        return $results;
    }

    /**
     * Generate renewal reminder message.
     */
    private function generateRenewalReminderMessage(CustomerInsurance $policy): string
    {
        $customer = $policy->customer;
        $daysRemaining = now()->diffInDays($policy->policy_end_date);
        
        $message = "🔔 *Policy Renewal Reminder*\n\n";
        $message .= "Dear *{$customer->name}*,\n\n";
        $message .= "Your insurance policy is due for renewal:\n\n";
        $message .= "📋 *Policy Details:*\n";
        $message .= "• Policy No: *{$policy->policy_number}*\n";
        $message .= "• Company: *{$policy->insuranceCompany->name}*\n";
        $message .= "• Type: *{$policy->policyType->name}*\n";
        $message .= "• End Date: *{$policy->policy_end_date->format('d M Y')}*\n";
        $message .= "• Days Remaining: *{$daysRemaining} days*\n\n";
        
        if ($daysRemaining <= 7) {
            $message .= "⚠️ *URGENT: Your policy expires in {$daysRemaining} days!*\n\n";
        }
        
        $message .= "📞 Please contact us to renew your policy and avoid any lapse in coverage.\n\n";
        $message .= "Best regards,\n";
        $message .= "Parth Rawal\n";
        $message .= "https://parthrawal.in\n";
        $message .= "Your Trusted Insurance Advisor\n";
        $message .= "\"Think of Insurance, Think of Us.\"";

        return $message;
    }
}