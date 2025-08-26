<?php

namespace Tests\Security;

use App\Models\Customer;
use App\Models\FamilyGroup;
use App\Models\CustomerAuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SqlInjectionSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $familyGroup;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create legitimate family group
        $this->familyGroup = FamilyGroup::create([
            'name' => 'Test Family',
            'status' => true,
            'created_by' => 1
        ]);
        
        $this->customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'sql.test@example.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'family_group_id' => $this->familyGroup->id,
            'email_verified_at' => now()
        ]);
        
        $this->familyGroup->update(['family_head_id' => $this->customer->id]);
    }

    public function test_validate_family_group_id_accepts_valid_integer(): void
    {
        $reflection = new \ReflectionClass($this->customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->customer, $this->familyGroup->id);
        $this->assertEquals($this->familyGroup->id, $result);
    }

    public function test_validate_family_group_id_rejects_null(): void
    {
        $reflection = new \ReflectionClass($this->customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Family group ID cannot be null for family operations');
        
        $method->invoke($this->customer, null);
    }

    public function test_validate_family_group_id_rejects_non_numeric(): void
    {
        $reflection = new \ReflectionClass($this->customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Family group ID must be numeric');
        
        $method->invoke($this->customer, 'malicious_string');
    }

    public function test_validate_family_group_id_rejects_sql_injection_attempts(): void
    {
        $reflection = new \ReflectionClass($this->customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);
        
        $sqlInjectionPayloads = [
            "1; DROP TABLE customers; --",
            "1 OR 1=1",
            "1 UNION SELECT * FROM customers",
            "1' OR '1'='1",
            "1 AND (SELECT COUNT(*) FROM customers) > 0",
            "1; INSERT INTO customers VALUES (1,'hacker')",
            "-1 UNION SELECT password FROM customers"
        ];
        
        foreach ($sqlInjectionPayloads as $payload) {
            $this->expectException(\InvalidArgumentException::class);
            try {
                $method->invoke($this->customer, $payload);
            } catch (\InvalidArgumentException $e) {
                $this->assertStringContainsString('must be numeric', $e->getMessage());
                continue; // Expected exception, continue to next payload
            }
        }
    }

    public function test_validate_family_group_id_rejects_negative_numbers(): void
    {
        $reflection = new \ReflectionClass($this->customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Family group ID must be a positive integer');
        
        $method->invoke($this->customer, -1);
    }

    public function test_validate_family_group_id_rejects_zero(): void
    {
        $reflection = new \ReflectionClass($this->customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Family group ID must be a positive integer');
        
        $method->invoke($this->customer, 0);
    }

    public function test_validate_family_group_id_rejects_nonexistent_family_group(): void
    {
        $reflection = new \ReflectionClass($this->customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid or inactive family group ID');
        
        $method->invoke($this->customer, 99999);
    }

    public function test_validate_family_group_id_rejects_inactive_family_group(): void
    {
        // Create inactive family group
        $inactiveFamilyGroup = FamilyGroup::create([
            'name' => 'Inactive Family',
            'status' => false,
            'created_by' => 1
        ]);
        
        $reflection = new \ReflectionClass($this->customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid or inactive family group ID');
        
        $method->invoke($this->customer, $inactiveFamilyGroup->id);
    }

    public function test_get_viewable_insurance_handles_sql_injection_gracefully(): void
    {
        // Manually manipulate family_group_id to simulate SQL injection attempt
        $this->customer->family_group_id = "1; DROP TABLE customers; --";
        
        $this->expectException(\InvalidArgumentException::class);
        $this->customer->getViewableInsurance()->get();
    }

    public function test_dashboard_logs_sql_injection_attempts(): void
    {
        // Create customer with malicious family_group_id
        $maliciousCustomer = Customer::create([
            'name' => 'Malicious Customer',
            'email' => 'malicious@example.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'family_group_id' => 999999, // Non-existent ID
            'email_verified_at' => now()
        ]);
        
        $this->actingAs($maliciousCustomer, 'customer');
        
        $response = $this->get(route('customer.dashboard'));
        
        // Should still show dashboard but with error
        $response->assertStatus(200);
        $response->assertSessionHas('error', 'Security error: Unable to load family policies.');
        
        // Should log the attempt
        $auditLog = CustomerAuditLog::where([
            'customer_id' => $maliciousCustomer->id,
            'action' => 'sql_injection_attempt',
            'success' => false
        ])->first();
        
        $this->assertNotNull($auditLog);
        $this->assertEquals('Invalid family group ID detected in dashboard query', $auditLog->description);
        $this->assertEquals('SQL injection prevention - Invalid family group ID', $auditLog->failure_reason);
        
        $metadata = $auditLog->metadata;
        $this->assertEquals('sql_injection_attempt', $metadata['security_violation']);
        $this->assertEquals('dashboard', $metadata['location']);
    }

    public function test_policies_page_logs_sql_injection_attempts(): void
    {
        // Create customer with malicious family_group_id  
        $maliciousCustomer = Customer::create([
            'name' => 'Malicious Customer',
            'email' => 'malicious2@example.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'family_group_id' => 999999, // Non-existent ID
            'email_verified_at' => now()
        ]);
        
        $this->actingAs($maliciousCustomer, 'customer');
        
        $response = $this->get(route('customer.policies'));
        
        // Should redirect to dashboard with error
        $response->assertRedirect(route('customer.dashboard'));
        $response->assertSessionHas('error', 'Security error: Invalid family data detected.');
        
        // Should log the attempt
        $auditLog = CustomerAuditLog::where([
            'customer_id' => $maliciousCustomer->id,
            'action' => 'sql_injection_attempt',
            'success' => false
        ])->first();
        
        $this->assertNotNull($auditLog);
        $this->assertEquals('Invalid family group ID detected in policy query', $auditLog->description);
    }

    public function test_family_insurance_method_prevents_sql_injection(): void
    {
        // Test that familyInsurance method validates properly
        $this->customer->family_group_id = "'; DROP TABLE customers; --";
        
        $this->expectException(\InvalidArgumentException::class);
        $this->customer->familyInsurance()->get();
    }

    public function test_sql_injection_prevention_with_unicode_attacks(): void
    {
        $reflection = new \ReflectionClass($this->customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);
        
        $unicodePayloads = [
            "1\u0000 OR 1=1",
            "1\u0020UNION\u0020SELECT",
            "1\u00A0AND\u00A0'1'='1'",
            "1\u2028OR\u20281=1"
        ];
        
        foreach ($unicodePayloads as $payload) {
            $this->expectException(\InvalidArgumentException::class);
            try {
                $method->invoke($this->customer, $payload);
            } catch (\InvalidArgumentException $e) {
                $this->assertStringContainsString('must be numeric', $e->getMessage());
                continue;
            }
        }
    }

    public function test_sql_injection_prevention_with_encoded_attacks(): void
    {
        $reflection = new \ReflectionClass($this->customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);
        
        $encodedPayloads = [
            urlencode("1; DROP TABLE customers; --"),
            "1%20OR%201=1",
            "1%27%20OR%20%271%27=%271",
            "1+OR+1=1"
        ];
        
        foreach ($encodedPayloads as $payload) {
            $this->expectException(\InvalidArgumentException::class);
            try {
                $method->invoke($this->customer, $payload);
            } catch (\InvalidArgumentException $e) {
                $this->assertStringContainsString('must be numeric', $e->getMessage());
                continue;
            }
        }
    }

    public function test_sql_injection_prevention_audit_metadata(): void
    {
        $maliciousCustomer = Customer::create([
            'name' => 'Audit Test Customer',
            'email' => 'audit.test@example.com', 
            'password' => Hash::make('password123'),
            'status' => true,
            'family_group_id' => -999, // Invalid negative ID
            'email_verified_at' => now()
        ]);
        
        $this->actingAs($maliciousCustomer, 'customer');
        
        $response = $this->get(route('customer.dashboard'));
        
        $auditLog = CustomerAuditLog::where([
            'customer_id' => $maliciousCustomer->id,
            'action' => 'sql_injection_attempt'
        ])->first();
        
        $this->assertNotNull($auditLog);
        $metadata = $auditLog->metadata;
        
        // Verify comprehensive metadata
        $this->assertArrayHasKey('error_message', $metadata);
        $this->assertArrayHasKey('family_group_id', $metadata);
        $this->assertArrayHasKey('security_violation', $metadata);
        $this->assertArrayHasKey('location', $metadata);
        
        $this->assertEquals(-999, $metadata['family_group_id']);
        $this->assertEquals('sql_injection_attempt', $metadata['security_violation']);
        $this->assertStringContainsString('positive integer', $metadata['error_message']);
    }

    public function test_legitimate_queries_work_normally(): void
    {
        // Test that legitimate queries continue to work
        $this->actingAs($this->customer, 'customer');
        
        // Dashboard should work normally
        $response = $this->get(route('customer.dashboard'));
        $response->assertStatus(200);
        $response->assertDontSee('Security error');
        
        // Policies should work normally (though redirected due to middleware)
        $response = $this->get(route('customer.policies'));
        // This might redirect or work depending on middleware, but shouldn't crash
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302]));
    }

    public function test_boundary_values_are_handled_correctly(): void
    {
        $reflection = new \ReflectionClass($this->customer);
        $method = $reflection->getMethod('validateFamilyGroupId');
        $method->setAccessible(true);
        
        // Test boundary values
        $this->expectException(\InvalidArgumentException::class);
        $method->invoke($this->customer, PHP_INT_MAX); // Very large number (likely non-existent)
        
        $this->expectException(\InvalidArgumentException::class);
        $method->invoke($this->customer, PHP_INT_MIN); // Very negative number
        
        // Test edge case - number that's technically valid but doesn't exist
        $this->expectException(\InvalidArgumentException::class);
        $method->invoke($this->customer, 2147483647);
    }
}