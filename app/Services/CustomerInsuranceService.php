<?php

namespace App\Services;

use App\Contracts\Services\CustomerInsuranceServiceInterface;
use App\Contracts\Repositories\CustomerInsuranceRepositoryInterface;
use App\Exports\CustomerInsurancesExport;
use App\Models\{Branch, Broker, Customer, CustomerInsurance, FuelType, InsuranceCompany, PolicyType, PremiumType, ReferenceUser, RelationshipManager};
use App\Traits\WhatsAppApiTrait;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\{Carbon, Facades\DB, Facades\Storage};
use Maatwebsite\Excel\Facades\Excel;

class CustomerInsuranceService extends BaseService implements CustomerInsuranceServiceInterface
{
    use WhatsAppApiTrait;
    
    public function __construct(
        private CustomerInsuranceRepositoryInterface $customerInsuranceRepository,
        private CacheService $cacheService
    ) {}
    
    public function getCustomerInsurances(Request $request): LengthAwarePaginator
    {
        $query = CustomerInsurance::select([
            'customer_insurances.*', 
            'customers.name as customer_name', 
            'branches.name as branch_name', 
            'brokers.name as broker_name', 
            'relationship_managers.name as relationship_manager_name', 
            'premium_types.name AS policy_type_name'
        ])
        ->join('customers', 'customers.id', 'customer_insurances.customer_id')
        ->leftJoin('branches', 'branches.id', 'customer_insurances.branch_id')
        ->leftJoin('premium_types', 'premium_types.id', 'customer_insurances.premium_type_id')
        ->leftJoin('brokers', 'brokers.id', 'customer_insurances.broker_id')
        ->leftJoin('relationship_managers', 'relationship_managers.id', 'customer_insurances.relationship_manager_id');

        // Apply search filter
        if (!empty($request->search)) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('registration_no', 'LIKE', '%' . $search . '%')
                  ->orWhere('policy_no', 'LIKE', '%' . $search . '%')
                  ->orWhere('customers.name', 'LIKE', '%' . $search . '%')
                  ->orWhere('customers.mobile_number', 'LIKE', '%' . $search . '%');
            });
        }

        // Status filter - default to active (unless filtering by renewal due dates)
        if (!$request->filled('renewal_due_start') && !$request->filled('renewal_due_end')) {
            $query->where('customer_insurances.status', 1);
        }
        
        // Apply explicit status filter if provided
        if ($request->filled('status')) {
            $query->where('customer_insurances.status', $request->input('status'));
        }

        // Customer filter
        if (!empty($request->customer_id)) {
            $query->where('customer_insurances.customer_id', $request->customer_id);
        }

        // Renewal filters
        if (!empty($request->already_renewed_this_month)) {
            $query->where('customer_insurances.is_renewed', 1);
        }
        if (!empty($request->pending_renewal_this_month)) {
            $query->where('customer_insurances.is_renewed', 0);
        }

        // Date range filter for expiring policies
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
            $query->whereBetween('expired_date', [$start_date, $end_date]);
        }
        
        // Renewal due date filter (from dashboard)
        if ($request->filled('renewal_due_start') && $request->filled('renewal_due_end')) {
            $renewal_start = Carbon::createFromFormat('d-m-Y', $request->input('renewal_due_start'))->startOfDay();
            $renewal_end = Carbon::createFromFormat('d-m-Y', $request->input('renewal_due_end'))->endOfDay();
            $query->whereBetween('expired_date', [$renewal_start, $renewal_end]);
        }

        // Sorting
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($sort, $direction);

        return $query->paginate(config('app.pagination_default', 15));
    }
    
    public function getFormData(): array
    {
        return [
            'customers' => Customer::select('id', 'name')->get(),
            'brokers' => Broker::select('id', 'name')->get(),
            'relationship_managers' => RelationshipManager::select('id', 'name')->get(),
            'branches' => Branch::select('id', 'name')->get(),
            'insurance_companies' => InsuranceCompany::select('id', 'name')->get(),
            'policy_type' => PolicyType::select('id', 'name')->get(),
            'fuel_type' => FuelType::select('id', 'name')->get(),
            'premium_types' => PremiumType::select('id', 'name', 'is_vehicle', 'is_life_insurance_policies')->get(),
            'reference_by_user' => ReferenceUser::select('id', 'name')->get(),
            'life_insurance_payment_mode' => config('constants.LIFE_INSURANCE_PAYMENT_MODE'),
        ];
    }

    public function getStoreValidationRules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'broker_id' => 'required|exists:brokers,id',
            'relationship_manager_id' => 'required|exists:relationship_managers,id',
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'policy_type_id' => 'required|exists:policy_types,id',
            'fuel_type_id' => 'nullable|exists:fuel_types,id',
            'premium_type_id' => 'required|exists:premium_types,id',
            'issue_date' => 'required|date_format:d/m/Y',
            'expired_date' => 'required|date_format:d/m/Y',
            'start_date' => 'required|date_format:d/m/Y',
            'tp_expiry_date' => 'nullable|date_format:d/m/Y',
            'maturity_date' => 'nullable|date_format:d/m/Y',
            'policy_no' => 'required',
            'net_premium' => 'nullable|numeric|min:0',
            'premium_amount' => 'nullable|numeric|min:0',
            'gst' => 'nullable|numeric|min:0',
            'final_premium_with_gst' => 'required|numeric|min:0',
            'mode_of_payment' => 'nullable|string',
            'cheque_no' => 'nullable|string',
            'rto' => 'nullable|string',
            'registration_no' => 'nullable|string',
            'make_model' => 'nullable|string',
            'od_premium' => 'nullable|numeric|min:0',
            'tp_premium' => 'nullable|numeric|min:0',
            'cgst1' => 'required|numeric|min:0',
            'sgst1' => 'required|numeric|min:0',
            'cgst2' => 'nullable|numeric|min:0',
            'sgst2' => 'nullable|numeric|min:0',
            'commission_on' => 'nullable|in:net_premium,od_premium,tp_premium',
            'my_commission_percentage' => 'nullable|numeric',
            'my_commission_amount' => 'nullable|numeric',
            'transfer_commission_percentage' => 'nullable|numeric',
            'transfer_commission_amount' => 'nullable|numeric',
            'reference_commission_percentage' => 'nullable|numeric',
            'reference_commission_amount' => 'nullable|numeric',
            'actual_earnings' => 'nullable|numeric',
            'ncb_percentage' => 'nullable|numeric',
            'gross_vehicle_weight' => 'nullable|numeric',
            'mfg_year' => 'nullable|numeric',
            'plan_name' => 'nullable|string',
            'premium_paying_term' => 'nullable|string',
            'policy_term' => 'nullable|string',
            'sum_insured' => 'nullable|string',
            'pension_amount_yearly' => 'nullable|string',
            'approx_maturity_amount' => 'nullable|string',
            'remarks' => 'nullable|string',
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'customer_id',
            'branch_id',
            'broker_id',
            'relationship_manager_id',
            'insurance_company_id',
            'premium_type_id',
            'policy_type_id',
            'fuel_type_id',
            'issue_date',
            'expired_date',
            'start_date',
            'tp_expiry_date',
            'policy_no',
            'net_premium',
            'gst',
            'final_premium_with_gst',
            'mode_of_payment',
            'cheque_no',
            'rto',
            'registration_no',
            'make_model',
            'od_premium',
            'premium_amount',
            'tp_premium',
            'cgst1',
            'sgst1',
            'cgst2',
            'sgst2',
            'commission_on',
            'my_commission_percentage',
            'my_commission_amount',
            'transfer_commission_percentage',
            'transfer_commission_amount',
            'actual_earnings',
            'ncb_percentage',
            'gross_vehicle_weight',
            'mfg_year',
            'reference_commission_percentage',
            'reference_commission_amount',
            'plan_name',
            'premium_paying_term',
            'policy_term',
            'sum_insured',
            'pension_amount_yearly',
            'approx_maturity_amount',
            'remarks',
            'maturity_date',
            'life_insurance_payment_mode',
            'reference_by',
        ];
    }

    public function getRenewalValidationRules(): array
    {
        return $this->getStoreValidationRules();
    }

    public function prepareStorageData(Request $request): array
    {
        $data_to_store = $request->only([
            'customer_id',
            'branch_id',
            'broker_id',
            'relationship_manager_id',
            'insurance_company_id',
            'premium_type_id',
            'policy_type_id',
            'fuel_type_id',
            'policy_no',
            'net_premium',
            'gst',
            'final_premium_with_gst',
            'mode_of_payment',
            'cheque_no',
            'rto',
            'registration_no',
            'make_model',
            'od_premium',
            'premium_amount',
            'tp_premium',
            'cgst1',
            'sgst1',
            'cgst2',
            'sgst2',
            'commission_on',
            'my_commission_percentage',
            'my_commission_amount',
            'transfer_commission_percentage',
            'transfer_commission_amount',
            'actual_earnings',
            'ncb_percentage',
            'gross_vehicle_weight',
            'mfg_year',
            'reference_commission_percentage',
            'reference_commission_amount',
            'plan_name',
            'premium_paying_term',
            'policy_term',
            'sum_insured',
            'pension_amount_yearly',
            'approx_maturity_amount',
            'remarks',
            'life_insurance_payment_mode',
            'reference_by',
        ]);

        // Handle date fields
        $dateFields = ['issue_date', 'expired_date', 'start_date', 'tp_expiry_date', 'maturity_date'];
        foreach ($dateFields as $field) {
            if (!empty($request->$field)) {
                $data_to_store[$field] = $request->$field;
            }
        }

        // Handle numeric fields - convert empty strings to null
        $numericFields = [
            'net_premium', 'premium_amount', 'gst', 'final_premium_with_gst',
            'od_premium', 'tp_premium', 'cgst1', 'sgst1', 'cgst2', 'sgst2',
            'my_commission_percentage', 'my_commission_amount', 
            'transfer_commission_percentage', 'transfer_commission_amount',
            'reference_commission_percentage', 'reference_commission_amount',
            'actual_earnings', 'ncb_percentage', 'gross_vehicle_weight', 
            'mfg_year', 'sum_insured', 'pension_amount_yearly', 'approx_maturity_amount',
            'premium_paying_term', 'policy_term'
        ];
        
        foreach ($numericFields as $field) {
            if (array_key_exists($field, $data_to_store)) {
                $data_to_store[$field] = $data_to_store[$field] === '' ? null : $data_to_store[$field];
            }
        }

        return $data_to_store;
    }

    public function createCustomerInsurance(array $data): CustomerInsurance
    {
        return $this->createInTransaction(function () use ($data) {
            // Calculate commission breakdown
            $data = $this->calculateCommissionFields($data);

            return $this->customerInsuranceRepository->create($data);
        });
    }
    
    public function updateCustomerInsurance(CustomerInsurance $customerInsurance, array $data): CustomerInsurance
    {
        return $this->updateInTransaction(function () use ($customerInsurance, $data) {
            // Calculate commission breakdown
            $data = $this->calculateCommissionFields($data);

            $updatedCustomerInsurance = $this->customerInsuranceRepository->update($customerInsurance, $data);

            // Handle policy document upload if present
            if (isset($data['policy_document']) && $data['policy_document']) {
                $this->handlePolicyDocument($updatedCustomerInsurance, $data['policy_document']);
            }

            return $updatedCustomerInsurance;
        });
    }
    
    public function deleteCustomerInsurance(CustomerInsurance $customerInsurance): bool
    {
        return $this->deleteInTransaction(function () use ($customerInsurance) {
            // Delete policy document if exists
            if ($customerInsurance->policy_document_path && Storage::exists($customerInsurance->policy_document_path)) {
                Storage::delete($customerInsurance->policy_document_path);
            }

            return $this->customerInsuranceRepository->delete($customerInsurance);
        });
    }
    
    public function updateStatus(int $customerInsuranceId, int $status): bool
    {
        return $this->executeInTransaction(
            fn() => $this->customerInsuranceRepository->updateStatus($customerInsuranceId, $status)
        );
    }
    
    public function handleFileUpload(Request $request, CustomerInsurance $customerInsurance): void
    {
        if ($request->hasFile('policy_document_path')) {
            $file = $request->file('policy_document_path');
            $timestamp = time();
            
            // Extract necessary information
            $customerName = $customerInsurance->customer->name;
            $premiumType = $customerInsurance->premiumType->name;
            $policyNo = $customerInsurance->policy_no;
            $registrationNo = $customerInsurance->registration_no;
            $currentYear = date('Y');
            
            if (!empty($registrationNo)) {
                $fileName = $registrationNo . '-' . $currentYear . '-POLICY COPY-' . $timestamp;
            } else {
                $fileName = $customerName . '-' . $premiumType . '-' . $policyNo . '-' . $currentYear . '-POLICY COPY-' . $timestamp;
            }
            
            // Clean filename
            $fileName = trim($fileName, '-');
            $fileName = str_replace('--', '-', $fileName);
            $fileName .= '-' . time();
            $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '', str_replace(' ', '-', $fileName));
            
            // Store the file
            $path = $file->storeAs(
                'customer_insurances/' . $customerInsurance->id . '/policy_document_path', 
                $fileName . '.' . $file->getClientOriginalExtension(), 
                'public'
            );
            
            // Update the policy_document_path
            $customerInsurance->update(['policy_document_path' => $path]);
        }
    }

    public function sendWhatsAppDocument(CustomerInsurance $customerInsurance): bool
    {
        \Log::info('Starting WhatsApp document send', [
            'customer_insurance_id' => $customerInsurance->id,
            'policy_no' => $customerInsurance->policy_no,
            'customer_name' => $customerInsurance->customer->name ?? 'N/A',
            'mobile_number' => $customerInsurance->customer->mobile_number,
            'document_path' => $customerInsurance->policy_document_path,
            'user_id' => auth()->user()->id ?? 'System'
        ]);

        if (empty($customerInsurance->policy_document_path)) {
            \Log::warning('WhatsApp document send skipped - no document path', [
                'customer_insurance_id' => $customerInsurance->id,
                'policy_no' => $customerInsurance->policy_no
            ]);
            return false;
        }

        try {
            $message = $this->insuranceAdded($customerInsurance);
            $filePath = Storage::path('public' . DIRECTORY_SEPARATOR . $customerInsurance->policy_document_path);

            if (!file_exists($filePath)) {
                throw new \Exception("Policy document file not found: {$filePath}");
            }

            $response = $this->whatsAppSendMessageWithAttachment($message, $customerInsurance->customer->mobile_number, $filePath);

            \Log::info('WhatsApp document sent successfully', [
                'customer_insurance_id' => $customerInsurance->id,
                'policy_no' => $customerInsurance->policy_no,
                'mobile_number' => $customerInsurance->customer->mobile_number,
                'response' => $response,
                'user_id' => auth()->user()->id ?? 'System'
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('WhatsApp document send failed', [
                'customer_insurance_id' => $customerInsurance->id,
                'policy_no' => $customerInsurance->policy_no,
                'customer_name' => $customerInsurance->customer->name ?? 'N/A',
                'mobile_number' => $customerInsurance->customer->mobile_number,
                'document_path' => $customerInsurance->policy_document_path,
                'error' => $e->getMessage(),
                'user_id' => auth()->user()->id ?? 'System',
                'trace' => $e->getTraceAsString()
            ]);

            throw $e; // Re-throw to maintain the error flow
        }
    }

    public function sendRenewalReminderWhatsApp(CustomerInsurance $customerInsurance): bool
    {
        \Log::info('Starting WhatsApp renewal reminder send', [
            'customer_insurance_id' => $customerInsurance->id,
            'policy_no' => $customerInsurance->policy_no,
            'customer_name' => $customerInsurance->customer->name ?? 'N/A',
            'mobile_number' => $customerInsurance->customer->mobile_number,
            'expired_date' => $customerInsurance->expired_date,
            'is_vehicle' => $customerInsurance->premiumType->is_vehicle ?? 0,
            'user_id' => auth()->user()->id ?? 'System'
        ]);

        try {
            $messageText = $customerInsurance->premiumType->is_vehicle == 1
                ? $this->renewalReminderVehicle($customerInsurance)
                : $this->renewalReminder($customerInsurance);

            $receiverId = $customerInsurance->customer->mobile_number;
            $response = $this->whatsAppSendMessage($messageText, $receiverId);

            \Log::info('WhatsApp renewal reminder sent successfully', [
                'customer_insurance_id' => $customerInsurance->id,
                'policy_no' => $customerInsurance->policy_no,
                'mobile_number' => $receiverId,
                'response' => $response,
                'user_id' => auth()->user()->id ?? 'System'
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('WhatsApp renewal reminder send failed', [
                'customer_insurance_id' => $customerInsurance->id,
                'policy_no' => $customerInsurance->policy_no,
                'customer_name' => $customerInsurance->customer->name ?? 'N/A',
                'mobile_number' => $customerInsurance->customer->mobile_number,
                'expired_date' => $customerInsurance->expired_date,
                'error' => $e->getMessage(),
                'user_id' => auth()->user()->id ?? 'System',
                'trace' => $e->getTraceAsString()
            ]);

            throw $e; // Re-throw to maintain the error flow
        }
    }

    public function renewPolicy(CustomerInsurance $customerInsurance, array $data): CustomerInsurance
    {
        return $this->executeInTransaction(function () use ($customerInsurance, $data) {
            // Calculate commission breakdown for renewal data
            $data = $this->calculateCommissionFields($data);

            // Create new policy record for renewal
            $renewalData = $this->prepareRenewalStorageData($data);
            $newPolicy = $this->customerInsuranceRepository->create($renewalData);

            // Mark original policy as renewed
            $this->customerInsuranceRepository->update($customerInsurance, [
                'status' => 0,
                'is_renewed' => 1,
                'renewed_date' => Carbon::now(),
                'new_insurance_id' => $newPolicy->id
            ]);

            return $newPolicy;
        });
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
    
    
    private function prepareRenewalStorageData(array $renewalData): array
    {
        // Remove fields that shouldn't be copied to new record
        unset($renewalData['id']);
        $renewalData['is_renewed'] = 0;
        $renewalData['renewed_date'] = null;
        $renewalData['new_insurance_id'] = null;
        $renewalData['created_at'] = now();
        $renewalData['updated_at'] = now();
        
        return $renewalData;
    }
    
}