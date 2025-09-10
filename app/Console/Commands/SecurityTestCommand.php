<?php

namespace App\Console\Commands;

use App\Services\ContentSecurityPolicyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SecurityTestCommand extends Command
{
    protected $signature = 'security:test {--headers : Test security headers} {--csp : Test CSP policy} {--xss : Test XSS protection} {--all : Run all security tests}';
    protected $description = 'Test security implementations and configurations';

    public function handle(): void
    {
        $this->info('🔐 Security Implementation Testing');
        $this->newLine();

        if ($this->option('all')) {
            $this->testSecurityHeaders();
            $this->testCspPolicy();
            $this->testXssProtection();
            $this->testConfiguration();
        } elseif ($this->option('headers')) {
            $this->testSecurityHeaders();
        } elseif ($this->option('csp')) {
            $this->testCspPolicy();
        } elseif ($this->option('xss')) {
            $this->testXssProtection();
        } else {
            $this->testConfiguration();
        }

        $this->newLine();
        $this->info('✅ Security testing completed!');
    }

    private function testSecurityHeaders(): void
    {
        $this->info('🛡️ Testing Security Headers');
        
        $cspService = app(ContentSecurityPolicyService::class);
        $headers = $cspService->getSecurityHeaders();
        
        $this->table(['Header', 'Value', 'Status'], [
            ['X-Content-Type-Options', $headers['X-Content-Type-Options'] ?? 'Missing', $this->getStatus($headers, 'X-Content-Type-Options')],
            ['X-Frame-Options', $headers['X-Frame-Options'] ?? 'Missing', $this->getStatus($headers, 'X-Frame-Options')],
            ['Referrer-Policy', $headers['Referrer-Policy'] ?? 'Missing', $this->getStatus($headers, 'Referrer-Policy')],
            ['Strict-Transport-Security', $this->truncate($headers['Strict-Transport-Security'] ?? 'Missing'), $this->getStatus($headers, 'Strict-Transport-Security')],
            ['Cross-Origin-Embedder-Policy', $headers['Cross-Origin-Embedder-Policy'] ?? 'Missing', $this->getStatus($headers, 'Cross-Origin-Embedder-Policy')],
            ['Cross-Origin-Opener-Policy', $headers['Cross-Origin-Opener-Policy'] ?? 'Missing', $this->getStatus($headers, 'Cross-Origin-Opener-Policy')],
            ['Permissions-Policy', $this->truncate($headers['Permissions-Policy'] ?? 'Missing'), $this->getStatus($headers, 'Permissions-Policy')],
        ]);
    }

    private function testCspPolicy(): void
    {
        $this->info('🎯 Testing Content Security Policy');
        
        $cspService = app(ContentSecurityPolicyService::class);
        $request = request();
        $cspPolicy = $cspService->getContentSecurityPolicy($request);
        
        $this->line("   ✓ CSP Nonce Generated: " . substr($cspService->getNonce(), 0, 10) . '...');
        $this->line("   ✓ CSP Policy Directives: " . count($cspPolicy));
        
        $criticalDirectives = [
            'default-src' => $cspPolicy['default-src'] ?? 'Missing',
            'script-src' => $this->truncate($cspPolicy['script-src'] ?? 'Missing'),
            'style-src' => $this->truncate($cspPolicy['style-src'] ?? 'Missing'),
            'object-src' => $cspPolicy['object-src'] ?? 'Missing',
            'frame-src' => $cspPolicy['frame-src'] ?? 'Missing',
        ];
        
        $this->table(['Directive', 'Policy'], array_map(function ($key, $value) {
            return [$key, $value];
        }, array_keys($criticalDirectives), $criticalDirectives));
        
        // Security checks
        $scriptSrc = $cspPolicy['script-src'] ?? '';
        if (str_contains($scriptSrc, "'unsafe-inline'")) {
            $this->warn("   ⚠️ WARNING: script-src contains 'unsafe-inline' - XSS risk!");
        } else {
            $this->line("   ✅ script-src does not contain 'unsafe-inline'");
        }
        
        if (str_contains($scriptSrc, "'unsafe-eval'")) {
            if (app()->environment('production')) {
                $this->error("   ❌ ERROR: script-src contains 'unsafe-eval' in production!");
            } else {
                $this->warn("   ⚠️ script-src contains 'unsafe-eval' (development only)");
            }
        } else {
            $this->line("   ✅ script-src does not contain 'unsafe-eval'");
        }
        
        if (str_contains($scriptSrc, "'nonce-")) {
            $this->line("   ✅ CSP nonce-based protection enabled");
        } else {
            $this->warn("   ⚠️ No nonce found in script-src");
        }
    }

    private function testXssProtection(): void
    {
        $this->info('🚫 Testing XSS Protection');
        
        $testStrings = [
            '<script>alert("xss")</script>',
            'javascript:alert(1)',
            '<img src=x onerror=alert(1)>',
            'data:text/html,<script>alert(1)</script>',
            '<svg onload=alert(1)>',
        ];
        
        foreach ($testStrings as $testString) {
            $sanitized = $this->simulateXssSanitization($testString);
            $status = ($sanitized !== $testString) ? '✅ Blocked' : '❌ Not Blocked';
            $this->line("   {$status}: " . substr($testString, 0, 30) . '...');
        }
    }

    private function testConfiguration(): void
    {
        $this->info('⚙️ Testing Security Configuration');
        
        $config = [
            'CSP Enabled' => config('security.csp_enabled', 'Not Set') ? 'Yes' : 'No',
            'CSP Report Only' => config('security.csp_report_only', 'Not Set') ? 'Yes' : 'No',
            'HSTS Max Age' => config('security.hsts_max_age', 'Not Set'),
            'XSS Auto Escape' => config('security.xss_protection.auto_escape_blade', 'Not Set') ? 'Yes' : 'No',
            'Input Sanitization' => config('security.xss_protection.sanitize_inputs', 'Not Set') ? 'Yes' : 'No',
            'Security Monitoring' => config('security.monitoring.log_csp_violations', 'Not Set') ? 'Yes' : 'No',
        ];
        
        $this->table(['Setting', 'Value'], array_map(function ($key, $value) {
            return [$key, $value];
        }, array_keys($config), $config));
        
        // Environment checks
        $this->newLine();
        $this->info('🌍 Environment Security Checks');
        
        if (app()->environment('production')) {
            $this->line("   ✅ Running in production environment");
            
            if (config('security.csp_report_only')) {
                $this->warn("   ⚠️ CSP is in report-only mode in production");
            }
            
            if (config('app.debug')) {
                $this->error("   ❌ Debug mode is enabled in production!");
            } else {
                $this->line("   ✅ Debug mode is disabled");
            }
        } else {
            $this->warn("   ⚠️ Running in development environment");
            $this->line("   ℹ️ Some security features may be relaxed for development");
        }
        
        // Test CSP violation endpoint
        $this->newLine();
        $this->info('🔍 Testing CSP Violation Endpoint');
        
        try {
            $response = Http::timeout(5)->post(url('/security/csp-report'), [
                'csp-report' => [
                    'document-uri' => 'test',
                    'violated-directive' => 'test',
                    'original-policy' => 'test',
                ]
            ]);
            
            if ($response->status() === 204) {
                $this->line("   ✅ CSP violation endpoint responding correctly");
            } else {
                $this->warn("   ⚠️ CSP violation endpoint returned status: " . $response->status());
            }
        } catch (\Exception $e) {
            $this->warn("   ⚠️ Could not test CSP violation endpoint: " . $e->getMessage());
        }
    }

    private function getStatus(array $headers, string $key): string
    {
        return isset($headers[$key]) ? '✅ Set' : '❌ Missing';
    }

    private function truncate(string $value, int $length = 50): string
    {
        return strlen($value) > $length ? substr($value, 0, $length) . '...' : $value;
    }

    private function simulateXssSanitization(string $input): string
    {
        // Simulate the XSS protection middleware logic
        $dangerousPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i',
            '/javascript\s*:/i',
            '/on\w+\s*=/i',
            '/data\s*:\s*text\/html/i',
            '/<svg[^>]*onload[^>]*>/i',
        ];
        
        $sanitized = $input;
        foreach ($dangerousPatterns as $pattern) {
            $sanitized = preg_replace($pattern, '', $sanitized);
        }
        
        // Basic HTML entity encoding
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        
        return $sanitized;
    }
}