<?php

namespace App\Services;

use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Services\CustomerServiceInterface;
use App\Events\Customer\CustomerRegistered;
use App\Events\Customer\CustomerProfileUpdated;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\FileUploadService;
use App\Traits\WhatsAppApiTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerService extends BaseService implements CustomerServiceInterface
{
    use WhatsAppApiTrait;

    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private FileUploadService $fileUploadService
    ) {
    }

    public function getCustomers(Request $request): LengthAwarePaginator
    {
        return $this->customerRepository->getPaginated($request, 10);
    }

    public function createCustomer(StoreCustomerRequest $request): Customer
    {
        // Check for existing email first to provide better error message
        if ($this->findByEmail($request->email)) {
            throw new \Exception('A customer with this email address already exists. Please use a different email address.');
        }

        return $this->createInTransaction(function () use ($request) {
            // Create customer with validated data
            $customer = $this->customerRepository->create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'status' => $request->status,
                'wedding_anniversary_date' => $request->wedding_anniversary_date,
                'engagement_anniversary_date' => $request->engagement_anniversary_date,
                'date_of_birth' => $request->date_of_birth,
                'type' => $request->type,
                'pan_card_number' => $request->pan_card_number,
                'aadhar_card_number' => $request->aadhar_card_number,
                'gst_number' => $request->gst_number,
            ]);

            // Handle document uploads
            $this->handleCustomerDocuments($request, $customer);

            // Send welcome email synchronously within transaction
            // This will cause rollback if email sending fails
            try {
                $this->sendWelcomeEmailSync($customer);
            } catch (\Throwable $emailError) {
                // Log the email error but continue with transaction rollback
                \Log::error('Customer welcome email failed during creation', [
                    'customer_id' => $customer->id,
                    'customer_email' => $customer->email,
                    'error' => $emailError->getMessage()
                ]);

                // Delete the customer record if it was created
                $customer->delete();

                // Re-throw to trigger transaction rollback
                throw new \Exception('Customer registration failed: Unable to send welcome email to ' . $customer->email . '. Please verify the email address and try again.');
            }

            // Fire other events for async processing (audit logs, admin notifications)
            // These are non-critical and won't rollback the transaction
            try {
                CustomerRegistered::dispatch(
                    $customer,
                    [
                        'request_data' => $request->only(['type', 'status']),
                        'has_documents' => $request->hasFile('documents'),
                    ],
                    'admin'
                );
            } catch (\Throwable $eventError) {
                // Log but don't rollback - customer was successfully created
                \Log::warning('Post-creation events failed', [
                    'customer_id' => $customer->id,
                    'error' => $eventError->getMessage()
                ]);
            }

            return $customer;
        });
    }

    public function updateCustomer(UpdateCustomerRequest $request, Customer $customer): bool
    {
        return $this->updateInTransaction(function () use ($request, $customer) {
            // Capture original values for change tracking
            $originalValues = $customer->only([
                'name', 'email', 'mobile_number', 'status', 'type',
                'pan_card_number', 'aadhar_card_number', 'gst_number'
            ]);

            $newValues = [
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'status' => $request->status,
                'wedding_anniversary_date' => $request->wedding_anniversary_date,
                'engagement_anniversary_date' => $request->engagement_anniversary_date,
                'date_of_birth' => $request->date_of_birth,
                'type' => $request->type,
                'pan_card_number' => $request->pan_card_number,
                'aadhar_card_number' => $request->aadhar_card_number,
                'gst_number' => $request->gst_number,
            ];

            // Update customer data
            $updated = $this->customerRepository->update($customer->id, $newValues);

            if ($updated) {
                // Handle document uploads
                $this->handleCustomerDocuments($request, $customer);

                // Identify changed fields
                $changedFields = [];
                foreach ($newValues as $field => $newValue) {
                    if (isset($originalValues[$field]) && $originalValues[$field] !== $newValue) {
                        $changedFields[] = $field;
                    }
                }

                // Fire CustomerProfileUpdated event if there are changes
                if (!empty($changedFields)) {
                    CustomerProfileUpdated::dispatch(
                        $customer->fresh(),
                        $changedFields,
                        $originalValues
                    );
                }

                return true;
            }

            return false;
        });
    }

    public function updateCustomerStatus(int $customerId, int $status): bool
    {
        // Validate input
        $validate = Validator::make([
            'customer_id' => $customerId,
            'status' => $status,
        ], [
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:0,1',
        ]);

        if ($validate->fails()) {
            throw new \InvalidArgumentException($validate->errors()->first());
        }

        return $this->executeInTransaction(
            fn() => $this->customerRepository->updateStatus($customerId, $status)
        );
    }

    public function deleteCustomer(Customer $customer): bool
    {
        return $this->deleteInTransaction(
            fn() => $this->customerRepository->delete($customer->id)
        );
    }

    public function handleCustomerDocuments(StoreCustomerRequest|UpdateCustomerRequest $request, Customer $customer): void
    {
        $documentsUpdated = false;

        // Handle PAN card upload
        if ($request->hasFile('pan_card_path')) {
            $customer->pan_card_path = $this->fileUploadService->uploadCustomerDocument(
                $request->file('pan_card_path'),
                $customer->id,
                'pan_card',
                $customer->name
            );
            $documentsUpdated = true;
        }

        // Handle Aadhar card upload
        if ($request->hasFile('aadhar_card_path')) {
            $customer->aadhar_card_path = $this->fileUploadService->uploadCustomerDocument(
                $request->file('aadhar_card_path'),
                $customer->id,
                'aadhar_card',
                $customer->name
            );
            $documentsUpdated = true;
        }

        // Handle GST document upload
        if ($request->hasFile('gst_path')) {
            $customer->gst_path = $this->fileUploadService->uploadCustomerDocument(
                $request->file('gst_path'),
                $customer->id,
                'gst',
                $customer->name
            );
            $documentsUpdated = true;
        }

        // Save document paths if any were uploaded
        if ($documentsUpdated) {
            $customer->save();
        }
    }

    public function sendOnboardingMessage(Customer $customer): bool
    {
        try {
            $message = $this->generateOnboardingMessage($customer);
            return $this->whatsAppSendMessage($message, $customer->mobile_number);
        } catch (\Throwable $th) {
            // Log the error but don't fail the customer creation
            \Log::warning('Failed to send onboarding WhatsApp message', [
                'customer_id' => $customer->id,
                'error' => $th->getMessage()
            ]);
            return false;
        }
    }

    public function getActiveCustomersForSelection(): Collection
    {
        return $this->customerRepository->getActive();
    }

    /**
     * Get customers by family group.
     */
    public function getCustomersByFamily(int $familyGroupId): Collection
    {
        return $this->customerRepository->getByFamilyGroup($familyGroupId);
    }

    /**
     * Get customers by type (Retail/Corporate).
     */
    public function getCustomersByType(string $type): Collection
    {
        return $this->customerRepository->getByType($type);
    }

    /**
     * Search customers by query.
     */
    public function searchCustomers(string $query): Collection
    {
        return $this->customerRepository->search($query);
    }

    /**
     * Get customer statistics for dashboard.
     */
    public function getCustomerStatistics(): array
    {
        $total = $this->customerRepository->count();
        $active = $this->customerRepository->getByType('Retail')->where('status', 1)->count();
        $corporate = $this->customerRepository->getByType('Corporate')->count();
        
        return [
            'total' => $total,
            'active' => $active,
            'corporate' => $corporate,
        ];
    }

    /**
     * Check if customer exists by ID.
     */
    public function customerExists(int $customerId): bool
    {
        return $this->customerRepository->exists($customerId);
    }

    /**
     * Find customer by email.
     */
    public function findByEmail(string $email): ?Customer
    {
        return $this->customerRepository->findByEmail($email);
    }

    /**
     * Find customer by mobile number.
     */
    public function findByMobileNumber(string $mobileNumber): ?Customer
    {
        return $this->customerRepository->findByMobileNumber($mobileNumber);
    }

    /**
     * Generate onboarding WhatsApp message.
     */
    private function generateOnboardingMessage(Customer $customer): string
    {
        return $this->newCustomerAdd($customer);
    }

    /**
     * Send welcome email synchronously within transaction
     * This will throw exception if email fails, causing transaction rollback
     */
    private function sendWelcomeEmailSync(Customer $customer): void
    {
        try {

            // Use Mail facade to send email synchronously
            \Mail::send('emails.customer.welcome', [
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_type' => $customer->type,
                'portal_url' => config('app.url') . '/customer',
                'support_email' => config('mail.from.address'),
                'company_name' => config('app.name')
            ], function ($message) use ($customer) {
                $message->to($customer->email, $customer->name)
                        ->subject('Welcome to ' . config('app.name') . ' - Your Customer Account is Ready!');
                $message->from(config('mail.from.address'), config('app.name'));
            });

            \Log::info('Welcome email sent successfully', [
                'customer_id' => $customer->id,
                'customer_email' => $customer->email
            ]);

        } catch (\Throwable $e) {
            \Log::error('Failed to send welcome email', [
                'customer_id' => $customer->id,
                'customer_email' => $customer->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw with user-friendly message
            throw new \Exception('Unable to send welcome email to ' . $customer->email . '. Please verify the email address and try again.');
        }
    }
}