<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XssProtectionMiddleware
{
    private array $allowedHtmlTags;
    private array $dangerousPatterns;
    private bool $autoSanitize;

    public function __construct()
    {
        $this->allowedHtmlTags = config('security.xss_protection.allowed_html_tags', []);
        $this->autoSanitize = config('security.xss_protection.sanitize_inputs', true);
        
        $this->dangerousPatterns = [
            // Script injection patterns
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi',
            '/javascript\s*:/i',
            '/on\w+\s*=/i', // Event handlers like onclick, onload
            
            // Data URIs that could contain scripts
            '/data\s*:\s*text\/html/i',
            '/data\s*:\s*application\/javascript/i',
            
            // Meta refresh redirects
            '/<meta[^>]+http-equiv\s*=\s*["\']refresh["\'][^>]*>/i',
            
            // Form injection
            '/<form[^>]*>/i',
            '/<iframe[^>]*>/i',
            '/<object[^>]*>/i',
            '/<embed[^>]*>/i',
            
            // SQL injection patterns in text
            '/(\s|^)(union|select|insert|update|delete|drop|create|alter|exec|execute)\s+/i',
            
            // PHP code injection
            '/<\?\s*(php|=)/i',
        ];
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->autoSanitize && $this->shouldSanitizeRequest($request)) {
            $this->sanitizeRequestData($request);
        }
        
        $response = $next($request);
        
        // Add XSS protection headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        return $response;
    }

    private function shouldSanitizeRequest(Request $request): bool
    {
        // Skip sanitization for API routes that expect raw data
        if ($request->is('api/*') && in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            return false;
        }
        
        // Skip for file uploads
        if ($request->hasFile('*')) {
            return false;
        }
        
        // Only sanitize for web routes with user input
        return $request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH');
    }

    private function sanitizeRequestData(Request $request): void
    {
        $data = $request->all();
        $sanitizedData = $this->sanitizeArray($data);
        
        // Replace request data with sanitized version
        $request->replace($sanitizedData);
    }

    private function sanitizeArray(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $sanitized[$key] = $this->sanitizeString($value, $key);
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }

    private function sanitizeString(string $value, string $fieldName = ''): string
    {
        // Skip sanitization for password fields
        if (str_contains(strtolower($fieldName), 'password')) {
            return $value;
        }
        
        // Skip sanitization for fields that legitimately need HTML
        $htmlAllowedFields = ['description', 'content', 'notes', 'message'];
        $allowHtml = in_array(strtolower($fieldName), $htmlAllowedFields);
        
        if ($allowHtml) {
            return $this->sanitizeHtml($value);
        }
        
        // Standard sanitization for most fields
        return $this->sanitizeText($value);
    }

    private function sanitizeText(string $value): string
    {
        // Remove null bytes
        $value = str_replace("\0", '', $value);
        
        // Check for dangerous patterns first
        foreach ($this->dangerousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                // Log potential XSS attempt
                \Log::channel('security')->warning('Potential XSS attempt detected and blocked', [
                    'value' => $value,
                    'pattern' => $pattern,
                    'user_id' => auth()->id(),
                    'ip' => request()->ip(),
                    'url' => request()->url(),
                ]);
                
                // Remove the dangerous content
                $value = preg_replace($pattern, '', $value);
            }
        }
        
        // Basic HTML entity encoding
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function sanitizeHtml(string $value): string
    {
        // Remove null bytes
        $value = str_replace("\0", '', $value);
        
        // Check for dangerous patterns in HTML content
        foreach ($this->dangerousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                \Log::channel('security')->warning('Dangerous HTML pattern detected', [
                    'value' => substr($value, 0, 200) . '...',
                    'pattern' => $pattern,
                    'user_id' => auth()->id(),
                    'ip' => request()->ip(),
                ]);
                
                $value = preg_replace($pattern, '', $value);
            }
        }
        
        // Strip all HTML except allowed tags
        if (!empty($this->allowedHtmlTags)) {
            $allowedTags = '<' . implode('><', $this->allowedHtmlTags) . '>';
            $value = strip_tags($value, $allowedTags);
        } else {
            $value = strip_tags($value);
        }
        
        return $value;
    }

    public function sanitizeOutput(string $output): string
    {
        // For output sanitization (use in views)
        return htmlspecialchars($output, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}