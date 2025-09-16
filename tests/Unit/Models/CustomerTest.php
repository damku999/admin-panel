<?php

namespace Tests\Unit\Models;

use Tests\BaseTestCase;
use App\Models\Customer;
use App\Models\FamilyGroup;
use App\Models\FamilyMember;
use App\Models\CustomerInsurance;
use App\Models\Quotation;
use App\Models\Claim;
use App\Models\CustomerAuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerTest extends BaseTestCase
{
    use RefreshDatabase;

    // =============================================================================
    // BASIC MODEL TESTS
    // =============================================================================

    /** @test */
    public function customer_has_fillable_attributes(): void
    {
        $customer = new Customer();
        $expectedFillable = [
            'name', 'email', 'mobile_number', 'status', 'wedding_anniversary_date',
            'date_of_birth', 'engagement_anniversary_date', 'pan_card_number',
            'aadhar_card_number', 'gst_number', 'pan_card_path', 'aadhar_card_path',
            'gst_path', 'type', 'family_group_id', 'password', 'email_verified_at',
            'password_changed_at', 'must_change_password', 'email_verification_token',
            'password_reset_sent_at', 'password_reset_token', 'password_reset_expires_at'
        ];

        $this->assertEquals($expectedFillable, $customer->getFillable());
    }

    /** @test */
    public function customer_has_hidden_attributes(): void
    {
        $customer = new Customer();
        $expectedHidden = ['password', 'remember_token'];

        $this->assertEquals($expectedHidden, $customer->getHidden());
    }

    /** @test */
    public function customer_has_correct_casts(): void
    {
        $customer = new Customer();
        $expectedCasts = [
            'id' => 'int',
            'status' => 'boolean',
            'date_of_birth' => 'date',
            'wedding_anniversary_date' => 'date',
            'engagement_anniversary_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'must_change_password' => 'boolean',
            'password_reset_sent_at' => 'datetime',
            'password_reset_expires_at' => 'datetime',
        ];

        $this->assertEquals($expectedCasts, $customer->getCasts());
    }

    // =============================================================================
    // RELATIONSHIP TESTS
    // =============================================================================

    /** @test */
    public function customer_has_insurance_relationship(): void
    {
        $customer = Customer::factory()->create();
        CustomerInsurance::factory()->count(3)->create(['customer_id' => $customer->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $customer->insurance());
        $this->assertCount(3, $customer->insurance);
    }

    /** @test */
    public function customer_has_quotations_relationship(): void
    {
        $customer = Customer::factory()->create();
        Quotation::factory()->count(2)->create(['customer_id' => $customer->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $customer->quotations());
        $this->assertCount(2, $customer->quotations);
    }

    /** @test */
    public function customer_has_claims_relationship(): void
    {
        $customer = Customer::factory()->create();

        // Create customer insurance first
        $insurance = CustomerInsurance::factory()->create(['customer_id' => $customer->id]);

        // Create claims
        Claim::factory()->count(2)->create(['customer_id' => $customer->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $customer->claims());
        $this->assertCount(2, $customer->claims);
    }

    /** @test */
    public function customer_belongs_to_family_group(): void
    {
        $familyGroup = FamilyGroup::factory()->create();
        $customer = Customer::factory()->create(['family_group_id' => $familyGroup->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $customer->familyGroup());
        $this->assertEquals($familyGroup->id, $customer->familyGroup->id);
    }

    /** @test */
    public function customer_has_one_family_member_record(): void
    {
        $familyGroup = FamilyGroup::factory()->create();
        $customer = Customer::factory()->create(['family_group_id' => $familyGroup->id]);
        $familyMember = FamilyMember::factory()->create([
            'customer_id' => $customer->id,
            'family_group_id' => $familyGroup->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $customer->familyMember());
        $this->assertEquals($familyMember->id, $customer->familyMember->id);
    }

    /** @test */
    public function customer_has_audit_logs_relationship(): void
    {
        $customer = Customer::factory()->create();
        CustomerAuditLog::factory()->count(3)->create(['customer_id' => $customer->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $customer->auditLogs());
        $this->assertCount(3, $customer->auditLogs);
    }

    // =============================================================================
    // FAMILY FUNCTIONALITY TESTS
    // =============================================================================

    /** @test */
    public function customer_can_check_if_has_family(): void
    {
        $customerWithFamily = Customer::factory()->create(['family_group_id' => 1]);
        $customerWithoutFamily = Customer::factory()->create(['family_group_id' => null]);

        $this->assertTrue($customerWithFamily->hasFamily());
        $this->assertFalse($customerWithoutFamily->hasFamily());
    }

    /** @test */
    public function customer_can_check_if_is_family_head(): void
    {
        $family = $this->createFamilyStructure();

        $this->assertTrue($family['familyHead']->isFamilyHead());
        $this->assertFalse($family['spouse']->isFamilyHead());
        $this->assertFalse($family['child']->isFamilyHead());
    }

    /** @test */
    public function customer_without_family_is_not_family_head(): void
    {
        $customer = Customer::factory()->create(['family_group_id' => null]);
        $this->assertFalse($customer->isFamilyHead());
    }

    /** @test */
    public function customer_can_check_if_in_same_family_as_another_customer(): void
    {
        $family = $this->createFamilyStructure();
        $outsideCustomer = Customer::factory()->create();

        // Same family
        $this->assertTrue($family['familyHead']->isInSameFamilyAs($family['spouse']));
        $this->assertTrue($family['spouse']->isInSameFamilyAs($family['child']));

        // Different family
        $this->assertFalse($family['familyHead']->isInSameFamilyAs($outsideCustomer));
    }

    /** @test */
    public function family_head_can_view_all_family_insurance(): void
    {
        $family = $this->createFamilyStructure();

        // Create insurance for different family members
        CustomerInsurance::factory()->create(['customer_id' => $family['familyHead']->id]);
        CustomerInsurance::factory()->create(['customer_id' => $family['spouse']->id]);
        CustomerInsurance::factory()->create(['customer_id' => $family['child']->id]);

        $viewableInsurance = $family['familyHead']->getViewableInsurance()->get();
        $this->assertCount(3, $viewableInsurance);
    }

    /** @test */
    public function family_member_can_only_view_own_insurance(): void
    {
        $family = $this->createFamilyStructure();

        // Create insurance for different family members
        CustomerInsurance::factory()->create(['customer_id' => $family['familyHead']->id]);
        CustomerInsurance::factory()->create(['customer_id' => $family['spouse']->id]);
        CustomerInsurance::factory()->create(['customer_id' => $family['child']->id]);

        $viewableInsurance = $family['spouse']->getViewableInsurance()->get();
        $this->assertCount(1, $viewableInsurance);
        $this->assertEquals($family['spouse']->id, $viewableInsurance->first()->customer_id);
    }

    // =============================================================================
    // PRIVACY AND SECURITY TESTS
    // =============================================================================

    /** @test */
    public function customer_can_get_privacy_safe_data(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'mobile_number' => '9876543210',
            'date_of_birth' => '1990-05-15',
        ]);

        $safeData = $customer->getPrivacySafeData();

        $this->assertEquals('John Doe', $safeData['name']);
        $this->assertEquals('jo******@example.com', $safeData['email']);
        $this->assertEquals('98******10', $safeData['mobile_number']);
        $this->assertEquals('May 15', $safeData['date_of_birth']); // Year hidden
        $this->assertArrayHasKey('status', $safeData);
        $this->assertArrayHasKey('created_at', $safeData);
    }

    /** @test */
    public function customer_email_masking_works_correctly(): void
    {
        $customer = new Customer();

        // Test normal email
        $reflection = new \ReflectionClass($customer);
        $method = $reflection->getMethod('maskEmail');
        $method->setAccessible(true);

        $this->assertEquals('jo**@example.com', $method->invoke($customer, 'john@example.com'));
        $this->assertEquals('ab*********@test.org', $method->invoke($customer, 'abcdefghijk@test.org'));
        $this->assertEquals('a@x.com', $method->invoke($customer, 'a@x.com')); // Short email
        $this->assertNull($method->invoke($customer, null));
    }

    /** @test */
    public function customer_mobile_masking_works_correctly(): void
    {
        $customer = new Customer();

        // Test mobile masking
        $reflection = new \ReflectionClass($customer);
        $method = $reflection->getMethod('maskMobile');
        $method->setAccessible(true);

        $this->assertEquals('98******10', $method->invoke($customer, '9876543210'));
        $this->assertEquals('91*********99', $method->invoke($customer, '9198765432199'));
        $this->assertEquals('123', $method->invoke($customer, '123')); // Short number
        $this->assertNull($method->invoke($customer, null));
    }

    /** @test */
    public function customer_can_view_sensitive_data_of_self(): void
    {
        $customer = Customer::factory()->create();
        $this->assertTrue($customer->canViewSensitiveDataOf($customer));
    }

    /** @test */
    public function family_head_can_view_sensitive_data_of_family_members(): void
    {
        $family = $this->createFamilyStructure();

        $this->assertTrue($family['familyHead']->canViewSensitiveDataOf($family['spouse']));
        $this->assertTrue($family['familyHead']->canViewSensitiveDataOf($family['child']));
    }

    /** @test */
    public function family_member_cannot_view_sensitive_data_of_other_members(): void
    {
        $family = $this->createFamilyStructure();
        $outsideCustomer = Customer::factory()->create();

        $this->assertFalse($family['spouse']->canViewSensitiveDataOf($family['familyHead']));
        $this->assertFalse($family['child']->canViewSensitiveDataOf($family['spouse']));
        $this->assertFalse($family['familyHead']->canViewSensitiveDataOf($outsideCustomer));
    }

    // =============================================================================
    // PASSWORD MANAGEMENT TESTS
    // =============================================================================

    /** @test */
    public function customer_can_generate_default_password(): void
    {
        $password = Customer::generateDefaultPassword();

        $this->assertIsString($password);
        $this->assertEquals(8, strlen($password));
        $this->assertTrue(ctype_alnum($password));
    }

    /** @test */
    public function customer_can_set_default_password(): void
    {
        $customer = Customer::factory()->create();
        $plainPassword = $customer->setDefaultPassword();

        $customer->refresh();

        $this->assertTrue(Hash::check($plainPassword, $customer->password));
        $this->assertTrue($customer->must_change_password);
        $this->assertNull($customer->password_changed_at);
        $this->assertNull($customer->email_verified_at);
        $this->assertNotNull($customer->email_verification_token);
    }

    /** @test */
    public function customer_can_set_custom_password(): void
    {
        $customer = Customer::factory()->create();
        $customPassword = 'MyCustomPassword123!';

        $returnedPassword = $customer->setCustomPassword($customPassword, false);

        $customer->refresh();

        $this->assertEquals($customPassword, $returnedPassword);
        $this->assertTrue(Hash::check($customPassword, $customer->password));
        $this->assertFalse($customer->must_change_password);
        $this->assertNotNull($customer->password_changed_at);
    }

    /** @test */
    public function customer_can_change_password(): void
    {
        $customer = Customer::factory()->create([
            'must_change_password' => true,
            'email_verified_at' => null,
        ]);

        $newPassword = 'NewPassword123!';
        $customer->changePassword($newPassword);

        $customer->refresh();

        $this->assertTrue(Hash::check($newPassword, $customer->password));
        $this->assertFalse($customer->must_change_password);
        $this->assertNotNull($customer->password_changed_at);
        $this->assertNotNull($customer->email_verified_at);
        $this->assertNull($customer->email_verification_token);
    }

    /** @test */
    public function customer_can_check_if_needs_password_change(): void
    {
        $customerNeedsChange = Customer::factory()->create(['must_change_password' => true]);
        $customerNoChange = Customer::factory()->create(['must_change_password' => false]);

        $this->assertTrue($customerNeedsChange->needsPasswordChange());
        $this->assertFalse($customerNoChange->needsPasswordChange());
    }

    // =============================================================================
    // EMAIL VERIFICATION TESTS
    // =============================================================================

    /** @test */
    public function customer_can_check_if_email_is_verified(): void
    {
        $verifiedCustomer = Customer::factory()->create(['email_verified_at' => now()]);
        $unverifiedCustomer = Customer::factory()->create(['email_verified_at' => null]);

        $this->assertTrue($verifiedCustomer->hasVerifiedEmail());
        $this->assertFalse($unverifiedCustomer->hasVerifiedEmail());
    }

    /** @test */
    public function customer_can_generate_email_verification_token(): void
    {
        $customer = Customer::factory()->create();
        $token = $customer->generateEmailVerificationToken();

        $customer->refresh();

        $this->assertIsString($token);
        $this->assertEquals(60, strlen($token));
        $this->assertEquals($token, $customer->email_verification_token);
    }

    /** @test */
    public function customer_can_verify_email_with_correct_token(): void
    {
        $customer = Customer::factory()->create([
            'email_verified_at' => null,
            'email_verification_token' => 'valid_token_123',
        ]);

        $result = $customer->verifyEmail('valid_token_123');

        $customer->refresh();

        $this->assertTrue($result);
        $this->assertNotNull($customer->email_verified_at);
        $this->assertNull($customer->email_verification_token);
    }

    /** @test */
    public function customer_cannot_verify_email_with_incorrect_token(): void
    {
        $customer = Customer::factory()->create([
            'email_verified_at' => null,
            'email_verification_token' => 'valid_token_123',
        ]);

        $result = $customer->verifyEmail('invalid_token');

        $customer->refresh();

        $this->assertFalse($result);
        $this->assertNull($customer->email_verified_at);
        $this->assertEquals('valid_token_123', $customer->email_verification_token);
    }

    // =============================================================================
    // PASSWORD RESET TESTS
    // =============================================================================

    /** @test */
    public function customer_can_generate_password_reset_token(): void
    {
        $customer = Customer::factory()->create();
        $token = $customer->generatePasswordResetToken();

        $customer->refresh();

        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token)); // 32 bytes = 64 hex chars
        $this->assertEquals($token, $customer->password_reset_token);
        $this->assertNotNull($customer->password_reset_expires_at);
        $this->assertNotNull($customer->password_reset_sent_at);
    }

    /** @test */
    public function customer_can_verify_valid_password_reset_token(): void
    {
        $customer = Customer::factory()->create();
        $token = $customer->generatePasswordResetToken();

        $result = $customer->verifyPasswordResetToken($token);
        $this->assertTrue($result);
    }

    /** @test */
    public function customer_cannot_verify_invalid_password_reset_token(): void
    {
        $customer = Customer::factory()->create();
        $customer->generatePasswordResetToken();

        $result = $customer->verifyPasswordResetToken('invalid_token');
        $this->assertFalse($result);
    }

    /** @test */
    public function customer_cannot_verify_expired_password_reset_token(): void
    {
        $customer = Customer::factory()->create();
        $token = $customer->generatePasswordResetToken();

        // Manually set expiration to past
        $customer->update(['password_reset_expires_at' => now()->subHour()]);

        $result = $customer->verifyPasswordResetToken($token);

        $customer->refresh();

        $this->assertFalse($result);
        // Token should be cleared after expiration check
        $this->assertNull($customer->password_reset_token);
        $this->assertNull($customer->password_reset_expires_at);
    }

    /** @test */
    public function customer_can_clear_password_reset_token(): void
    {
        $customer = Customer::factory()->create();
        $customer->generatePasswordResetToken();

        $customer->clearPasswordResetToken();
        $customer->refresh();

        $this->assertNull($customer->password_reset_token);
        $this->assertNull($customer->password_reset_expires_at);
    }

    // =============================================================================
    // UTILITY METHOD TESTS
    // =============================================================================

    /** @test */
    public function customer_can_check_if_active(): void
    {
        $activeCustomer = Customer::factory()->create(['status' => true]);
        $inactiveCustomer = Customer::factory()->create(['status' => false]);

        $this->assertTrue($activeCustomer->isActive());
        $this->assertFalse($inactiveCustomer->isActive());
    }

    /** @test */
    public function customer_can_check_customer_type(): void
    {
        $retailCustomer = Customer::factory()->create(['type' => 'Retail']);
        $corporateCustomer = Customer::factory()->create(['type' => 'Corporate']);

        $this->assertTrue($retailCustomer->isRetailCustomer());
        $this->assertFalse($retailCustomer->isCorporateCustomer());

        $this->assertTrue($corporateCustomer->isCorporateCustomer());
        $this->assertFalse($corporateCustomer->isRetailCustomer());
    }

    /** @test */
    public function customer_can_get_masked_pan_number(): void
    {
        $customer = Customer::factory()->create(['pan_card_number' => 'CFDPB1228P']);
        $this->assertEquals('CFD******P', $customer->getMaskedPanNumber());

        $customerNoPan = Customer::factory()->create(['pan_card_number' => null]);
        $this->assertNull($customerNoPan->getMaskedPanNumber());

        $customerShortPan = Customer::factory()->create(['pan_card_number' => 'ABC']);
        $this->assertEquals('***', $customerShortPan->getMaskedPanNumber());
    }

    // =============================================================================
    // SECURITY VALIDATION TESTS
    // =============================================================================

    /** @test */
    public function validate_family_group_id_throws_exception_for_null(): void
    {
        $customer = new Customer();
        $reflection = new \ReflectionClass($customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Family group ID cannot be null for family operations');

        $method->invoke($customer, null);
    }

    /** @test */
    public function validate_family_group_id_throws_exception_for_non_numeric(): void
    {
        $customer = new Customer();
        $reflection = new \ReflectionClass($customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Family group ID must be numeric');

        $method->invoke($customer, 'invalid');
    }

    /** @test */
    public function validate_family_group_id_throws_exception_for_negative_number(): void
    {
        $customer = new Customer();
        $reflection = new \ReflectionClass($customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Family group ID must be a positive integer');

        $method->invoke($customer, -1);
    }

    /** @test */
    public function validate_family_group_id_throws_exception_for_nonexistent_group(): void
    {
        $customer = new Customer();
        $reflection = new \ReflectionClass($customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid or inactive family group ID');

        $method->invoke($customer, 99999); // Non-existent ID
    }

    /** @test */
    public function validate_family_group_id_succeeds_for_valid_group(): void
    {
        $familyGroup = FamilyGroup::factory()->create(['status' => true]);
        $customer = new Customer();

        $reflection = new \ReflectionClass($customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);

        $result = $method->invoke($customer, $familyGroup->id);
        $this->assertEquals($familyGroup->id, $result);
    }
}