<?php

namespace App\Modules\Notification\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Notification\Contracts\NotificationServiceInterface;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class NotificationApiController extends Controller
{
    public function __construct(
        private NotificationServiceInterface $notificationService
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Send WhatsApp message.
     */
    public function sendWhatsApp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:4000',
            'phone_number' => 'required|string|regex:/^[0-9+\-\s()]+$/',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $attachments = null;
            if ($request->hasFile('attachments')) {
                $attachments = [];
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('temp/whatsapp', 'public');
                    $attachments[] = storage_path('app/public/' . $path);
                }
            }

            $sent = $this->notificationService->sendWhatsAppMessage(
                $request->input('message'),
                $request->input('phone_number'),
                $attachments
            );

            // Clean up temporary files
            if ($attachments) {
                foreach ($attachments as $attachment) {
                    if (file_exists($attachment)) {
                        unlink($attachment);
                    }
                }
            }

            return response()->json([
                'success' => $sent,
                'message' => $sent ? 'WhatsApp message sent successfully' : 'Failed to send WhatsApp message',
                'data' => [
                    'phone_number' => $request->input('phone_number'),
                    'sent_at' => $sent ? now()->toISOString() : null,
                ]
            ], $sent ? Response::HTTP_OK : Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send WhatsApp message',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Send email.
     */
    public function sendEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $attachments = null;
            if ($request->hasFile('attachments')) {
                $attachments = [];
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('temp/email', 'public');
                    $attachments[] = storage_path('app/public/' . $path);
                }
            }

            $sent = $this->notificationService->sendEmail(
                $request->input('email'),
                $request->input('subject'),
                $request->input('body'),
                $attachments
            );

            // Clean up temporary files
            if ($attachments) {
                foreach ($attachments as $attachment) {
                    if (file_exists($attachment)) {
                        unlink($attachment);
                    }
                }
            }

            return response()->json([
                'success' => $sent,
                'message' => $sent ? 'Email sent successfully' : 'Failed to send email',
                'data' => [
                    'email' => $request->input('email'),
                    'subject' => $request->input('subject'),
                    'sent_at' => $sent ? now()->toISOString() : null,
                ]
            ], $sent ? Response::HTTP_OK : Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Send SMS.
     */
    public function sendSms(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|regex:/^[0-9+\-\s()]+$/',
            'message' => 'required|string|max:160',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $sent = $this->notificationService->sendSms(
                $request->input('phone_number'),
                $request->input('message')
            );

            return response()->json([
                'success' => $sent,
                'message' => $sent ? 'SMS sent successfully' : 'Failed to send SMS',
                'data' => [
                    'phone_number' => $request->input('phone_number'),
                    'message' => $request->input('message'),
                    'sent_at' => $sent ? now()->toISOString() : null,
                ]
            ], $sent ? Response::HTTP_OK : Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Queue notification for later processing.
     */
    public function queueNotification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:whatsapp,email,sms',
            'recipient' => 'required|array',
            'content' => 'required|array',
            'priority' => 'nullable|integer|min:1|max:9',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $queued = $this->notificationService->queueNotification(
                $request->input('type'),
                $request->input('recipient'),
                $request->input('content'),
                $request->input('priority', 5)
            );

            return response()->json([
                'success' => $queued,
                'message' => $queued ? 'Notification queued successfully' : 'Failed to queue notification',
                'data' => [
                    'type' => $request->input('type'),
                    'priority' => $request->input('priority', 5),
                    'queued_at' => $queued ? now()->toISOString() : null,
                ]
            ], $queued ? Response::HTTP_OK : Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to queue notification',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get notification status.
     */
    public function getStatus(string $messageId): JsonResponse
    {
        try {
            $status = $this->notificationService->getNotificationStatus($messageId);

            return response()->json([
                'success' => true,
                'data' => $status,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notification status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get delivery report.
     */
    public function getDeliveryReport(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $report = $this->notificationService->getDeliveryReport(
                $request->input('start_date'),
                $request->input('end_date')
            );

            return response()->json([
                'success' => true,
                'data' => $report,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate delivery report',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Process notification queue.
     */
    public function processQueue(): JsonResponse
    {
        try {
            $processed = $this->notificationService->processNotificationQueue();

            return response()->json([
                'success' => true,
                'message' => "Processed {$processed} notifications from queue",
                'data' => [
                    'processed_count' => $processed,
                    'processed_at' => now()->toISOString(),
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process notification queue',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Retry failed notifications.
     */
    public function retryFailed(): JsonResponse
    {
        try {
            $retried = $this->notificationService->retryFailedNotifications();

            return response()->json([
                'success' => true,
                'message' => "Retried {$retried} failed notifications",
                'data' => [
                    'retried_count' => $retried,
                    'retried_at' => now()->toISOString(),
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retry notifications',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get queue statistics.
     */
    public function getQueueStats(): JsonResponse
    {
        try {
            // Get queue statistics from database
            $stats = \DB::table('message_queue')
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "queued" THEN 1 ELSE 0 END) as queued,
                    SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
                    SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
                    AVG(attempts) as avg_attempts
                ')
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $stats->total ?? 0,
                    'queued' => $stats->queued ?? 0,
                    'sent' => $stats->sent ?? 0,
                    'failed' => $stats->failed ?? 0,
                    'avg_attempts' => round($stats->avg_attempts ?? 0, 2),
                    'generated_at' => now()->toISOString(),
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve queue statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get notification templates by type.
     */
    public function getTemplates(string $type): JsonResponse
    {
        if (!in_array($type, ['whatsapp', 'email', 'sms'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid template type. Must be whatsapp, email, or sms.',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $templates = $this->notificationService->getNotificationTemplates($type);

            return response()->json([
                'success' => true,
                'data' => $templates,
                'type' => $type,
                'count' => count($templates),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notification templates',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create notification template.
     */
    public function createTemplate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:whatsapp,email,sms',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'variables' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $template = \DB::table('notification_templates')->insertGetId([
                'name' => $request->input('name'),
                'type' => $request->input('type'),
                'subject' => $request->input('subject'),
                'content' => $request->input('content'),
                'variables' => json_encode($request->input('variables', [])),
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template created successfully',
                'data' => [
                    'id' => $template,
                    'name' => $request->input('name'),
                    'type' => $request->input('type'),
                ]
            ], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create template',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update notification template.
     */
    public function updateTemplate(Request $request, int $templateId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'subject' => 'nullable|string|max:255',
            'content' => 'sometimes|string',
            'variables' => 'nullable|array',
            'active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $updateData = $request->only(['name', 'subject', 'content', 'active']);
            
            if ($request->has('variables')) {
                $updateData['variables'] = json_encode($request->input('variables'));
            }
            
            $updateData['updated_at'] = now();

            $updated = \DB::table('notification_templates')
                ->where('id', $templateId)
                ->update($updateData);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found',
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'Template updated successfully',
                'data' => ['id' => $templateId]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update template',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get customer communication preferences.
     */
    public function getCustomerPreferences(Customer $customer): JsonResponse
    {
        try {
            $preferences = \DB::table('communication_preferences')
                ->where('customer_id', $customer->id)
                ->first();

            $defaultPreferences = [
                'customer_id' => $customer->id,
                'whatsapp_enabled' => true,
                'email_enabled' => true,
                'sms_enabled' => false,
                'marketing_enabled' => true,
            ];

            $data = $preferences ? (array) $preferences : $defaultPreferences;

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve customer preferences',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update customer communication preferences.
     */
    public function updateCustomerPreferences(Request $request, Customer $customer): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'whatsapp' => 'nullable|boolean',
            'email' => 'nullable|boolean',
            'sms' => 'nullable|boolean',
            'marketing' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $preferences = $request->only(['whatsapp', 'email', 'sms', 'marketing']);
            
            $updated = $this->notificationService->updateCommunicationPreferences(
                $customer->id,
                $preferences
            );

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Communication preferences updated successfully',
                    'data' => [
                        'customer_id' => $customer->id,
                        'preferences' => $preferences,
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to update communication preferences',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update communication preferences',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}