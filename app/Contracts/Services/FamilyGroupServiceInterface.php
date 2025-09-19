<?php

namespace App\Contracts\Services;

use App\Models\FamilyGroup;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Family Group Service Interface
 *
 * Defines business logic operations for FamilyGroup management.
 * Includes complex family relationship management, password setup, and notifications.
 */
interface FamilyGroupServiceInterface
{
    /**
     * Get paginated list of family groups with filters
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getFamilyGroups(Request $request): LengthAwarePaginator;

    /**
     * Get family group with all relationships loaded
     *
     * @param int $familyGroupId
     * @return FamilyGroup|null
     */
    public function getFamilyGroupWithMembers(int $familyGroupId): ?FamilyGroup;

    /**
     * Create a new family group with family head and members
     *
     * @param array $data
     * @return FamilyGroup
     */
    public function createFamilyGroup(array $data): FamilyGroup;

    /**
     * Update an existing family group
     *
     * @param FamilyGroup $familyGroup
     * @param array $data
     * @return bool
     */
    public function updateFamilyGroup(FamilyGroup $familyGroup, array $data): bool;

    /**
     * Delete a family group and handle member cleanup
     *
     * @param FamilyGroup $familyGroup
     * @return bool
     */
    public function deleteFamilyGroup(FamilyGroup $familyGroup): bool;

    /**
     * Update family group status
     *
     * @param int $familyGroupId
     * @param bool $status
     * @return bool
     */
    public function updateFamilyGroupStatus(int $familyGroupId, bool $status): bool;

    /**
     * Add a new member to family group
     *
     * @param int $familyGroupId
     * @param array $memberData
     * @return FamilyMember
     */
    public function addFamilyMember(int $familyGroupId, array $memberData): FamilyMember;

    /**
     * Remove a member from family group
     *
     * @param int $familyGroupId
     * @param int $memberId
     * @return bool
     */
    public function removeFamilyMember(int $familyGroupId, int $memberId): bool;

    /**
     * Remove a specific family member by FamilyMember object
     *
     * @param FamilyMember $familyMember
     * @return bool
     */
    public function removeFamilyMemberByObject(FamilyMember $familyMember): bool;

    /**
     * Update family member relationship
     *
     * @param int $familyMemberId
     * @param array $data
     * @return bool
     */
    public function updateFamilyMember(int $familyMemberId, array $data): bool;

    /**
     * Change family head
     *
     * @param int $familyGroupId
     * @param int $newFamilyHeadId
     * @return bool
     */
    public function changeFamilyHead(int $familyGroupId, int $newFamilyHeadId): bool;

    /**
     * Setup passwords for family members
     *
     * @param array $memberIds
     * @param bool $forceChange
     * @return array
     */
    public function setupMemberPasswords(array $memberIds, bool $forceChange = true): array;

    /**
     * Send password notifications to family members
     *
     * @param array $passwordNotifications
     * @param FamilyGroup $familyGroup
     * @return bool
     */
    public function sendPasswordNotifications(array $passwordNotifications, FamilyGroup $familyGroup): bool;

    /**
     * Get available customers for family group
     *
     * @param int|null $familyGroupId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableCustomers(?int $familyGroupId = null): \Illuminate\Database\Eloquent\Collection;

    /**
     * Cleanup orphaned family member records
     *
     * @return int Number of records cleaned up
     */
    public function cleanupOrphanedRecords(): int;

    /**
     * Get family group statistics
     *
     * @return array
     */
    public function getFamilyGroupStatistics(): array;

    /**
     * Get all family groups for export
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllFamilyGroupsForExport(): \Illuminate\Database\Eloquent\Collection;
}