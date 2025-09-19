<?php

namespace App\Contracts\Services;

use App\Models\ReferenceUser;

interface ReferenceUserServiceInterface
{
    /**
     * Create a new reference user
     *
     * @param array $data
     * @return ReferenceUser
     * @throws \Throwable
     */
    public function createReferenceUser(array $data): ReferenceUser;

    /**
     * Update an existing reference user
     *
     * @param ReferenceUser $referenceUser
     * @param array $data
     * @return bool
     * @throws \Throwable
     */
    public function updateReferenceUser(ReferenceUser $referenceUser, array $data): bool;

    /**
     * Delete a reference user
     *
     * @param ReferenceUser $referenceUser
     * @return bool
     * @throws \Throwable
     */
    public function deleteReferenceUser(ReferenceUser $referenceUser): bool;

    /**
     * Update reference user status
     *
     * @param int $referenceUserId
     * @param int $status
     * @return bool
     * @throws \Throwable
     */
    public function updateStatus(int $referenceUserId, int $status): bool;
}