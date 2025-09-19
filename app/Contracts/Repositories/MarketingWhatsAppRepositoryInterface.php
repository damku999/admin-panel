<?php

namespace App\Contracts\Repositories;

use App\Models\MarketingWhatsApp;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Marketing WhatsApp Repository Interface
 *
 * Defines data access operations for MarketingWhatsApp entity.
 * Handles WhatsApp marketing message storage, retrieval, and filtering.
 */
interface MarketingWhatsAppRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get paginated marketing WhatsApp messages with filters
     *
     * @param Request $request
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getMarketingMessagesWithFilters(Request $request, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get marketing messages by status
     *
     * @param bool $status
     * @return Collection
     */
    public function getMarketingMessagesByStatus(bool $status): Collection;

    /**
     * Get marketing messages by type
     *
     * @param string $type
     * @return Collection
     */
    public function getMarketingMessagesByType(string $type): Collection;

    /**
     * Get marketing messages sent today
     *
     * @return Collection
     */
    public function getTodayMarketingMessages(): Collection;

    /**
     * Get marketing messages by date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getMarketingMessagesByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Get marketing message statistics
     *
     * @return array
     */
    public function getMarketingMessageStatistics(): array;

    /**
     * Search marketing messages by content
     *
     * @param string $searchTerm
     * @param int $limit
     * @return Collection
     */
    public function searchMarketingMessages(string $searchTerm, int $limit = 20): Collection;

    /**
     * Get marketing messages by phone number
     *
     * @param string $phoneNumber
     * @return Collection
     */
    public function getMarketingMessagesByPhoneNumber(string $phoneNumber): Collection;

    /**
     * Get failed marketing messages for retry
     *
     * @return Collection
     */
    public function getFailedMarketingMessages(): Collection;

    /**
     * Get all marketing messages for export
     *
     * @return Collection
     */
    public function getAllMarketingMessagesForExport(): Collection;

    /**
     * Mark message as sent
     *
     * @param MarketingWhatsApp $marketingMessage
     * @param string $messageId
     * @return bool
     */
    public function markMessageAsSent(MarketingWhatsApp $marketingMessage, string $messageId): bool;

    /**
     * Mark message as failed
     *
     * @param MarketingWhatsApp $marketingMessage
     * @param string $errorMessage
     * @return bool
     */
    public function markMessageAsFailed(MarketingWhatsApp $marketingMessage, string $errorMessage): bool;
}