<?php

namespace App\Listeners\Communication;

use App\Events\Communication\WhatsAppMessageQueued;
use App\Traits\WhatsAppApiTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessWhatsAppMessage implements ShouldQueue
{
    use InteractsWithQueue, WhatsAppApiTrait;

    public $tries = 3;
    public $maxExceptions = 2;
    public $backoff = [60, 300, 900]; // 1 min, 5 min, 15 min

    public function handle(WhatsAppMessageQueued $event): void
    {
        try {
            $this->sendWhatsAppMessage($event);
            $this->logSuccess($event);
        } catch (\Exception $e) {
            $this->logFailure($event, $e);
            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    private function sendWhatsAppMessage(WhatsAppMessageQueued $event): void
    {
        $eventData = $event->getEventData();
        
        if ($event->isDocument()) {
            // Send document message
            $this->sendDocument(
                $event->phoneNumber,
                $event->mediaUrl,
                $event->message
            );
        } elseif ($event->isTemplate()) {
            // Send template message
            $this->sendTemplateMessage(
                $event->phoneNumber,
                $event->message,
                $event->templateData
            );
        } else {
            // Send plain text message
            $this->sendTextMessage(
                $event->phoneNumber,
                $event->message
            );
        }
    }

    private function sendTextMessage(string $phoneNumber, string $message): void
    {
        // Use existing WhatsAppApiTrait method
        $response = $this->sendWhatsApp($phoneNumber, $message);
        
        if (!$response || !$this->isSuccessfulResponse($response)) {
            throw new \Exception('WhatsApp API call failed: ' . json_encode($response));
        }
    }

    private function sendTemplateMessage(string $phoneNumber, string $message, array $templateData): void
    {
        // Replace template variables in message
        $processedMessage = $this->processTemplate($message, $templateData);
        $this->sendTextMessage($phoneNumber, $processedMessage);
    }

    private function sendDocument(string $phoneNumber, string $mediaUrl, string $message): void
    {
        // Use existing document sending capability if available
        // Otherwise, send the document URL as a text message
        $documentMessage = $message . "\n\nDocument: " . $mediaUrl;
        $this->sendTextMessage($phoneNumber, $documentMessage);
    }

    private function processTemplate(string $message, array $templateData): string
    {
        $processedMessage = $message;
        
        foreach ($templateData as $key => $value) {
            $processedMessage = str_replace("{{$key}}", $value, $processedMessage);
        }
        
        return $processedMessage;
    }

    private function isSuccessfulResponse($response): bool
    {
        // Implement based on your WhatsApp API response format
        return isset($response['success']) && $response['success'] === true;
    }

    private function logSuccess(WhatsAppMessageQueued $event): void
    {
        \Log::info('WhatsApp message sent successfully', [
            'phone_number' => $event->phoneNumber,
            'message_type' => $event->messageType,
            'reference_id' => $event->referenceId,
            'customer_id' => $event->customerId,
            'priority' => $event->priority,
        ]);
    }

    private function logFailure(WhatsAppMessageQueued $event, \Throwable $exception): void
    {
        \Log::error('WhatsApp message failed', [
            'phone_number' => $event->phoneNumber,
            'message_type' => $event->messageType,
            'reference_id' => $event->referenceId,
            'customer_id' => $event->customerId,
            'error' => $exception->getMessage(),
            'attempt' => $this->attempts(),
        ]);
    }

    public function failed(WhatsAppMessageQueued $event, \Throwable $exception): void
    {
        \Log::error('WhatsApp message permanently failed', [
            'phone_number' => $event->phoneNumber,
            'reference_id' => $event->referenceId,
            'customer_id' => $event->customerId,
            'error' => $exception->getMessage(),
            'final_attempt' => true,
        ]);
    }
}