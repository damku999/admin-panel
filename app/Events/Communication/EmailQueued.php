<?php

namespace App\Events\Communication;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailQueued
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $recipientEmail;
    public ?string $recipientName;
    public string $subject;
    public string $emailType;
    public array $emailData;
    public array $attachments;
    public int $priority;
    public ?string $referenceId;
    public ?int $customerId;

    public function __construct(
        string $recipientEmail,
        ?string $recipientName,
        string $subject,
        string $emailType,
        array $emailData = [],
        array $attachments = [],
        int $priority = 5,
        ?string $referenceId = null,
        ?int $customerId = null
    ) {
        $this->recipientEmail = $recipientEmail;
        $this->recipientName = $recipientName;
        $this->subject = $subject;
        $this->emailType = $emailType; // verification, quotation, policy, renewal, welcome, etc.
        $this->emailData = $emailData;
        $this->attachments = $attachments;
        $this->priority = $priority; // 1 (highest) to 10 (lowest)
        $this->referenceId = $referenceId;
        $this->customerId = $customerId;
    }

    public function getEventData(): array
    {
        return [
            'recipient_email' => $this->recipientEmail,
            'recipient_name' => $this->recipientName ?? 'Unknown',
            'subject' => $this->subject,
            'email_type' => $this->emailType,
            'email_data' => $this->emailData,
            'attachments' => $this->attachments,
            'priority' => $this->priority,
            'reference_id' => $this->referenceId,
            'customer_id' => $this->customerId,
            'queued_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    public function isTransactional(): bool
    {
        $transactionalTypes = ['verification', 'password_reset', 'policy_document', 'quotation'];
        return in_array($this->emailType, $transactionalTypes);
    }

    public function isHighPriority(): bool
    {
        return $this->priority <= 3 || $this->isTransactional();
    }

    public function shouldQueue(): bool
    {
        return true;
    }

    public function getQueueName(): string
    {
        return $this->isHighPriority() ? 'email-priority' : 'email-normal';
    }
}