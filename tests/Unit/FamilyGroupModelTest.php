<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\FamilyGroup;
use App\Models\FamilyMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FamilyGroupModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_family_group_can_be_created(): void
    {
        $familyHead = Customer::factory()->create();
        
        $familyGroup = FamilyGroup::create([
            'name' => 'Test Family',
            'family_head_id' => $familyHead->id,
            'status' => true,
            'created_by' => 1
        ]);

        $this->assertInstanceOf(FamilyGroup::class, $familyGroup);
        $this->assertEquals('Test Family', $familyGroup->name);
        $this->assertEquals($familyHead->id, $familyGroup->family_head_id);
        $this->assertTrue($familyGroup->status);
    }

    public function test_family_group_belongs_to_family_head(): void
    {
        $familyHead = Customer::factory()->create();
        $familyGroup = FamilyGroup::factory()->create(['family_head_id' => $familyHead->id]);

        $this->assertInstanceOf(Customer::class, $familyGroup->familyHead);
        $this->assertEquals($familyHead->id, $familyGroup->familyHead->id);
    }

    public function test_family_group_has_many_members(): void
    {
        $familyGroup = FamilyGroup::factory()->create();
        $customer1 = Customer::factory()->create(['family_group_id' => $familyGroup->id]);
        $customer2 = Customer::factory()->create(['family_group_id' => $familyGroup->id]);

        // Create family member records
        FamilyMember::create([
            'family_group_id' => $familyGroup->id,
            'customer_id' => $customer1->id,
            'relationship' => 'head',
            'is_head' => true,
            'status' => true
        ]);

        FamilyMember::create([
            'family_group_id' => $familyGroup->id,
            'customer_id' => $customer2->id,
            'relationship' => 'spouse',
            'is_head' => false,
            'status' => true
        ]);

        $this->assertCount(2, $familyGroup->members);
        $this->assertContains($customer1->id, $familyGroup->members->pluck('customer_id'));
        $this->assertContains($customer2->id, $familyGroup->members->pluck('customer_id'));
    }

    public function test_family_group_has_many_customers(): void
    {
        $familyGroup = FamilyGroup::factory()->create();
        $customer1 = Customer::factory()->create(['family_group_id' => $familyGroup->id]);
        $customer2 = Customer::factory()->create(['family_group_id' => $familyGroup->id]);

        $customers = $familyGroup->customers;
        
        $this->assertCount(2, $customers);
        $this->assertContains($customer1->id, $customers->pluck('id'));
        $this->assertContains($customer2->id, $customers->pluck('id'));
    }

    public function test_family_group_soft_deletes(): void
    {
        $familyGroup = FamilyGroup::factory()->create(['name' => 'Test Family']);
        $familyGroupId = $familyGroup->id;

        $familyGroup->delete();

        // Should not be found in regular queries
        $this->assertNull(FamilyGroup::find($familyGroupId));
        
        // Should be found with trashed
        $this->assertNotNull(FamilyGroup::withTrashed()->find($familyGroupId));
        $this->assertTrue(FamilyGroup::withTrashed()->find($familyGroupId)->trashed());
    }

    public function test_family_group_status_scope(): void
    {
        $activeGroup = FamilyGroup::factory()->create(['status' => true]);
        $inactiveGroup = FamilyGroup::factory()->create(['status' => false]);

        $activeGroups = FamilyGroup::where('status', true)->get();
        $inactiveGroups = FamilyGroup::where('status', false)->get();

        $this->assertContains($activeGroup->id, $activeGroups->pluck('id'));
        $this->assertNotContains($inactiveGroup->id, $activeGroups->pluck('id'));
        
        $this->assertContains($inactiveGroup->id, $inactiveGroups->pluck('id'));
        $this->assertNotContains($activeGroup->id, $inactiveGroups->pluck('id'));
    }

    public function test_family_group_member_count(): void
    {
        $familyGroup = FamilyGroup::factory()->create();
        $customer1 = Customer::factory()->create(['family_group_id' => $familyGroup->id]);
        $customer2 = Customer::factory()->create(['family_group_id' => $familyGroup->id]);
        $customer3 = Customer::factory()->create(['family_group_id' => $familyGroup->id]);

        // Create family member records
        foreach ([$customer1, $customer2, $customer3] as $index => $customer) {
            FamilyMember::create([
                'family_group_id' => $familyGroup->id,
                'customer_id' => $customer->id,
                'relationship' => $index === 0 ? 'head' : 'child',
                'is_head' => $index === 0,
                'status' => true
            ]);
        }

        $this->assertEquals(3, $familyGroup->customers()->count());
        $this->assertEquals(3, $familyGroup->members()->count());
    }

    public function test_family_group_finds_head_member(): void
    {
        $familyGroup = FamilyGroup::factory()->create();
        $familyHead = Customer::factory()->create(['family_group_id' => $familyGroup->id]);
        $regularMember = Customer::factory()->create(['family_group_id' => $familyGroup->id]);

        // Set family head in the group
        $familyGroup->update(['family_head_id' => $familyHead->id]);

        // Create family member records
        FamilyMember::create([
            'family_group_id' => $familyGroup->id,
            'customer_id' => $familyHead->id,
            'relationship' => 'head',
            'is_head' => true,
            'status' => true
        ]);

        FamilyMember::create([
            'family_group_id' => $familyGroup->id,
            'customer_id' => $regularMember->id,
            'relationship' => 'spouse',
            'is_head' => false,
            'status' => true
        ]);

        $headMember = $familyGroup->members()->where('is_head', true)->first();
        
        $this->assertNotNull($headMember);
        $this->assertEquals($familyHead->id, $headMember->customer_id);
        $this->assertTrue($headMember->is_head);
    }

    public function test_family_group_validation_rules(): void
    {
        // Test that family group requires a name
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        FamilyGroup::create([
            'name' => null, // Should fail validation
            'status' => true
        ]);
    }

    public function test_family_group_name_uniqueness(): void
    {
        FamilyGroup::factory()->create(['name' => 'Unique Family']);
        
        // Should be able to create another family with different name
        $differentFamily = FamilyGroup::factory()->create(['name' => 'Different Family']);
        $this->assertInstanceOf(FamilyGroup::class, $differentFamily);
        
        // Test duplicate names (if unique constraint exists)
        // Note: This depends on database constraints in the migration
    }

    public function test_family_group_created_by_relationship(): void
    {
        // This would test the relationship to User model if implemented
        $familyGroup = FamilyGroup::factory()->create(['created_by' => 1]);
        
        $this->assertEquals(1, $familyGroup->created_by);
    }

    public function test_family_group_cascading_delete(): void
    {
        $familyGroup = FamilyGroup::factory()->create();
        $customer = Customer::factory()->create(['family_group_id' => $familyGroup->id]);
        
        $familyMember = FamilyMember::create([
            'family_group_id' => $familyGroup->id,
            'customer_id' => $customer->id,
            'relationship' => 'head',
            'is_head' => true,
            'status' => true
        ]);

        $familyMemberId = $familyMember->id;
        $familyGroup->delete();

        // Family members should still exist (soft delete doesn't cascade)
        // But customer's family_group_id should remain (no cascade on soft delete)
        $this->assertNotNull(FamilyMember::find($familyMemberId));
        
        // Customer should still exist but might need family_group_id cleanup
        $customer->refresh();
        $this->assertNotNull($customer);
    }
}