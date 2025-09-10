<?php

namespace App\Events\Communication;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WhatsAppMessageQueued
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $phoneNumber;
    public string $message;
    public string $messageType;
    public ?string $mediaUrl;
    public array $templateData;
    public int $priority;
    public ?string $referenceId;
    public ?int $customerId;

    public function __construct(
        string $phoneNumber,
        string $message,
        string $messageType = 'text',
        ?string $mediaUrl = null,
        array $templateData = [],
        int $priority = 5,
        ?string $referenceId = null,
        ?int $customerId = null
    ) {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
        $this->messageType = $messageType; // text, document, template
        $this->mediaUrl = $mediaUrl;
        $this->templateData = $templateData;
        $this->priority = $priority; // 1 (highest) to 10 (lowest)
        $this->referenceId = $referenceId;
        $this->customerId = $customerId;
    }

    public function getEventData(): array
    {
        return [
            'phone_number' => $this->phoneNumber,
            'message' => $this->message,
            'message_type' => $this->messageType,
            'media_url' => $this->mediaUrl,
            'template_data' => $this->templateData,
            'priority' => $this->priority,
            'reference_id' => $this->referenceId,
            'customer_id' => $this->customerId,
            'queued_at' => now()->format('Y-m-d H:i:s'),
            'source_ip' => request()->ip(),
        ];
    }

    public function isDocument(): bool
    {
        return $this->messageType === 'document' && !empty($this->mediaUrl);
    }

    public function isTemplate(): bool
    {
        return $this->messageType === 'template';
    }

    public function isHighPriority(): bool
    {
        return $this->priority <= 3;
    }

    public function shouldQueue(): bool
    {
        return true;
    }

    public function getQueueName(): string
    {
        return $this->isHighPriority() ? 'whatsapp-priority' : 'whatsapp-normal';
    }
}