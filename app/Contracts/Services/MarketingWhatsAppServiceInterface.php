<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

/**
 * Marketing WhatsApp Service Interface
 *
 * Defines business logic operations for WhatsApp marketing.
 * Handles customer selection, message sending, and campaign management.
 */
interface MarketingWhatsAppServiceInterface
{
    /**
     * Get active customers for WhatsApp marketing
     *
     * @return Collection
     */
    public function getActiveCustomers(): Collection;

    /**
     * Get customers with valid mobile numbers for marketing
     *
     * @param string $recipients ('all' or 'selected')
     * @param array $selectedCustomerIds
     * @return Collection
     */
    public function getValidCustomersForMarketing(string $recipients, array $selectedCustomerIds = []): Collection;

    /**
     * Send WhatsApp marketing campaign to customers
     *
     * @param array $campaignData
     * @return array Campaign results
     */
    public function sendMarketingCampaign(array $campaignData): array;

    /**
     * Preview customer list for marketing campaign
     *
     * @param string $recipients
     * @param array $selectedCustomerIds
     * @return array
     */
    public function previewCustomerList(string $recipients, array $selectedCustomerIds = []): array;

    /**
     * Send text message to customer
     *
     * @param string $message
     * @param string $mobileNumber
     * @param int $customerId
     * @return bool
     */
    public function sendTextMessage(string $message, string $mobileNumber, int $customerId): bool;

    /**
     * Send image message to customer
     *
     * @param string $message
     * @param string $mobileNumber
     * @param string $imagePath
     * @param int $customerId
     * @return bool
     */
    public function sendImageMessage(string $message, string $mobileNumber, string $imagePath, int $customerId): bool;

    /**
     * Get marketing campaign statistics
     *
     * @return array
     */
    public function getMarketingStatistics(): array;

    /**
     * Log marketing message attempt
     *
     * @param int $customerId
     * @param string $messageType
     * @param bool $success
     * @param string|null $error
     * @return void
     */
    public function logMarketingAttempt(int $customerId, string $messageType, bool $success, ?string $error = null): void;
}