<?php

namespace App\Http\Controllers;

use App\Models\NotificationTemplate;
use App\Models\NotificationType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Notification Template Controller
 *
 * Manages WhatsApp and Email message templates for automated notifications
 */
class NotificationTemplateController extends AbstractBaseCrudController
{
    public function __construct()
    {
        $this->setupPermissionMiddleware('notification-template');
    }

    /**
     * Display a listing of notification templates
     */
    public function index(): View
    {
        $templates = NotificationTemplate::with('notificationType')
            ->get();

        // Group by notification type category for better organization
        $groupedTemplates = $templates->groupBy(function($template) {
            return $template->notificationType->category ?? 'unknown';
        });

        return view('admin.notification_templates.index', compact('groupedTemplates'));
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

        return view('admin.notification_templates.create', compact('notificationTypes'));
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
                $this->getErrorMessage('Notification Template', 'create') . ': ' . $th->getMessage())
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

        return view('admin.notification_templates.edit', compact('template', 'notificationTypes'));
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
                $this->getErrorMessage('Notification Template', 'update') . ': ' . $th->getMessage())
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
                $this->getErrorMessage('Notification Template', 'delete') . ': ' . $th->getMessage());
        }
    }

    /**
     * Preview template with sample data
     */
    public function preview(Request $request)
    {
        $request->validate([
            'template_content' => 'required|string',
            'available_variables' => 'nullable|json',
        ]);

        try {
            $content = $request->template_content;
            $variables = $request->available_variables ? json_decode($request->available_variables, true) : [];

            // Generate sample data for preview
            $sampleData = $this->generateSampleData($variables);

            // Replace variables in template
            $preview = $this->renderTemplate($content, $sampleData);

            return response()->json([
                'success' => true,
                'preview' => $preview,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Generate sample data for variables
     */
    private function generateSampleData(array $variables): array
    {
        $samples = [
            'customer_name' => 'John Doe',
            'policy_number' => 'POL-2025-001',
            'policy_type' => '2 WHEELER',
            'insurance_company' => 'HDFC ERGO',
            'expiry_date' => date('d-M-Y', strtotime('+30 days')),
            'days_remaining' => '30',
            'premium_amount' => '₹5,000',
            'vehicle_number' => 'GJ-01-AB-1234',
            'company_name' => 'Your Insurance Advisor',
            'company_phone' => '+91 98765 43210',
        ];

        $data = [];
        foreach ($variables as $var) {
            $data[$var] = $samples[$var] ?? ucwords(str_replace('_', ' ', $var));
        }

        return $data;
    }

    /**
     * Render template with data
     */
    private function renderTemplate(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }

        return $template;
    }
}
