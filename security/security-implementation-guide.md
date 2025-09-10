# Security Implementation Guide: Laravel Insurance Management System

## Critical Security Fixes (Immediate Action Required)

### 1. Content Security Policy (CSP) Hardening

#### Current Issue
The existing CSP allows `'unsafe-inline'` and `'unsafe-eval'`, which defeats XSS protection:

```php
// PROBLEMATIC (current SecurityService.php):
"script-src" => "'self' 'unsafe-inline' 'unsafe-eval' https://code.jquery.com ...",
"style-src" => "'self' 'unsafe-inline' https://fonts.googleapis.com ..."
```

#### Secure Implementation

**Step 1: Update SecurityService.php**

```php
<?php

namespace App\Services;

class SecurityService
{
    private function generateNonce(): string
    {
        return base64_encode(random_bytes(16));
    }

    public function getContentSecurityPolicy(): array
    {
        $nonce = $this->generateNonce();
        request()->attributes->set('csp_nonce', $nonce);
        
        return [
            "default-src" => "'self'",
            "script-src" => "'self' 'nonce-{$nonce}' https://code.jquery.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
            "style-src" => "'self' 'nonce-{$nonce}' https://fonts.googleapis.com https://cdn.jsdelivr.net",
            "font-src" => "'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
            "img-src" => "'self' data: https:",
            "connect-src" => "'self'",
            "frame-src" => "'none'",
            "object-src" => "'none'",
            "media-src" => "'self'",
            "form-action" => "'self'",
            "frame-ancestors" => "'none'",
            "base-uri" => "'self'",
            "upgrade-insecure-requests" => "",
            "block-all-mixed-content" => ""
        ];
    }
    
    public function getSecurityHeaders(): array
    {
        return [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=(), payment=(), usb=()',
            'Cross-Origin-Embedder-Policy' => 'require-corp',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'X-Permitted-Cross-Domain-Policies' => 'none',
        ];
    }
    
    public function sanitizeForJavaScript($input): string
    {
        return json_encode($input, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }
}
```

**Step 2: Update Blade Templates to Use Nonces**

In `resources/views/common/head.blade.php`:
```php
<style nonce="{{ request()->attributes->get('csp_nonce') }}">
    /* Critical CSS for above-the-fold content */
    .sidebar { transition: width 0.3s ease; }
    .topbar { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); }
    .card { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); }
</style>
```

**Step 3: Fix Session Message XSS**

In `resources/views/layouts/customer.blade.php`, replace:
```javascript
// VULNERABLE:
@if (session('message'))
    show_notification('success', '{{ session('message') }}');
@endif

// SECURE:
@if (session('message'))
    show_notification('success', {!! app(App\Services\SecurityService::class)->sanitizeForJavaScript(session('message')) !!});
@endif
```

### 2. Inline JavaScript Elimination

#### Step 1: Create External JavaScript File

Create `public/js/admin-security.js`:
```javascript
// Secure event handlers - no inline JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Replace all onclick handlers with event delegation
    document.addEventListener('click', function(e) {
        // Handle delete confirmations
        if (e.target.matches('[data-action="delete-confirm"]')) {
            e.preventDefault();
            const recordId = e.target.dataset.recordId;
            const model = e.target.dataset.model;
            const displayTitle = e.target.dataset.title;
            const tableIdOrUrl = e.target.dataset.tableIdOrUrl || '';
            
            showDeleteConfirmation(recordId, model, displayTitle, tableIdOrUrl);
        }
        
        // Handle form submissions with loading states
        if (e.target.matches('[data-action="submit-form"]')) {
            e.preventDefault();
            const form = e.target.closest('form');
            if (form) {
                showLoading();
                form.submit();
            }
        }
    });
    
    // Secure AJAX operations
    function performAjaxOperation(options) {
        // Ensure CSRF token is included
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            options.headers = options.headers || {};
            options.headers['X-CSRF-TOKEN'] = token.getAttribute('content');
        }
        
        // Add security headers
        options.headers['X-Requested-With'] = 'XMLHttpRequest';
        
        return fetch(options.url, options)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .catch(error => {
                console.error('Ajax operation failed:', error);
                showNotification('error', 'Operation failed. Please try again.');
            });
    }
    
    function showDeleteConfirmation(recordId, model, displayTitle, tableIdOrUrl) {
        // Use proper HTML escaping for user data
        const escapedTitle = escapeHtml(displayTitle);
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.querySelector('.module_action').textContent = 'Delete';
            modal.querySelector('#module_title').textContent = escapedTitle;
            
            const deleteBtn = modal.querySelector('#delete-btn');
            deleteBtn.onclick = function() {
                deleteRecord(recordId, model, tableIdOrUrl);
            };
            
            // Show modal (Bootstrap 5)
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        }
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
```

#### Step 2: Update Blade Templates

Replace inline `onclick` handlers in templates:

**Before (Vulnerable):**
```html
<a href="#" onclick="delete_conf_common('{{ $item->id }}', 'customer', '{{ $item->name }}')">Delete</a>
```

**After (Secure):**
```html
<a href="#" 
   data-action="delete-confirm"
   data-record-id="{{ $item->id }}" 
   data-model="customer" 
   data-title="{{ $item->name }}"
   class="btn btn-danger">Delete</a>
```

#### Step 3: Include Security Script

In `resources/views/layouts/app.blade.php`:
```html
<!-- Security-hardened JavaScript -->
<script src="{{ asset('js/admin-security.js') }}" nonce="{{ request()->attributes->get('csp_nonce') }}"></script>
```

### 3. Subresource Integrity (SRI) Implementation

Update CDN resources in `resources/views/common/head.blade.php`:

```html
<!-- jQuery with SRI -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" 
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" 
        crossorigin="anonymous"
        nonce="{{ request()->attributes->get('csp_nonce') }}"></script>

<!-- Select2 with SRI -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" 
      integrity="sha256-5EFTHqNzRpRPVFNMUKmvXF1W3dCksQ3J0rOa9hSZgZs=" 
      crossorigin="anonymous" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" 
        integrity="sha256-lx9+FPHLLKPsJcvkisPf5aH5K3Jw+1ywEfNUuiNZW7o=" 
        crossorigin="anonymous"
        nonce="{{ request()->attributes->get('csp_nonce') }}"></script>
```

### 4. Enhanced Session Security

#### Update Session Configuration

Add to `config/session.php`:
```php
<?php

return [
    // ... existing configuration ...
    
    'cookie' => env('SESSION_COOKIE', 'laravel_session'),
    'secure' => env('SESSION_SECURE_COOKIE', true), // Force HTTPS
    'http_only' => true, // Prevent JavaScript access
    'same_site' => 'strict', // CSRF protection
    'encrypt' => true, // Encrypt session data
    
    // Customer session timeout (in minutes)
    'customer_timeout' => env('CUSTOMER_SESSION_TIMEOUT', 60),
    
    // Admin session timeout (in minutes) 
    'admin_timeout' => env('ADMIN_SESSION_TIMEOUT', 480), // 8 hours
];
```

#### Create Admin Session Timeout Middleware

Create `app/Http/Middleware/AdminSessionTimeout.php`:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminSessionTimeout
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = $request->session()->get('admin_last_activity');
            $timeoutMinutes = config('session.admin_timeout', 480);
            
            if ($lastActivity && now()->diffInMinutes($lastActivity) > $timeoutMinutes) {
                Log::warning('Admin session timed out', [
                    'admin_id' => Auth::id(),
                    'last_activity' => $lastActivity,
                    'timeout_minutes' => $timeoutMinutes
                ]);
                
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->with('error', 'Your session has timed out due to inactivity.');
            }
            
            $request->session()->put('admin_last_activity', now());
        }
        
        return $next($request);
    }
}
```

#### Register Admin Middleware

In `app/Http/Kernel.php`:
```php
protected $middlewareGroups = [
    'web' => [
        // ... existing middleware ...
        \App\Http\Middleware\AdminSessionTimeout::class,
    ],
];
```

## Medium Priority Security Enhancements

### 5. CSP Violation Reporting

#### Create CSP Report Controller

Create `app/Http/Controllers/SecurityController.php`:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityController extends Controller
{
    public function cspReport(Request $request)
    {
        $report = $request->getContent();
        
        Log::channel('security')->warning('CSP Violation Detected', [
            'report' => $report,
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'timestamp' => now()
        ]);
        
        return response('', 204);
    }
}
```

#### Update CSP Configuration

In `SecurityService.php`, add reporting:
```php
public function getContentSecurityPolicy(): array
{
    $policy = [
        // ... existing policy ...
        "report-uri" => "/csp-report",
        "report-to" => "csp-endpoint"
    ];
    
    return $policy;
}
```

#### Add Route

In `routes/web.php`:
```php
Route::post('/csp-report', [SecurityController::class, 'cspReport'])
    ->name('csp.report')
    ->withoutMiddleware(['web']); // No CSRF for reports
```

### 6. Rate Limiting Enhancement

#### Create Advanced Rate Limiter

In `app/Providers/RouteServiceProvider.php`:
```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

protected function configureRateLimiting()
{
    // Login rate limiting
    RateLimiter::for('login', function (Request $request) {
        $key = Str::transliterate($request->ip().'|'.$request->input('email'));
        
        return [
            Limit::perMinute(5)->by($key),
            Limit::perHour(20)->by($key)
        ];
    });
    
    // API rate limiting
    RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });
    
    // File upload rate limiting
    RateLimiter::for('uploads', function (Request $request) {
        return [
            Limit::perMinute(10)->by($request->user()->id),
            Limit::perHour(50)->by($request->user()->id)
        ];
    });
}
```

### 7. File Upload Security Enhancement

#### Create Secure File Upload Service

Create `app/Services/SecureFileUploadService.php`:
```php
<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SecureFileUploadService
{
    private const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp'
    ];
    
    private const MAX_FILE_SIZE = 2048; // 2MB in KB
    
    public function uploadDocument(UploadedFile $file, string $directory): array
    {
        $this->validateFile($file);
        
        // Generate secure filename
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $directory . '/' . $filename;
        
        // Scan file for malware (if virus scanner available)
        $this->scanFile($file);
        
        // Store file with restricted permissions
        Storage::putFileAs($directory, $file, $filename, 'private');
        
        return [
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ];
    }
    
    private function validateFile(UploadedFile $file): void
    {
        // MIME type validation
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new \InvalidArgumentException('Invalid file type');
        }
        
        // File size validation
        if ($file->getSize() > (self::MAX_FILE_SIZE * 1024)) {
            throw new \InvalidArgumentException('File too large');
        }
        
        // Extension validation
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new \InvalidArgumentException('Invalid file extension');
        }
        
        // Content-based validation
        $this->validateFileContent($file);
    }
    
    private function validateFileContent(UploadedFile $file): void
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file->getRealPath());
        finfo_close($finfo);
        
        if (!in_array($mimeType, self::ALLOWED_MIME_TYPES)) {
            throw new \InvalidArgumentException('File content does not match extension');
        }
    }
    
    private function scanFile(UploadedFile $file): void
    {
        // Implement virus scanning if available
        // This would integrate with ClamAV or similar
        
        // Basic PHP file detection
        $content = file_get_contents($file->getRealPath());
        $phpPatterns = ['<?php', '<?=', '<script', 'eval(', 'exec(', 'system('];
        
        foreach ($phpPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                throw new \InvalidArgumentException('Suspicious file content detected');
            }
        }
    }
}
```

## Long-term Security Implementations

### 8. Multi-Factor Authentication (MFA)

#### Install Required Package

```bash
composer require pragmarx/google2fa-laravel
```

#### Create MFA Middleware

Create `app/Http/Middleware/RequireTwoFactorAuth.php`:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequireTwoFactorAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if ($user && $user->two_factor_secret && !$request->session()->get('2fa_verified')) {
            return redirect()->route('2fa.verify');
        }
        
        return $next($request);
    }
}
```

### 9. Security Monitoring and Alerting

#### Create Security Event Logger

Create `app/Services/SecurityMonitoringService.php`:
```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SecurityAlert;

class SecurityMonitoringService
{
    public function logSecurityEvent(string $event, array $context = []): void
    {
        $logData = [
            'event' => $event,
            'context' => $context,
            'timestamp' => now(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => auth()->id()
        ];
        
        Log::channel('security')->warning($event, $logData);
        
        if ($this->isHighSeverityEvent($event)) {
            $this->sendSecurityAlert($event, $logData);
        }
    }
    
    private function isHighSeverityEvent(string $event): bool
    {
        $highSeverityEvents = [
            'multiple_failed_logins',
            'privilege_escalation_attempt',
            'unauthorized_file_access',
            'csrf_token_mismatch',
            'xss_attempt_detected'
        ];
        
        return in_array($event, $highSeverityEvents);
    }
    
    private function sendSecurityAlert(string $event, array $data): void
    {
        $adminEmails = config('security.admin_emails', []);
        
        foreach ($adminEmails as $email) {
            Mail::to($email)->queue(new SecurityAlert($event, $data));
        }
    }
}
```

## Testing and Validation

### Security Test Suite

Create `tests/Feature/SecurityTest.php`:
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_csp_headers_are_present()
    {
        $response = $this->get('/');
        
        $response->assertHeader('Content-Security-Policy');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
    }
    
    public function test_xss_protection_in_templates()
    {
        $xssPayload = '<script>alert("xss")</script>';
        
        $response = $this->post('/contact', [
            'message' => $xssPayload
        ]);
        
        $response->assertDontSee($xssPayload, false);
    }
    
    public function test_csrf_protection_enabled()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
        
        $response->assertStatus(419); // CSRF token mismatch
    }
}
```

## Deployment Checklist

### Pre-Deployment Security Validation

1. **CSP Configuration**
   - [ ] Remove `'unsafe-inline'` from script-src
   - [ ] Remove `'unsafe-eval'` from script-src  
   - [ ] Implement nonce system
   - [ ] Test all functionality with strict CSP

2. **JavaScript Security**
   - [ ] Eliminate all inline event handlers
   - [ ] Add SRI to all CDN resources
   - [ ] Implement secure AJAX patterns
   - [ ] Escape all user data in JavaScript context

3. **Session Security**
   - [ ] Enable HTTPS-only cookies
   - [ ] Set HttpOnly flags
   - [ ] Configure SameSite=Strict
   - [ ] Implement session timeouts

4. **File Upload Security**
   - [ ] Validate MIME types
   - [ ] Implement content-based validation
   - [ ] Add virus scanning (if available)
   - [ ] Restrict file permissions

5. **Monitoring and Alerting**
   - [ ] Configure security logging
   - [ ] Set up CSP violation reporting
   - [ ] Implement security alerting
   - [ ] Create incident response procedures

### Post-Deployment Monitoring

1. **CSP Violations**: Monitor and investigate all violations
2. **Failed Login Attempts**: Track and block suspicious IPs  
3. **File Upload Anomalies**: Monitor for malicious uploads
4. **Session Anomalies**: Detect unusual session patterns

## Conclusion

This implementation guide provides a comprehensive roadmap for securing the Laravel insurance management system. The fixes should be implemented in priority order, starting with the critical CSP and XSS vulnerabilities.

**Implementation Timeline:**
- **Week 1**: Critical fixes (CSP, XSS, Security headers)
- **Week 2-3**: Medium priority (SRI, Enhanced sessions, Monitoring)
- **Month 2**: Long-term features (MFA, Advanced monitoring)

Regular security assessments and penetration testing should be conducted quarterly to validate these implementations and identify new vulnerabilities.