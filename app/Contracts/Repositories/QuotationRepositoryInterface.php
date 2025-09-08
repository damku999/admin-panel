<?php

namespace App\Contracts\Repositories;

use App\Models\Quotation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface QuotationRepositoryInterface
{
    /**
     * Get all quotations with optional filters.
     */
    public function getAll(array $filters = []): Collection;

    /**
     * Get paginated quotations with search and filters.
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Find quotation by ID with relationships.
     */
    public function findById(int $id): ?Quotation;

    /**
     * Create a new quotation.
     */
    public function create(array $data): Quotation;

    /**
     * Update quotation by ID.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete quotation by ID.
     */
    public function delete(int $id): bool;

    /**
     * Get quotations by customer ID.
     */
    public function getByCustomer(int $customerId): Collection;

    /**
     * Get quotations by status.
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get recent quotations.
     */
    public function getRecent(int $limit = 10): Collection;

    /**
     * Search quotations by vehicle number or customer details.
     */
    public function search(string $query): Collection;

    /**
     * Get quotations sent via WhatsApp.
     */
    public function getSentQuotations(): Collection;

    /**
     * Get quotations pending to be sent.
     */
    public function getPendingQuotations(): Collection;

    /**
     * Get quotation count by status.
     */
    public function getCountByStatus(): array;

    /**
     * Check if quotation exists.
     */
    public function exists(int $id): bool;
}