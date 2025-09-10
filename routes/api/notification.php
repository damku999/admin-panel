<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Notification\Http\Controllers\Api\NotificationApiController;

/*
|--------------------------------------------------------------------------
| Notification Module API Routes
|--------------------------------------------------------------------------
|
| These routes provide API access to notification module functionality.
| All routes are prefixed with /api/notifications and include authentication.
|
*/

Route::middleware(['auth:sanctum'])->prefix('notifications')->group(function () {
    // Direct notification sending
    Route::post('/whatsapp', [NotificationApiController::class, 'sendWhatsApp']);
    Route::post('/email', [NotificationApiController::class, 'sendEmail']);
    Route::post('/sms', [NotificationApiController::class, 'sendSms']);
    
    // Notification queuing
    Route::post('/queue', [NotificationApiController::class, 'queueNotification']);
    
    // Notification status and tracking
    Route::get('/{messageId}/status', [NotificationApiController::class, 'getStatus']);
    Route::get('/delivery-report', [NotificationApiController::class, 'getDeliveryReport']);
    
    // Queue management
    Route::post('/queue/process', [NotificationApiController::class, 'processQueue']);
    Route::post('/queue/retry-failed', [NotificationApiController::class, 'retryFailed']);
    Route::get('/queue/stats', [NotificationApiController::class, 'getQueueStats']);
    
    // Template management
    Route::get('/templates/{type}', [NotificationApiController::class, 'getTemplates']);
    Route::post('/templates', [NotificationApiController::class, 'createTemplate']);
    Route::put('/templates/{template}', [NotificationApiController::class, 'updateTemplate']);
    
    // Customer communication preferences
    Route::get('/preferences/{customer}', [NotificationApiController::class, 'getCustomerPreferences']);
    Route::put('/preferences/{customer}', [NotificationApiController::class, 'updateCustomerPreferences']);
});