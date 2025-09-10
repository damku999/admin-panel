<?php

namespace App\Listeners\Communication;

use App\Events\Communication\EmailQueued;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class ProcessEmailMessage implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;
    public $maxExceptions = 2;
    public $backoff = [60, 300, 900]; // 1 min, 5 min, 15 min

    public function handle(EmailQueued $event): void
    {
        try {
            $this->sendEmail($event);
            $this->logSuccess($event);
        } catch (\Exception $e) {
            $this->logFailure($event, $e);
            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    private function sendEmail(EmailQueued $event): void
    {
        $viewName = $this->getEmailView($event->emailType);
        
        Mail::send($viewName, $event->emailData, function ($message) use ($event) {
            $message->to($event->recipientEmail, $event->recipientName)
                   ->subject($event->subject);
            
            // Add attachments if present
            foreach ($event->attachments as $attachment) {
                if (isset($attachment['path'])) {
                    $message->attach(
                        $attachment['path'],
                        [
                            'as' => $attachment['name'] ?? basename($attachment['path']),
                            'mime' => $attachment['mime'] ?? null,
                        ]
                    );
                }
            }
            
            // Set appropriate headers
            $message->getHeaders()
                   ->addTextHeader('X-Customer-ID', (string) $event->customerId)
                   ->addTextHeader('X-Reference-ID', (string) $event->referenceId)
                   ->addTextHeader('X-Email-Type', $event->emailType)
                   ->addTextHeader('X-Priority', (string) $event->priority);
        });
    }

    private function getEmailView(string $emailType): string
    {
        $viewMap = [
            'welcome' => 'emails.customer.welcome',
            'verification' => 'emails.customer.verification',
            'password_reset' => 'emails.customer.password_reset',
            'quotation' => 'emails.customer.quotation',
            'policy_document' => 'emails.customer.policy_document',
            'renewal_reminder' => 'emails.customer.renewal_reminder',
            'admin_notification' => 'emails.admin.notification',
        ];

        return $viewMap[$emailType] ?? 'emails.default';
    }

    private function logSuccess(EmailQueued $event): void
    {
        \Log::info('Email sent successfully', [
            'recipient_email' => $event->recipientEmail,
            'email_type' => $event->emailType,
            'subject' => $event->subject,
            'reference_id' => $event->referenceId,
            'customer_id' => $event->customerId,
            'has_attachments' => $event->hasAttachments(),
        ]);
    }

    private function logFailure(EmailQueued $event, \Throwable $exception): void
    {
        \Log::error('Email sending failed', [
            'recipient_email' => $event->recipientEmail,
            'email_type' => $event->emailType,
            'subject' => $event->subject,
            'reference_id' => $event->referenceId,
            'customer_id' => $event->customerId,
            'error' => $exception->getMessage(),
            'attempt' => $this->attempts(),
        ]);
    }

    public function failed(EmailQueued $event, \Throwable $exception): void
    {
        \Log::error('Email permanently failed', [
            'recipient_email' => $event->recipientEmail,
            'email_type' => $event->emailType,
            'subject' => $event->subject,
            'reference_id' => $event->referenceId,
            'customer_id' => $event->customerId,
            'error' => $exception->getMessage(),
            'final_attempt' => true,
        ]);
    }
}