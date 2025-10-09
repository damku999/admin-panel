<?php

namespace App\Services;

use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Contracts\Services\CustomerServiceInterface;
use App\Events\Customer\CustomerProfileUpdated;
use App\Events\Customer\CustomerRegistered;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Traits\WhatsAppApiTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerService extends BaseService implements CustomerServiceInterface
{
    use WhatsAppApiTrait;

    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Get paginated list of customers with filtering and search.
     *
     * @param  Request  $request  HTTP request with optional filter parameters
     * @return LengthAwarePaginator Paginated customer collection with 10 items per page
     */
    public function getCustomers(Request $request): LengthAwarePaginator
    {
        return $this->customerRepository->getPaginated($request, 10);
    }

    /**
     * Create a new customer with document handling and welcome email.
     *
     * This method orchestrates customer creation within a database transaction,
     * ensuring atomicity of the customer record, associated documents, and
     * the welcome email notification. If email sending fails, the entire
     * transaction is rolled back to maintain data consistency.
     *
     * @param  StoreCustomerRequest  $request  Validated customer registration data including personal info and documents
     * @return Customer The newly created customer instance with relationships loaded
     *
     * @throws \Exception If email already exists in the system
     * @throws \Exception If email sending fails, triggering transaction rollback with user-friendly message
     */
    public function createCustomer(StoreCustomerRequest $request): Customer
    {
        // Check for existing email first to provide better error message
        if ($this->findByEmail($request->email)) {
            throw new \Exception('A customer with this email address already exists. Please use a different email address.');
        }

        return $this->createInTransaction(function () use ($request) {
            // Create customer with validated data
            /** @var Customer $customer */
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
                    'error' => $emailError->getMessage(),
                ]);

                // Delete the customer record if it was created
                $customer->delete();

                // Re-throw to trigger transaction rollback
                throw new \Exception('Customer registration failed: Unable to send welcome email to '.$customer->email.'. Please verify the email address and try again.');
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
                    'error' => $eventError->getMessage(),
                ]);
            }

            return $customer;
        });
    }

    /**
     * Update customer information with change tracking and event dispatching.
     *
     * Updates customer profile data within a transaction, handles document uploads,
     * and dispatches CustomerProfileUpdated event for changed fields to enable
     * audit logging and notifications.
     *
     * @param  UpdateCustomerRequest  $request  Validated update data with personal info and optional documents
     * @param  Customer  $customer  The customer instance to update
     * @return bool True if update successful, false otherwise
     */
    public function updateCustomer(UpdateCustomerRequest $request, Customer $customer): bool
    {
        return $this->updateInTransaction(function () use ($request, $customer) {
            // Capture original values for change tracking
            $originalValues = $customer->only([
                'name', 'email', 'mobile_number', 'status', 'type',
                'pan_card_number', 'aadhar_card_number', 'gst_number',
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
            $updated = $this->customerRepository->update($customer, $newValues);

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
                if (! empty($changedFields)) {
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

    /**
     * Update customer active status with validation.
     *
     * Changes customer status (active/inactive) within a transaction after
     * validating the customer ID and status value against business rules.
     *
     * @param  int  $customerId  The customer ID to update
     * @param  int  $status  Status value (0 = inactive, 1 = active)
     * @return bool True if status update successful
     *
     * @throws \InvalidArgumentException If customer ID doesn't exist or status value is invalid
     */
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
            fn () => $this->customerRepository->updateStatus($customerId, $status)
        );
    }

    /**
     * Soft delete customer record within transaction.
     *
     * @param  Customer  $customer  The customer instance to delete
     * @return bool True if deletion successful
     */
    public function deleteCustomer(Customer $customer): bool
    {
        return $this->deleteInTransaction(
            fn () => $this->customerRepository->delete($customer)
        );
    }

    /**
     * Handle document uploads for customer (PAN, Aadhar, GST).
     *
     * Processes and stores customer identity and tax documents using the
     * FileUploadService. Updates customer document paths only if new files
     * are provided in the request.
     *
     * @param  StoreCustomerRequest|UpdateCustomerRequest  $request  Request containing potential file uploads
     * @param  Customer  $customer  Customer instance to attach documents to
     */
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

    /**
     * Send onboarding WhatsApp message to newly registered customer.
     *
     * Attempts to send a welcome WhatsApp message using templates from the
     * notification system. Falls back to legacy hardcoded message if template
     * not found. Logs errors but returns false on failure to avoid blocking
     * customer creation.
     *
     * @param  Customer  $customer  The customer to send onboarding message to
     * @return bool True if message sent successfully, false otherwise
     */
    public function sendOnboardingMessage(Customer $customer): bool
    {
        try {
            $message = $this->generateOnboardingMessage($customer);

            return $this->whatsAppSendMessage($message, $customer->mobile_number);
        } catch (\Throwable $th) {
            // Log the error but don't fail the customer creation
            \Log::warning('Failed to send onboarding WhatsApp message', [
                'customer_id' => $customer->id,
                'error' => $th->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send onboarding email to newly registered customer.
     *
     * Sends welcome email using the EmailService and notification template system.
     * Logs errors but returns false on failure to prevent blocking customer creation.
     *
     * @param  Customer  $customer  The customer to send onboarding email to
     * @return bool True if email sent successfully, false otherwise
     */
    public function sendOnboardingEmail(Customer $customer): bool
    {
        try {
            $emailService = app(\App\Services\EmailService::class);

            return $emailService->sendFromCustomer('customer_welcome', $customer);
        } catch (\Throwable $th) {
            \Log::warning('Failed to send onboarding email', [
                'customer_id' => $customer->id,
                'error' => $th->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get active customers for dropdown selection.
     *
     * Retrieves only active customers (status = 1) for use in forms and
     * selection dropdowns across the application.
     *
     * @return Collection Collection of active customer records
     */
    public function getActiveCustomersForSelection(): Collection
    {
        return $this->customerRepository->getActive();
    }

    /**
     * Get customers by family group ID.
     *
     * Retrieves all customers belonging to a specific family group,
     * useful for family insurance policies and group operations.
     *
     * @param  int  $familyGroupId  The family group identifier
     * @return Collection Collection of customers in the family group
     */
    public function getCustomersByFamily(int $familyGroupId): Collection
    {
        return $this->customerRepository->getByFamilyGroup($familyGroupId);
    }

    /**
     * Get customers by type (Retail/Corporate).
     *
     * Filters customers by their business classification for targeted
     * marketing, reporting, and policy management.
     *
     * @param  string  $type  Customer type ('Retail' or 'Corporate')
     * @return Collection Collection of customers matching the specified type
     */
    public function getCustomersByType(string $type): Collection
    {
        return $this->customerRepository->getByType($type);
    }

    /**
     * Search customers by query string.
     *
     * Performs full-text search across customer name, email, and mobile number
     * for autocomplete and lookup functionality.
     *
     * @param  string  $query  Search term to match against customer data
     * @return Collection Collection of matching customer records
     */
    public function searchCustomers(string $query): Collection
    {
        return $this->customerRepository->search($query);
    }

    /**
     * Get customer statistics for dashboard display.
     *
     * Aggregates customer data by various dimensions (total count, active retail,
     * corporate customers) for dashboard widgets and reporting.
     *
     * @return array Associative array with 'total', 'active', and 'corporate' counts
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
     *
     * Verifies customer existence before performing operations that require
     * a valid customer record.
     *
     * @param  int  $customerId  The customer ID to verify
     * @return bool True if customer exists, false otherwise
     */
    public function customerExists(int $customerId): bool
    {
        return $this->customerRepository->exists($customerId);
    }

    /**
     * Find customer by email address.
     *
     * Searches for existing customer by email to prevent duplicate registrations
     * and enable email-based customer lookup.
     *
     * @param  string  $email  The email address to search for
     * @return Customer|null Customer instance if found, null otherwise
     */
    public function findByEmail(string $email): ?Customer
    {
        return $this->customerRepository->findByEmail($email);
    }

    /**
     * Find customer by mobile number.
     *
     * Searches for customer by mobile number for WhatsApp messaging,
     * duplicate detection, and phone-based customer lookup.
     *
     * @param  string  $mobileNumber  The mobile number to search for
     * @return Customer|null Customer instance if found, null otherwise
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
        // Try to get message from template, fallback to hardcoded
        $templateService = app(\App\Services\TemplateService::class);
        $message = $templateService->renderFromCustomer('customer_welcome', 'whatsapp', $customer);

        if (! $message) {
            // Fallback to old hardcoded message
            return $this->newCustomerAdd($customer);
        }

        return $message;
    }

    /**
     * Send welcome email synchronously within transaction
     * This will throw exception if email fails, causing transaction rollback
     */
    private function sendWelcomeEmailSync(Customer $customer): void
    {
        try {
            // Check if email notifications are enabled
            if (! is_email_notification_enabled()) {
                \Log::info('Welcome email skipped (disabled in settings)', [
                    'customer_id' => $customer->id,
                ]);

                return;
            }

            // Use Mail facade to send email synchronously
            \Mail::send('emails.customer.welcome', [
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_type' => $customer->type,
                'portal_url' => config('app.url').'/customer',
                'support_email' => config('mail.from.address'),
                'company_name' => config('app.name'),
            ], function ($message) use ($customer) {
                $message->to($customer->email, $customer->name)
                    ->subject('Welcome to '.config('app.name').' - Your Customer Account is Ready!');
                $message->from(config('mail.from.address'), config('app.name'));
            });

            \Log::info('Welcome email sent successfully', [
                'customer_id' => $customer->id,
                'customer_email' => $customer->email,
            ]);

        } catch (\Throwable $e) {
            \Log::error('Failed to send welcome email', [
                'customer_id' => $customer->id,
                'customer_email' => $customer->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw with user-friendly message
            throw new \Exception('Unable to send welcome email to '.$customer->email.'. Please verify the email address and try again.');
        }
    }
}
