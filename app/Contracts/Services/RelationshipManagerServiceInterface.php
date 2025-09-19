<?php

namespace App\Contracts\Services;

use App\Models\RelationshipManager;

interface RelationshipManagerServiceInterface
{
    /**
     * Create a new relationship manager
     *
     * @param array $data
     * @return RelationshipManager
     * @throws \Throwable
     */
    public function createRelationshipManager(array $data): RelationshipManager;

    /**
     * Update an existing relationship manager
     *
     * @param RelationshipManager $relationshipManager
     * @param array $data
     * @return bool
     * @throws \Throwable
     */
    public function updateRelationshipManager(RelationshipManager $relationshipManager, array $data): bool;

    /**
     * Delete a relationship manager
     *
     * @param RelationshipManager $relationshipManager
     * @return bool
     * @throws \Throwable
     */
    public function deleteRelationshipManager(RelationshipManager $relationshipManager): bool;

    /**
     * Update relationship manager status
     *
     * @param int $relationshipManagerId
     * @param int $status
     * @return bool
     * @throws \Throwable
     */
    public function updateStatus(int $relationshipManagerId, int $status): bool;
}