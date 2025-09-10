<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentSecurityPolicyService
{
    private string $nonce;
    private array $trustedHosts;
    private bool $isDevelopment;
    private bool $reportOnly;

    public function __construct()
    {
        $this->nonce = base64_encode(random_bytes(16));
        $this->trustedHosts = config('security.trusted_hosts', []);
        $this->isDevelopment = app()->environment(['local', 'development']);
        $this->reportOnly = config('security.csp_report_only', false);
    }

    public function getNonce(): string
    {
        return $this->nonce;
    }

    public function getContentSecurityPolicy(Request $request): array
    {
        $isAdminPanel = $this->isAdminPanel($request);
        
        $basePolicy = [
            'default-src' => "'self'",
            'script-src' => $this->getScriptSrc($isAdminPanel),
            'style-src' => $this->getStyleSrc(),
            'img-src' => $this->getImageSrc(),
            'font-src' => $this->getFontSrc(),
            'connect-src' => $this->getConnectSrc(),
            'frame-src' => "'none'",
            'object-src' => "'none'",
            'media-src' => "'self'",
            'form-action' => "'self'",
            'frame-ancestors' => "'none'",
            'base-uri' => "'self'",
            'manifest-src' => "'self'",
        ];

        // Add report URI for monitoring
        if ($reportUri = config('security.csp_report_uri')) {
            $basePolicy['report-uri'] = $reportUri;
            $basePolicy['report-to'] = 'csp-endpoint';
        }

        // Upgrade insecure requests in production
        if (!$this->isDevelopment) {
            $basePolicy['upgrade-insecure-requests'] = '';
        }

        return $basePolicy;
    }

    public function getSecurityHeaders(): array
    {
        return [
            // Core security headers
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY', 
            'X-XSS-Protection' => '0', // Disabled in favor of CSP
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            
            // HSTS for HTTPS
            'Strict-Transport-Security' => $this->getHstsHeader(),
            
            // Cross-Origin policies
            'Cross-Origin-Embedder-Policy' => 'require-corp',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Cross-Origin-Resource-Policy' => 'same-origin',
            
            // Permissions policy (new Permissions API)
            'Permissions-Policy' => $this->getPermissionsPolicy(),
            
            // Additional security headers
            'X-Permitted-Cross-Domain-Policies' => 'none',
            'X-DNS-Prefetch-Control' => 'off',
            'Expect-CT' => 'max-age=86400, enforce',
        ];
    }

    public function getCspHeaderName(): string
    {
        return $this->reportOnly ? 'Content-Security-Policy-Report-Only' : 'Content-Security-Policy';
    }

    public function buildCspString(array $policy): string
    {
        $cspString = '';
        
        foreach ($policy as $directive => $value) {
            if (empty($value)) {
                $cspString .= $directive . '; ';
            } else {
                $cspString .= $directive . ' ' . $value . '; ';
            }
        }
        
        return rtrim($cspString, '; ');
    }

    private function getScriptSrc(bool $isAdminPanel): string
    {
        $sources = ["'self'"];
        
        // Add nonce for inline scripts
        $sources[] = "'nonce-{$this->nonce}'";
        
        // Add trusted CDNs for specific functionality
        $cdnSources = [
            'https://code.jquery.com',
            'https://cdn.jsdelivr.net',
            'https://cdnjs.cloudflare.com',
        ];
        
        // Admin panel may need additional sources for charts/analytics
        if ($isAdminPanel) {
            $cdnSources = array_merge($cdnSources, [
                'https://kit.fontawesome.com',
                'https://cdn.datatables.net',
            ]);
        }
        
        $sources = array_merge($sources, $cdnSources);
        
        // Add trusted hosts
        if (!empty($this->trustedHosts)) {
            $sources = array_merge($sources, $this->trustedHosts);
        }
        
        // Development mode may need eval for hot reloading
        if ($this->isDevelopment) {
            $sources[] = "'unsafe-eval'"; // Only for development
        }
        
        return implode(' ', $sources);
    }

    private function getStyleSrc(): string
    {
        $sources = ["'self'"];
        
        // Add nonce for inline styles
        $sources[] = "'nonce-{$this->nonce}'";
        
        // Add trusted style CDNs
        $styleCdns = [
            'https://fonts.googleapis.com',
            'https://cdn.jsdelivr.net',
            'https://cdnjs.cloudflare.com',
            'https://kit.fontawesome.com',
        ];
        
        $sources = array_merge($sources, $styleCdns);
        
        return implode(' ', $sources);
    }

    private function getImageSrc(): string
    {
        return "'self' data: https: blob:";
    }

    private function getFontSrc(): string
    {
        return "'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com https://kit.fontawesome.com";
    }

    private function getConnectSrc(): string
    {
        $sources = ["'self'"];
        
        // Add API endpoints if different domain
        if ($apiDomain = config('app.api_domain')) {
            $sources[] = $apiDomain;
        }
        
        // Add WhatsApp API domain if configured
        if ($whatsappDomain = config('whatsapp.api_domain')) {
            $sources[] = $whatsappDomain;
        }
        
        return implode(' ', $sources);
    }

    private function getHstsHeader(): string
    {
        $maxAge = config('security.hsts_max_age', 31536000); // 1 year default
        $includeSubdomains = config('security.hsts_include_subdomains', true);
        $preload = config('security.hsts_preload', false);
        
        $header = "max-age={$maxAge}";
        
        if ($includeSubdomains) {
            $header .= '; includeSubDomains';
        }
        
        if ($preload) {
            $header .= '; preload';
        }
        
        return $header;
    }

    private function getPermissionsPolicy(): string
    {
        return 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), speaker=()';
    }

    private function isAdminPanel(Request $request): bool
    {
        return !str_starts_with($request->path(), 'customer');
    }

    public function generateNonceForView(): string
    {
        // Store nonce in view data for Blade templates
        return $this->nonce;
    }

    public function isNonceValid(string $providedNonce): bool
    {
        return hash_equals($this->nonce, $providedNonce);
    }

    public function logCspViolation(array $violationData): void
    {
        \Log::channel('security')->warning('CSP Violation detected', [
            'violation' => $violationData,
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->url(),
        ]);
    }
}