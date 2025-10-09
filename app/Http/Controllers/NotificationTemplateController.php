<?php

namespace App\Http\Controllers;

use App\Models\NotificationTemplate;
use App\Models\NotificationType;
use App\Services\Notification\NotificationContext;
use App\Services\Notification\VariableResolverService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Notification Template Controller
 *
 * Manages WhatsApp and Email message templates for automated notifications
 */
class NotificationTemplateController extends AbstractBaseCrudController
{
    use \App\Traits\WhatsAppApiTrait;

    public function __construct()
    {
        $this->setupPermissionMiddleware('notification-template');
    }

    /**
     * Display a listing of notification templates
     */
    public function index(Request $request): View
    {
        $query = NotificationTemplate::with('notificationType');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'LIKE', "%{$search}%")
                    ->orWhere('template_content', 'LIKE', "%{$search}%")
                    ->orWhereHas('notificationType', function ($nq) use ($search) {
                        $nq->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('notificationType', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        // Channel filter
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'notification_type_id');
        $sortOrder = $request->get('sort_order', 'asc');

        // Validate sort columns
        $allowedSorts = ['notification_type_id', 'channel', 'is_active', 'created_at', 'updated_at'];
        if (! in_array($sortBy, $allowedSorts)) {
            $sortBy = 'notification_type_id';
        }

        // Validate sort order
        if (! in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $templates = $query->paginate(config('app.pagination_default', 15));
        $templates->appends($request->except('page'));

        // Get unique categories for filter
        $categories = \DB::table('notification_types')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category', 'category');

        return view('admin.notification_templates.index', compact('templates', 'categories'));
    }

    /**
     * Show the form for creating a new template
     */
    public function create(): View
    {
        $notificationTypes = NotificationType::where('is_active', true)
            ->orderBy('category')
            ->orderBy('order_no')
            ->get();

        // Load all customers for preview dropdown
        $customers = \App\Models\Customer::select('id', 'name', 'mobile_number', 'email')
            ->orderBy('name', 'asc')
            ->get();

        // Policies and quotations will be loaded dynamically when customer is selected
        return view('admin.notification_templates.create', compact('notificationTypes', 'customers'));
    }

    /**
     * Store a newly created template
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'notification_type_id' => 'required|exists:notification_types,id',
            'channel' => 'required|in:whatsapp,email,both',
            'subject' => 'nullable|string|max:200',
            'template_content' => 'required|string',
            'available_variables' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        try {
            NotificationTemplate::create([
                'notification_type_id' => $request->notification_type_id,
                'channel' => $request->channel,
                'subject' => $request->subject,
                'template_content' => $request->template_content,
                'available_variables' => $request->available_variables ? json_decode($request->available_variables, true) : [],
                'is_active' => $request->has('is_active'),
                'updated_by' => auth()->id(),
            ]);

            return $this->redirectWithSuccess('notification-templates.index',
                $this->getSuccessMessage('Notification Template', 'created'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Notification Template', 'create').': '.$th->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified template
     */
    public function edit(NotificationTemplate $template): View
    {
        $template->load('notificationType');
        $notificationTypes = NotificationType::where('is_active', true)
            ->orderBy('category')
            ->orderBy('order_no')
            ->get();

        // Load all customers for preview dropdown
        $customers = \App\Models\Customer::select('id', 'name', 'mobile_number', 'email')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.notification_templates.edit', compact('template', 'notificationTypes', 'customers'));
    }

    /**
     * Update the specified template
     */
    public function update(Request $request, NotificationTemplate $template): RedirectResponse
    {
        $request->validate([
            'notification_type_id' => 'required|exists:notification_types,id',
            'channel' => 'required|in:whatsapp,email,both',
            'subject' => 'nullable|string|max:200',
            'template_content' => 'required|string',
            'available_variables' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        try {
            $template->update([
                'notification_type_id' => $request->notification_type_id,
                'channel' => $request->channel,
                'subject' => $request->subject,
                'template_content' => $request->template_content,
                'available_variables' => $request->available_variables ? json_decode($request->available_variables, true) : [],
                'is_active' => $request->has('is_active'),
                'updated_by' => auth()->id(),
            ]);

            return $this->redirectWithSuccess('notification-templates.index',
                $this->getSuccessMessage('Notification Template', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Notification Template', 'update').': '.$th->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified template
     */
    public function delete(NotificationTemplate $template): RedirectResponse
    {
        try {
            $template->delete();

            return $this->redirectWithSuccess('notification-templates.index',
                $this->getSuccessMessage('Notification Template', 'deleted'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Notification Template', 'delete').': '.$th->getMessage());
        }
    }

    /**
     * Preview template with real data
     */
    public function preview(Request $request)
    {
        $request->validate([
            'template_content' => 'required|string',
            'customer_id' => 'nullable|integer|exists:customers,id',
            'insurance_id' => 'nullable|integer|exists:customer_insurances,id',
            'quotation_id' => 'nullable|integer|exists:quotations,id',
        ]);

        try {
            $content = $request->template_content;

            // Build context with real data
            $context = $this->buildPreviewContext(
                $request->customer_id,
                $request->insurance_id,
                $request->quotation_id
            );

            // Resolve template variables using new service
            $resolver = app(VariableResolverService::class);
            $preview = $resolver->resolveTemplate($content, $context);

            return response()->json([
                'success' => true,
                'preview' => $preview,
                'context_info' => [
                    'customer' => $context->customer?->name,
                    'insurance' => $context->insurance?->policy_no,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Preview failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get customer's policies and quotations for preview
     */
    public function getCustomerData(Request $request)
    {
        $customerId = $request->input('customer_id');

        if (! $customerId) {
            return response()->json([
                'success' => false,
                'message' => 'Customer ID required',
            ], 400);
        }

        // Get customer's policies (all statuses)
        $policies = \App\Models\CustomerInsurance::where('customer_id', $customerId)
            ->select('id', 'policy_no', 'registration_no', 'status')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get customer's quotations
        $quotations = \App\Models\Quotation::where('customer_id', $customerId)
            ->select('id', 'vehicle_number', 'make_model_variant')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'policies' => $policies,
            'quotations' => $quotations,
        ]);
    }

    /**
     * Get available variables for dynamic UI
     */
    public function getAvailableVariables(Request $request)
    {
        $notificationType = $request->input('notification_type');

        $registry = app(\App\Services\Notification\VariableRegistryService::class);

        // Get variables grouped by category
        $groupedVariables = $registry->getVariablesGroupedByCategory($notificationType);

        return response()->json([
            'success' => true,
            'variables' => $groupedVariables,
            'categories' => $registry->getAllCategories(),
        ]);
    }

    /**
     * Build notification context for preview
     *
     * @param  int|null  $customerId  Optional customer ID
     * @param  int|null  $insuranceId  Optional insurance ID
     */
    protected function buildPreviewContext(?int $customerId, ?int $insuranceId, ?int $quotationId = null): NotificationContext
    {
        $context = new NotificationContext;

        if ($customerId) {
            // Use specified customer
            $context = NotificationContext::fromCustomerId($customerId, $insuranceId);
        } elseif ($insuranceId) {
            // Use specified insurance (loads customer automatically)
            $context = NotificationContext::fromInsuranceId($insuranceId);
        } elseif ($quotationId) {
            // Use specified quotation (loads customer automatically)
            $context = NotificationContext::fromQuotationId($quotationId);
        } else {
            // Use random real data for preview
            $context = NotificationContext::sample();
        }

        // Load app settings
        $context->settings = $this->loadSettings();

        return $context;
    }

    /**
     * Load app settings for context
     */
    protected function loadSettings(): array
    {
        $settings = \App\Models\AppSetting::where('is_active', true)->get();

        $structured = [];
        foreach ($settings as $setting) {
            // Strip category prefix from key
            // e.g., 'company_advisor_name' becomes 'advisor_name' under 'company' category
            $key = $setting->key;
            $categoryPrefix = $setting->category.'_';

            if (str_starts_with($key, $categoryPrefix)) {
                $key = substr($key, strlen($categoryPrefix));
            }

            $structured[$setting->category][$key] = $setting->value;
        }

        return $structured;
    }

    /**
     * Send test message with real data
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'recipient' => 'required|string',
            'channel' => 'required|in:whatsapp,email',
            'subject' => 'nullable|string',
            'template_content' => 'required|string',
            'customer_id' => 'nullable|integer|exists:customers,id',
            'insurance_id' => 'nullable|integer|exists:customer_insurances,id',
        ]);

        try {
            $content = $request->template_content;
            $channel = $request->channel;
            $recipient = $request->recipient;

            // Build context with real data
            $context = $this->buildPreviewContext(
                $request->customer_id,
                $request->insurance_id
            );

            // Resolve template variables using new service
            $resolver = app(VariableResolverService::class);
            $message = $resolver->resolveTemplate($content, $context);

            // Resolve subject if email
            $subject = $request->subject;
            if ($subject && $channel === 'email') {
                $subject = $resolver->resolveTemplate($subject, $context);
            }

            // Send based on channel
            if ($channel === 'whatsapp') {
                $result = $this->sendWhatsAppTest($recipient, $message);
            } elseif ($channel === 'email') {
                $subject = $subject ?: 'Test Email Template';
                $result = $this->sendEmailTest($recipient, $subject, $message);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid channel selected',
                ], 400);
            }

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test message sent successfully to '.$recipient,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to send test message',
                ], 400);
            }

        } catch (\Exception $e) {
            \Log::error('Send test failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send WhatsApp test message - uses same method as onboarding
     */
    private function sendWhatsAppTest(string $phoneNumber, string $message): array
    {
        try {
            // Use the same WhatsAppApiTrait method that works for onboarding
            $response = $this->whatsAppSendMessage($message, $phoneNumber);

            \Log::info('WhatsApp Test - Response', [
                'phone' => $phoneNumber,
                'response' => $response,
            ]);

            // The trait method returns JSON string response
            $responseData = json_decode($response, true);

            // Check if response indicates success
            if (is_array($responseData)) {
                foreach ($responseData as $result) {
                    if (isset($result['success']) && $result['success'] === true) {
                        return [
                            'success' => true,
                            'message' => 'WhatsApp message sent successfully',
                        ];
                    }
                }
            }

            return [
                'success' => true,
                'message' => 'WhatsApp message sent',
            ];

        } catch (\Exception $e) {
            \Log::error('WhatsApp test exception', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber,
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send Email test message
     */
    private function sendEmailTest(string $email, string $subject, string $message): array
    {
        try {
            \Mail::raw($message, function ($mail) use ($email, $subject) {
                $mail->to($email)
                    ->subject($subject);
            });

            return ['success' => true, 'message' => 'Email sent'];
        } catch (\Exception $e) {
            \Log::error('Email test failed', ['error' => $e->getMessage()]);

            return ['success' => false, 'message' => 'Email error: '.$e->getMessage()];
        }
    }
}
