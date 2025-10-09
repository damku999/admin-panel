<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Templated Notification Mailable
 *
 * Sends notification emails with templated content.
 * Supports HTML content and file attachments.
 */
class TemplatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $subject,
        public string $htmlContent,
        public array $attachments = []
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Get email settings from app settings
        $fromEmail = $this->getEmailFromAddress();
        $fromName = $this->getEmailFromName();

        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($fromEmail, $fromName),
            replyTo: [
                new \Illuminate\Mail\Mailables\Address($this->getEmailReplyTo(), $fromName),
            ],
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.templated-notification',
            with: [
                'htmlContent' => $this->htmlContent,
                'companyName' => company_name(),
                'companyWebsite' => company_website(),
                'companyPhone' => company_phone(),
                'companyAdvisor' => company_advisor_name(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $attachmentObjects = [];

        foreach ($this->attachments as $filePath) {
            if (file_exists($filePath)) {
                $attachmentObjects[] = Attachment::fromPath($filePath);
            }
        }

        return $attachmentObjects;
    }

    /**
     * Get email from address from settings.
     */
    protected function getEmailFromAddress(): string
    {
        try {
            return app(\App\Services\AppSettingService::class)
                ->get('email_from_address', 'email', config('mail.from.address', 'noreply@example.com'));
        } catch (\Exception $e) {
            return config('mail.from.address', 'noreply@example.com');
        }
    }

    /**
     * Get email from name from settings.
     */
    protected function getEmailFromName(): string
    {
        try {
            return app(\App\Services\AppSettingService::class)
                ->get('email_from_name', 'email', company_name());
        } catch (\Exception $e) {
            return company_name();
        }
    }

    /**
     * Get email reply-to address from settings.
     */
    protected function getEmailReplyTo(): string
    {
        try {
            return app(\App\Services\AppSettingService::class)
                ->get('email_reply_to', 'email', $this->getEmailFromAddress());
        } catch (\Exception $e) {
            return $this->getEmailFromAddress();
        }
    }
}
