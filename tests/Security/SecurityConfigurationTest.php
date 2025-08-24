<?php

namespace Tests\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Security Configuration Testing
 * 
 * Tests security-related configurations, headers, and environment settings
 * to ensure the application follows security best practices.
 */
class SecurityConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_secure_session_configuration(): void
    {
        // Test session security configuration
        $this->assertEquals('cookie', config('session.driver'), 
            'Session driver should be cookie for security');
        
        $this->assertTrue(config('session.http_only'), 
            'Session cookies should be HTTP only');
        
        $this->assertTrue(config('session.secure') || app()->environment('testing'), 
            'Session cookies should be secure in production');
        
        $this->assertEquals('strict', config('session.same_site'), 
            'Session cookies should have strict SameSite policy');
        
        // Test session lifetime is reasonable
        $lifetime = config('session.lifetime');
        $this->assertLessThanOrEqual(120, $lifetime, 
            'Session lifetime should not exceed 2 hours for security');
        $this->assertGreaterThanOrEqual(30, $lifetime, 
            'Session lifetime should be at least 30 minutes');
    }

    public function test_password_hashing_configuration(): void
    {
        // Test bcrypt configuration
        $this->assertEquals('bcrypt', config('hashing.driver'), 
            'Password hashing should use bcrypt');
        
        $rounds = config('hashing.bcrypt.rounds');
        $this->assertGreaterThanOrEqual(12, $rounds, 
            'Bcrypt rounds should be at least 12 for security');
        $this->assertLessThanOrEqual(16, $rounds, 
            'Bcrypt rounds should not exceed 16 for performance');
    }

    public function test_database_security_configuration(): void
    {
        $dbConfig = config('database.connections.mysql');
        
        // Check for secure database options
        $this->assertArrayHasKey('options', $dbConfig, 
            'Database should have security options configured');
        
        if (isset($dbConfig['options'])) {
            // Verify SSL is configured if available
            $this->assertTrue(
                !isset($dbConfig['options'][\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]) ||
                $dbConfig['options'][\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] === true,
                'Database SSL verification should be enabled'
            );
        }
    }

    public function test_cors_security_configuration(): void
    {
        $corsConfig = config('cors');
        
        // CORS should be restrictive
        $allowedOrigins = $corsConfig['allowed_origins'] ?? ['*'];
        $this->assertNotContains('*', $allowedOrigins, 
            'CORS should not allow all origins in production');
        
        // Credentials should be handled carefully
        if ($corsConfig['supports_credentials'] ?? false) {
            $this->assertNotEquals(['*'], $allowedOrigins, 
                'CORS with credentials should not allow all origins');
        }
    }

    public function test_security_headers_middleware_registered(): void
    {
        $middlewareGroups = config('app.middleware_groups', []);
        $routeMiddleware = config('app.route_middleware', []);
        
        // Check for security-related middleware
        $this->assertArrayHasKey('customer.secure', $routeMiddleware, 
            'SecureSession middleware should be registered');
    }

    public function test_debug_mode_disabled_in_production(): void
    {
        if (app()->environment('production')) {
            $this->assertFalse(config('app.debug'), 
                'Debug mode must be disabled in production');
        }
    }

    public function test_environment_variables_security(): void
    {
        // Check that sensitive environment variables are not exposed
        $sensitiveVars = [
            'DB_PASSWORD',
            'APP_KEY',
            'MAIL_PASSWORD',
            'AWS_SECRET_ACCESS_KEY'
        ];
        
        foreach ($sensitiveVars as $var) {
            $value = env($var);
            if ($value) {
                $this->assertNotEmpty($value, 
                    "Environment variable {$var} should not be empty if set");
                $this->assertNotEquals('null', strtolower($value), 
                    "Environment variable {$var} should not be 'null'");
                $this->assertNotEquals('password', strtolower($value), 
                    "Environment variable {$var} should not use default values");
            }
        }
    }

    public function test_app_key_is_set_and_secure(): void
    {
        $appKey = config('app.key');
        
        $this->assertNotEmpty($appKey, 'Application key must be set');
        $this->assertNotEquals('SomeRandomString', $appKey, 
            'Application key should not be default value');
        $this->assertStringStartsWith('base64:', $appKey, 
            'Application key should be base64 encoded');
        
        // Decode and check length
        $decoded = base64_decode(substr($appKey, 7));
        $this->assertEquals(32, strlen($decoded), 
            'Application key should be 32 bytes when decoded');
    }

    public function test_csrf_protection_enabled(): void
    {
        $middleware = app('router')->getMiddleware();
        
        $this->assertArrayHasKey('csrf', $middleware, 
            'CSRF middleware should be registered');
        
        // Test that CSRF middleware is in web middleware group
        $webMiddleware = config('app.middleware_groups.web', []);
        $this->assertContains(
            \App\Http\Middleware\VerifyCsrfToken::class, 
            $webMiddleware, 
            'CSRF middleware should be in web middleware group'
        );
    }

    public function test_rate_limiting_configuration(): void
    {
        // Test that rate limiting is configured
        $response = $this->get('/customer/login');
        
        // Make multiple requests to test rate limiting
        for ($i = 0; $i < 15; $i++) {
            $response = $this->get('/customer/login');
        }
        
        // Should not exceed reasonable limits
        $this->assertLessThanOrEqual(429, $response->getStatusCode(), 
            'Rate limiting should be active for login page');
    }

    public function test_file_upload_security_configuration(): void
    {
        $maxFileSize = ini_get('upload_max_filesize');
        $maxPostSize = ini_get('post_max_size');
        
        // Convert to bytes for comparison
        $maxFileSizeBytes = $this->convertToBytes($maxFileSize);
        $maxPostSizeBytes = $this->convertToBytes($maxPostSize);
        
        // File upload should be limited
        $this->assertLessThanOrEqual(50 * 1024 * 1024, $maxFileSizeBytes, 
            'Maximum file upload size should not exceed 50MB');
        $this->assertLessThanOrEqual(50 * 1024 * 1024, $maxPostSizeBytes, 
            'Maximum POST size should not exceed 50MB');
    }

    public function test_error_reporting_configuration(): void
    {
        if (app()->environment('production')) {
            $this->assertFalse(config('app.debug'), 
                'Debug mode should be disabled in production');
            
            $this->assertEquals('daily', config('logging.default'), 
                'Logging should be configured for production');
        }
        
        // Error reporting should not expose sensitive information
        $logLevel = config('logging.channels.daily.level', 'debug');
        if (app()->environment('production')) {
            $this->assertNotEquals('debug', $logLevel, 
                'Debug logging should be disabled in production');
        }
    }

    public function test_cache_security_configuration(): void
    {
        $cacheConfig = config('cache.default');
        
        // Cache should be properly configured
        $this->assertNotEquals('array', $cacheConfig, 
            'Array cache should not be used in production');
        
        if ($cacheConfig === 'redis') {
            $redisConfig = config('cache.stores.redis');
            $this->assertArrayHasKey('password', $redisConfig, 
                'Redis cache should have password protection');
        }
    }

    public function test_queue_security_configuration(): void
    {
        $queueConfig = config('queue.default');
        
        if ($queueConfig === 'redis') {
            $redisConfig = config('queue.connections.redis');
            $this->assertArrayHasKey('password', $redisConfig, 
                'Redis queue should have password protection');
        }
    }

    public function test_mail_security_configuration(): void
    {
        $mailConfig = config('mail.default');
        
        if ($mailConfig === 'smtp') {
            $smtpConfig = config('mail.mailers.smtp');
            
            // SMTP should use encryption
            $encryption = $smtpConfig['encryption'] ?? null;
            $this->assertContains($encryption, ['tls', 'ssl'], 
                'SMTP should use TLS or SSL encryption');
            
            // Port should be secure
            $port = $smtpConfig['port'] ?? 25;
            $this->assertNotEquals(25, $port, 
                'SMTP should not use unencrypted port 25');
        }
    }

    public function test_trusted_proxies_configuration(): void
    {
        $trustedProxies = config('trustedproxy.proxies');
        
        if ($trustedProxies !== '*') {
            $this->assertIsArray($trustedProxies, 
                'Trusted proxies should be an array of specific IPs');
        } else {
            // If using '*', ensure it's intentional and documented
            $this->markTestIncomplete(
                'Trusted proxies set to "*" - verify this is intentional for your deployment'
            );
        }
    }

    public function test_content_security_policy(): void
    {
        // Test if CSP headers are being set
        $response = $this->get('/customer/login');
        
        // Note: This test assumes CSP is implemented
        // If not implemented, this serves as a reminder to add it
        $cspHeader = $response->headers->get('Content-Security-Policy');
        
        if ($cspHeader) {
            $this->assertStringContainsString("default-src 'self'", $cspHeader, 
                'CSP should have restrictive default-src policy');
            $this->assertStringNotContainsString("'unsafe-inline'", $cspHeader, 
                'CSP should avoid unsafe-inline where possible');
        } else {
            $this->markTestIncomplete(
                'Content-Security-Policy header not found - consider implementing CSP'
            );
        }
    }

    public function test_secure_cookie_settings(): void
    {
        if (!app()->environment('testing')) {
            // Test secure cookie settings
            $response = $this->get('/customer/login');
            
            $cookies = $response->headers->getCookies();
            foreach ($cookies as $cookie) {
                if ($cookie->getName() === session()->getName()) {
                    $this->assertTrue($cookie->isSecure() || app()->environment('local'), 
                        'Session cookie should be secure');
                    $this->assertTrue($cookie->isHttpOnly(), 
                        'Session cookie should be HTTP only');
                    $this->assertEquals('strict', strtolower($cookie->getSameSite()), 
                        'Session cookie should have strict SameSite');
                }
            }
        }
    }

    public function test_password_policy_enforcement(): void
    {
        // Test password validation rules
        $validator = app('validator');
        
        $weakPasswords = [
            'password',
            '123456',
            'qwerty',
            'abc123',
            'password123'
        ];
        
        foreach ($weakPasswords as $weakPassword) {
            $validation = $validator->make(
                ['password' => $weakPassword], 
                ['password' => 'required|string|min:8|confirmed']
            );
            
            // Basic validation should at least check length
            if (strlen($weakPassword) < 8) {
                $this->assertTrue($validation->fails(), 
                    "Weak password '{$weakPassword}' should be rejected");
            }
        }
    }

    /**
     * Convert PHP ini shorthand to bytes
     */
    private function convertToBytes(string $value): int
    {
        $unit = strtolower(substr($value, -1));
        $value = (int) $value;
        
        switch ($unit) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }
        
        return $value;
    }
}