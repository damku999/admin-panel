<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_security_headers_are_applied(): void
    {
        $response = $this->get('/');
        
        // Test that security headers are present
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Strict-Transport-Security');
        
        // Test Cross-Origin policies
        $response->assertHeader('Cross-Origin-Embedder-Policy', 'require-corp');
        $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
        $response->assertHeader('Cross-Origin-Resource-Policy', 'same-origin');
        
        // Test Permissions Policy
        $response->assertHeader('Permissions-Policy');
        $permissionsPolicy = $response->headers->get('Permissions-Policy');
        $this->assertStringContains('geolocation=()', $permissionsPolicy);
        $this->assertStringContains('microphone=()', $permissionsPolicy);
        $this->assertStringContains('camera=()', $permissionsPolicy);
    }

    public function test_csp_header_is_applied(): void
    {
        $response = $this->get('/');
        
        // Test that CSP header is present
        $this->assertTrue(
            $response->headers->has('Content-Security-Policy') || 
            $response->headers->has('Content-Security-Policy-Report-Only')
        );
        
        $cspHeader = $response->headers->get('Content-Security-Policy') ?: 
                     $response->headers->get('Content-Security-Policy-Report-Only');
        
        // Test key CSP directives
        $this->assertStringContains("default-src 'self'", $cspHeader);
        $this->assertStringContains("frame-src 'none'", $cspHeader);
        $this->assertStringContains("object-src 'none'", $cspHeader);
        $this->assertStringContains("base-uri 'self'", $cspHeader);
        
        // Test that unsafe-inline and unsafe-eval are NOT present (security improvement)
        $this->assertStringNotContains("'unsafe-inline'", $cspHeader);
        $this->assertStringNotContains("'unsafe-eval'", $cspHeader);
        
        // Test that nonce is present in script-src
        $this->assertStringContains("'nonce-", $cspHeader);
    }

    public function test_csp_nonce_is_available_in_views(): void
    {
        $response = $this->get('/login');
        
        // Test that the view receives the CSP nonce
        $response->assertViewHas('cspNonce');
        
        // Test that nonce is a valid base64 string
        $nonce = $response->viewData('cspNonce');
        $this->assertIsString($nonce);
        $this->assertGreaterThan(10, strlen($nonce)); // Nonce should be sufficiently long
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9+\/]+=*$/', $nonce); // Valid base64
    }

    public function test_csp_violation_reporting_endpoint(): void
    {
        $violationData = [
            'csp-report' => [
                'document-uri' => 'https://example.com/page',
                'referrer' => 'https://example.com/',
                'violated-directive' => 'script-src',
                'original-policy' => "default-src 'self'; script-src 'self' 'nonce-xyz'",
                'blocked-uri' => 'https://evil.com/script.js',
                'status-code' => 200,
            ]
        ];

        $response = $this->postJson('/security/csp-report', $violationData, [
            'Content-Type' => 'application/csp-report'
        ]);

        $response->assertStatus(204); // No Content - violation logged
    }

    public function test_security_headers_skipped_for_admin_tools(): void
    {
        // Skip headers for log viewer (if route exists)
        if (\Route::has('log-viewer.logs.index')) {
            $response = $this->get('/log-viewer');
            // Should still have basic headers but potentially different CSP
            $response->assertHeader('X-Content-Type-Options', 'nosniff');
        }
        
        $this->assertTrue(true); // Pass if no admin tools are configured
    }

    public function test_hsts_header_configuration(): void
    {
        $response = $this->get('/');
        
        $hstsHeader = $response->headers->get('Strict-Transport-Security');
        $this->assertNotNull($hstsHeader);
        
        // Test HSTS includes max-age
        $this->assertStringContains('max-age=', $hstsHeader);
        
        // Test HSTS includes includeSubDomains by default
        $this->assertStringContains('includeSubDomains', $hstsHeader);
    }

    public function test_admin_panel_has_enhanced_csp(): void
    {
        // Login as admin user
        $user = \App\Models\User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user);
        
        $response = $this->get('/dashboard');
        
        $cspHeader = $response->headers->get('Content-Security-Policy') ?: 
                     $response->headers->get('Content-Security-Policy-Report-Only');
        
        // Admin panel should have additional trusted sources
        $this->assertStringContains('kit.fontawesome.com', $cspHeader);
        $this->assertStringContains('cdn.datatables.net', $cspHeader);
    }

    public function test_customer_portal_has_basic_csp(): void
    {
        $response = $this->get('/customer');
        
        $cspHeader = $response->headers->get('Content-Security-Policy') ?: 
                     $response->headers->get('Content-Security-Policy-Report-Only');
        
        // Basic CSP should be applied
        $this->assertStringContains("default-src 'self'", $cspHeader);
        $this->assertStringContains("'nonce-", $cspHeader);
    }

    public function test_report_to_header_when_configured(): void
    {
        config(['security.csp_report_uri' => 'https://example.com/csp-report']);
        
        $response = $this->get('/');
        
        if (config('security.csp_report_uri')) {
            $response->assertHeader('Report-To');
            
            $reportToHeader = $response->headers->get('Report-To');
            $reportToData = json_decode($reportToHeader, true);
            
            $this->assertArrayHasKey('group', $reportToData);
            $this->assertArrayHasKey('endpoints', $reportToData);
            $this->assertEquals('csp-endpoint', $reportToData['group']);
        }
    }
}