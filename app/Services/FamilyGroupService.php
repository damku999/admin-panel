<?php

namespace App\Services;

use App\Contracts\Repositories\FamilyGroupRepositoryInterface;
use App\Contracts\Services\FamilyGroupServiceInterface;
use App\Models\FamilyGroup;
use App\Models\FamilyMember;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

/**
 * Family Group Service
 *
 * Handles FamilyGroup business logic including member management,
 * password setup, and family relationship operations.
 * Inherits transaction management from BaseService.
 */
class FamilyGroupService extends BaseService implements FamilyGroupServiceInterface
{
    /**
     * Family Group Repository instance
     *
     * @var FamilyGroupRepositoryInterface
     */
    private FamilyGroupRepositoryInterface $familyGroupRepository;

    /**
     * Constructor
     *
     * @param FamilyGroupRepositoryInterface $familyGroupRepository
     */
    public function __construct(FamilyGroupRepositoryInterface $familyGroupRepository)
    {
        $this->familyGroupRepository = $familyGroupRepository;
    }

    /**
     * Get paginated list of family groups with filters
     */
    public function getFamilyGroups(Request $request): LengthAwarePaginator
    {
        return $this->familyGroupRepository->getFamilyGroupsWithFilters($request);
    }

    /**
     * Get family group with all relationships loaded
     */
    public function getFamilyGroupWithMembers(int $familyGroupId): ?FamilyGroup
    {
        return $this->familyGroupRepository->getFamilyGroupWithMembers($familyGroupId);
    }

    /**
     * Create a new family group with family head and members
     */
    public function createFamilyGroup(array $data): FamilyGroup
    {
        return $this->createInTransaction(function () use ($data) {
            // Create family group
            $familyGroup = $this->familyGroupRepository->create([
                'name' => $data['name'],
                'family_head_id' => $data['family_head_id'],
                'status' => $data['status'] ?? true,
                'created_by' => auth()->id(),
            ]);

            // Update family head's family_group_id
            Customer::where('id', $data['family_head_id'])
                ->update(['family_group_id' => $familyGroup->id]);

            // Set up passwords for all family members
            $passwordNotifications = [];

            // Setup family head password
            $familyHead = Customer::find($data['family_head_id']);

            // Use admin-provided password or generate one
            $customPassword = !empty($data['family_head_password']) ? $data['family_head_password'] : null;
            $forcePasswordChange = $data['force_password_change'] ?? true;

            if ($customPassword || !$familyHead->hasVerifiedEmail() || $familyHead->needsPasswordChange()) {
                $password = $customPassword ? $familyHead->setCustomPassword($customPassword, $forcePasswordChange) : $familyHead->setDefaultPassword();
                $passwordNotifications[] = [
                    'customer' => $familyHead,
                    'password' => $password,
                    'is_head' => true,
                    'admin_set' => !empty($customPassword)
                ];
            }

            // Add family head as member
            FamilyMember::create([
                'family_group_id' => $familyGroup->id,
                'customer_id' => $data['family_head_id'],
                'relationship' => 'head',
                'is_head' => true,
                'status' => true,
            ]);

            // Add other family members if provided
            if (!empty($data['member_ids'])) {
                foreach ($data['member_ids'] as $index => $memberId) {
                    if ($memberId != $data['family_head_id']) {
                        Customer::where('id', $memberId)
                            ->update(['family_group_id' => $familyGroup->id]);

                        // Setup password for family member
                        $familyMember = Customer::find($memberId);
                        if (!$familyMember->hasVerifiedEmail() || $familyMember->needsPasswordChange()) {
                            $password = $familyMember->setDefaultPassword();
                            $passwordNotifications[] = [
                                'customer' => $familyMember,
                                'password' => $password,
                                'is_head' => false
                            ];
                        }

                        FamilyMember::create([
                            'family_group_id' => $familyGroup->id,
                            'customer_id' => $memberId,
                            'relationship' => $data['relationships'][$index] ?? null,
                            'is_head' => false,
                            'status' => true,
                        ]);
                    }
                }
            }

            // Send password notifications after successful commit
            $this->sendPasswordNotifications($passwordNotifications, $familyGroup);

            Log::info('Family group created successfully', [
                'family_group_id' => $familyGroup->id,
                'family_head_id' => $data['family_head_id'],
                'members_count' => count($data['member_ids'] ?? []),
                'user_id' => auth()->id(),
            ]);

            return $familyGroup;
        });
    }

    /**
     * Update an existing family group
     */
    public function updateFamilyGroup(FamilyGroup $familyGroup, array $data): bool
    {
        return $this->updateInTransaction(function () use ($familyGroup, $data) {
            // Update basic family group data
            $updated = $this->familyGroupRepository->update($familyGroup, [
                'name' => $data['name'],
                'status' => $data['status'] ?? $familyGroup->status,
            ]);

            // Handle family head change if needed
            if (isset($data['family_head_id']) && $data['family_head_id'] != $familyGroup->family_head_id) {
                $this->changeFamilyHead($familyGroup->id, $data['family_head_id']);
            }

            // Handle member updates if provided
            if (isset($data['member_ids'])) {
                $this->updateFamilyMembers($familyGroup, $data['member_ids'], $data['relationships'] ?? []);
            }

            if ($updated) {
                Log::info('Family group updated successfully', [
                    'family_group_id' => $familyGroup->id,
                    'user_id' => auth()->id(),
                ]);
            }

            return $updated;
        });
    }

    /**
     * Delete a family group and handle member cleanup
     */
    public function deleteFamilyGroup(FamilyGroup $familyGroup): bool
    {
        return $this->deleteInTransaction(function () use ($familyGroup) {
            // Remove all family members
            FamilyMember::where('family_group_id', $familyGroup->id)->delete();

            // Update all customers to remove family_group_id
            Customer::where('family_group_id', $familyGroup->id)
                ->update(['family_group_id' => null]);

            // Delete the family group
            $deleted = $this->familyGroupRepository->delete($familyGroup);

            if ($deleted) {
                Log::info('Family group deleted successfully', [
                    'family_group_id' => $familyGroup->id,
                    'user_id' => auth()->id(),
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Update family group status
     */
    public function updateFamilyGroupStatus(int $familyGroupId, bool $status): bool
    {
        try {
            $updated = $this->familyGroupRepository->updateStatus($familyGroupId, $status);

            if ($updated) {
                Log::info('Family group status updated', [
                    'family_group_id' => $familyGroupId,
                    'status' => $status,
                    'user_id' => auth()->id(),
                ]);
            }

            return $updated;

        } catch (\Exception $e) {
            Log::error('Failed to update family group status', [
                'family_group_id' => $familyGroupId,
                'status' => $status,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            throw $e;
        }
    }

    /**
     * Add a new member to family group
     */
    public function addFamilyMember(int $familyGroupId, array $memberData): FamilyMember
    {
        return $this->createInTransaction(function () use ($familyGroupId, $memberData) {
            // Update customer's family_group_id
            Customer::where('id', $memberData['customer_id'])
                ->update(['family_group_id' => $familyGroupId]);

            // Create family member record
            $familyMember = FamilyMember::create([
                'family_group_id' => $familyGroupId,
                'customer_id' => $memberData['customer_id'],
                'relationship' => $memberData['relationship'] ?? null,
                'is_head' => false,
                'status' => $memberData['status'] ?? true,
            ]);

            // Setup password if needed
            $customer = Customer::find($memberData['customer_id']);
            if (!$customer->hasVerifiedEmail() || $customer->needsPasswordChange()) {
                $customer->setDefaultPassword();
            }

            return $familyMember;
        });
    }

    /**
     * Remove a member from family group
     */
    public function removeFamilyMember(int $familyGroupId, int $memberId): bool
    {
        return $this->executeInTransaction(function () use ($familyGroupId, $memberId) {
            // Check if member is family head
            $familyMember = FamilyMember::where('family_group_id', $familyGroupId)
                ->where('customer_id', $memberId)
                ->first();

            if ($familyMember && $familyMember->is_head) {
                throw new \Exception('Cannot remove family head. Please change family head first.');
            }

            return $this->familyGroupRepository->removeCustomerFromFamilyGroup($memberId);
        });
    }

    /**
     * Update family member relationship
     */
    public function updateFamilyMember(int $familyMemberId, array $data): bool
    {
        $familyMember = FamilyMember::findOrFail($familyMemberId);

        return $familyMember->update([
            'relationship' => $data['relationship'] ?? $familyMember->relationship,
            'status' => $data['status'] ?? $familyMember->status,
        ]);
    }

    /**
     * Change family head
     */
    public function changeFamilyHead(int $familyGroupId, int $newFamilyHeadId): bool
    {
        return $this->executeInTransaction(function () use ($familyGroupId, $newFamilyHeadId) {
            return $this->familyGroupRepository->updateFamilyHead($familyGroupId, $newFamilyHeadId);
        });
    }

    /**
     * Setup passwords for family members
     */
    public function setupMemberPasswords(array $memberIds, bool $forceChange = true): array
    {
        $passwordNotifications = [];

        foreach ($memberIds as $memberId) {
            $customer = Customer::find($memberId);
            if ($customer && (!$customer->hasVerifiedEmail() || $customer->needsPasswordChange())) {
                $password = $customer->setDefaultPassword();
                $passwordNotifications[] = [
                    'customer' => $customer,
                    'password' => $password,
                    'is_head' => false
                ];
            }
        }

        return $passwordNotifications;
    }

    /**
     * Send password notifications to family members
     */
    public function sendPasswordNotifications(array $passwordNotifications, FamilyGroup $familyGroup): bool
    {
        try {
            foreach ($passwordNotifications as $notification) {
                // Send WhatsApp notification if enabled
                if ($notification['customer']->mobile_number) {
                    // Implementation would depend on WhatsApp service
                    Log::info('Password notification sent', [
                        'customer_id' => $notification['customer']->id,
                        'family_group_id' => $familyGroup->id,
                        'is_head' => $notification['is_head'],
                    ]);
                }
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send password notifications', [
                'family_group_id' => $familyGroup->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get available customers for family group
     */
    public function getAvailableCustomers(?int $familyGroupId = null): \Illuminate\Database\Eloquent\Collection
    {
        if ($familyGroupId) {
            return $this->familyGroupRepository->getAvailableCustomersForEdit($familyGroupId);
        }

        return $this->familyGroupRepository->getAvailableCustomers();
    }

    /**
     * Cleanup orphaned family member records
     */
    public function cleanupOrphanedRecords(): int
    {
        $orphanedCount = FamilyMember::whereNotExists(function ($query) {
            $query->select(\DB::raw(1))
                  ->from('family_groups')
                  ->whereRaw('family_groups.id = family_members.family_group_id');
        })->count();

        FamilyMember::whereNotExists(function ($query) {
            $query->select(\DB::raw(1))
                  ->from('family_groups')
                  ->whereRaw('family_groups.id = family_members.family_group_id');
        })->delete();

        if ($orphanedCount > 0) {
            Log::info('Cleaned up orphaned family member records', [
                'count' => $orphanedCount,
                'user_id' => auth()->id(),
            ]);
        }

        return $orphanedCount;
    }

    /**
     * Get family group statistics
     */
    public function getFamilyGroupStatistics(): array
    {
        try {
            return $this->familyGroupRepository->getFamilyGroupStatistics();
        } catch (\Exception $e) {
            Log::error('Failed to get family group statistics', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return [];
        }
    }

    /**
     * Update family members for a family group
     *
     * @param FamilyGroup $familyGroup
     * @param array $memberIds
     * @param array $relationships
     * @return void
     */
    private function updateFamilyMembers(FamilyGroup $familyGroup, array $memberIds, array $relationships = []): void
    {
        // Get current members (excluding head)
        $currentMembers = $familyGroup->familyMembers()
            ->where('is_head', false)
            ->pluck('customer_id')
            ->toArray();

        // Remove members no longer in the list
        $membersToRemove = array_diff($currentMembers, $memberIds);
        foreach ($membersToRemove as $memberId) {
            $this->familyGroupRepository->removeCustomerFromFamilyGroup($memberId);
        }

        // Add new members
        $membersToAdd = array_diff($memberIds, $currentMembers);
        foreach ($membersToAdd as $index => $memberId) {
            if ($memberId != $familyGroup->family_head_id) {
                $this->addFamilyMember($familyGroup->id, [
                    'customer_id' => $memberId,
                    'relationship' => $relationships[$index] ?? null,
                ]);
            }
        }
    }

    /**
     * Get all family groups for export
     */
    public function getAllFamilyGroupsForExport(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->familyGroupRepository->getAllFamilyGroupsWithRelationships();
    }

    /**
     * Remove a specific family member by FamilyMember object
     */
    public function removeFamilyMemberByObject(FamilyMember $familyMember): bool
    {
        return $this->executeInTransaction(function () use ($familyMember) {
            // Prevent removing family head
            if ($familyMember->is_head) {
                throw new \Exception('Cannot remove family head. Please change family head first or delete the entire family group.');
            }

            $customerId = $familyMember->customer_id;

            // Reset customer data when removing from family
            Customer::where('id', $customerId)->update([
                'family_group_id' => null,
                'password' => null,
                'email_verified_at' => null,
                'password_reset_sent_at' => null
            ]);

            // Delete the family member record
            return $familyMember->delete();
        });
    }
}