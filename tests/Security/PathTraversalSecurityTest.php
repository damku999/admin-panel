<?php

namespace Tests\Security;

use App\Models\Customer;
use App\Models\FamilyGroup;
use App\Models\FamilyMember;
use App\Models\CustomerInsurance;
use App\Models\CustomerAuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PathTraversalSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $familyHead;
    protected $testPolicy;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test family setup
        $familyGroup = FamilyGroup::factory()->create([
            'name' => 'Security Test Family',
            'status' => true
        ]);
        
        $this->familyHead = Customer::factory()->create([
            'email' => 'security.head@example.com',
            'password' => Hash::make('password123'),
            'status' => true,
            'family_group_id' => $familyGroup->id
        ]);
        
        FamilyMember::create([
            'family_group_id' => $familyGroup->id,
            'customer_id' => $this->familyHead->id,
            'relationship' => 'head',
            'is_head' => true,
            'status' => true
        ]);
        
        $familyGroup->update(['family_head_id' => $this->familyHead->id]);
    }

    public function test_path_traversal_attack_is_blocked_with_dot_dot_slash(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Create policy with malicious path traversal attempt
        $maliciousPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->familyHead->id,
            'policy_no' => 'MAL001',
            'policy_document_path' => '../../../etc/passwd'
        ]);
        
        // Attempt to download with path traversal
        $response = $this->get(route('customer.policies.download', $maliciousPolicy->id));
        
        // Should be redirected back with error
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Invalid policy document path.');
        
        // Should log security violation
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $this->familyHead->id,
            'action' => 'download_policy',
            'success' => false,
            'failure_reason' => 'Invalid file path detected'
        ]);
    }

    public function test_path_traversal_attack_is_blocked_with_windows_style(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Create policy with Windows-style path traversal
        $maliciousPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->familyHead->id,
            'policy_no' => 'MAL002',
            'policy_document_path' => '..\\..\\..\\windows\\system32\\config\\sam'
        ]);
        
        $response = $this->get(route('customer.policies.download', $maliciousPolicy->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Invalid policy document path.');
        
        // Verify security logging
        $auditLog = CustomerAuditLog::where([
            'customer_id' => $this->familyHead->id,
            'action' => 'download_policy',
            'success' => false
        ])->latest()->first();
        
        $this->assertNotNull($auditLog);
        $this->assertEquals('Invalid file path detected', $auditLog->failure_reason);
    }

    public function test_null_byte_injection_attack_is_blocked(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Create policy with null byte injection attempt
        $maliciousPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->familyHead->id,
            'policy_no' => 'MAL003',
            'policy_document_path' => "normal_file.pdf\x00../../../etc/passwd"
        ]);
        
        $response = $this->get(route('customer.policies.download', $maliciousPolicy->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Invalid policy document path.');
    }

    public function test_directory_traversal_outside_allowed_directory_is_blocked(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Create policy trying to access system files
        $maliciousPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->familyHead->id,
            'policy_no' => 'MAL004',
            'policy_document_path' => '/etc/passwd'
        ]);
        
        $response = $this->get(route('customer.policies.download', $maliciousPolicy->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Access denied. Invalid file path.');
        
        // Should log path traversal attempt
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $this->familyHead->id,
            'action' => 'download_policy',
            'success' => false,
            'failure_reason' => 'Path traversal attack blocked'
        ]);
    }

    public function test_non_pdf_file_type_is_blocked(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Create test executable file
        $testDir = storage_path('app/public/test');
        if (!is_dir($testDir)) {
            mkdir($testDir, 0755, true);
        }
        
        $executableFile = $testDir . '/malicious.exe';
        file_put_contents($executableFile, 'Fake executable content');
        
        $maliciousPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->familyHead->id,
            'policy_no' => 'MAL005',
            'policy_document_path' => 'test/malicious.exe'
        ]);
        
        $response = $this->get(route('customer.policies.download', $maliciousPolicy->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Only PDF documents can be downloaded.');
        
        // Cleanup
        if (file_exists($executableFile)) {
            unlink($executableFile);
        }
    }

    public function test_legitimate_pdf_download_works(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Create legitimate PDF file
        $testDir = storage_path('app/public/policies');
        if (!is_dir($testDir)) {
            mkdir($testDir, 0755, true);
        }
        
        $pdfFile = $testDir . '/legitimate_policy.pdf';
        file_put_contents($pdfFile, '%PDF-1.4 Fake PDF content for testing');
        
        $legitimatePolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->familyHead->id,
            'policy_no' => 'LEG001',
            'policy_document_path' => 'policies/legitimate_policy.pdf'
        ]);
        
        $response = $this->get(route('customer.policies.download', $legitimatePolicy->id));
        
        // Should succeed and return file download
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/octet-stream');
        
        // Should log successful download
        $this->assertDatabaseHas('customer_audit_logs', [
            'customer_id' => $this->familyHead->id,
            'action' => 'download_policy',
            'success' => true
        ]);
        
        // Cleanup
        if (file_exists($pdfFile)) {
            unlink($pdfFile);
        }
    }

    public function test_symlink_attack_is_blocked(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Create test directory and try symlink attack
        $testDir = storage_path('app/public/test');
        if (!is_dir($testDir)) {
            mkdir($testDir, 0755, true);
        }
        
        $symlinkPath = $testDir . '/symlink_attack.pdf';
        
        // Try to create symlink to sensitive file (if possible)
        if (function_exists('symlink') && !file_exists($symlinkPath)) {
            @symlink('/etc/passwd', $symlinkPath);
        }
        
        $maliciousPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->familyHead->id,
            'policy_no' => 'SYM001',
            'policy_document_path' => 'test/symlink_attack.pdf'
        ]);
        
        $response = $this->get(route('customer.policies.download', $maliciousPolicy->id));
        
        // Should either be blocked or not find the file
        $this->assertTrue(
            $response->isRedirection() || $response->status() === 403
        );
        
        // Cleanup
        if (file_exists($symlinkPath)) {
            unlink($symlinkPath);
        }
    }

    public function test_encoded_path_traversal_is_blocked(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Test URL encoded path traversal
        $maliciousPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->familyHead->id,
            'policy_no' => 'ENC001',
            'policy_document_path' => '%2e%2e%2f%2e%2e%2f%2e%2e%2fetc%2fpasswd'
        ]);
        
        $response = $this->get(route('customer.policies.download', $maliciousPolicy->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Invalid policy document path.');
    }

    public function test_unicode_encoding_path_traversal_is_blocked(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        // Test Unicode encoded path traversal
        $maliciousPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->familyHead->id,
            'policy_no' => 'UNI001',
            'policy_document_path' => '..%c0%af..%c0%af..%c0%afetc%c0%afpasswd'
        ]);
        
        $response = $this->get(route('customer.policies.download', $maliciousPolicy->id));
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Invalid policy document path.');
    }

    public function test_security_audit_logs_contain_detailed_information(): void
    {
        $this->actingAs($this->familyHead, 'customer');
        
        $maliciousPolicy = CustomerInsurance::factory()->create([
            'customer_id' => $this->familyHead->id,
            'policy_no' => 'AUDIT001',
            'policy_document_path' => '../../../etc/passwd'
        ]);
        
        $response = $this->get(route('customer.policies.download', $maliciousPolicy->id));
        
        $auditLog = CustomerAuditLog::where([
            'customer_id' => $this->familyHead->id,
            'action' => 'download_policy',
            'success' => false
        ])->latest()->first();
        
        $this->assertNotNull($auditLog);
        
        // Verify audit log contains security-relevant information
        $metadata = $auditLog->metadata;
        $this->assertArrayHasKey('policy_id', $metadata);
        $this->assertArrayHasKey('policy_no', $metadata);
        $this->assertArrayHasKey('attempted_path', $metadata);
        $this->assertArrayHasKey('security_violation', $metadata);
        $this->assertEquals('path_traversal_attempt', $metadata['security_violation']);
    }

    protected function tearDown(): void
    {
        // Clean up any test files
        $testDirs = [
            storage_path('app/public/test'),
            storage_path('app/public/policies')
        ];
        
        foreach ($testDirs as $dir) {
            if (is_dir($dir)) {
                $files = glob("$dir/*");
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
        }
        
        parent::tearDown();
    }
}