<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Services\ContentSecurityPolicyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CspViolationController extends Controller
{
    public function __construct(
        private ContentSecurityPolicyService $cspService
    ) {}

    public function report(Request $request): Response
    {
        try {
            $violationData = $this->extractViolationData($request);
            
            if ($this->isValidViolation($violationData)) {
                $this->cspService->logCspViolation($violationData);
                
                // Alert if this is a critical violation
                if ($this->isCriticalViolation($violationData)) {
                    $this->alertCriticalViolation($violationData);
                }
            }
            
            return response('', 204); // No Content
        } catch (\Exception $e) {
            \Log::error('CSP violation report processing failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);
            
            return response('', 400); // Bad Request
        }
    }

    private function extractViolationData(Request $request): array
    {
        $rawData = $request->getContent();
        
        // Handle both application/json and application/csp-report content types
        if ($request->header('Content-Type') === 'application/csp-report') {
            $data = json_decode($rawData, true);
            return $data['csp-report'] ?? [];
        }
        
        // Standard JSON format
        $data = json_decode($rawData, true);
        return $data ?? [];
    }

    private function isValidViolation(array $violationData): bool
    {
        // Check for required fields in CSP violation report
        $requiredFields = ['violated-directive', 'original-policy'];
        
        foreach ($requiredFields as $field) {
            if (!isset($violationData[$field])) {
                return false;
            }
        }
        
        // Filter out common false positives
        $falsePositives = [
            'chrome-extension://',
            'moz-extension://',
            'safari-web-extension://',
            'ms-browser-extension://',
        ];
        
        $blockedUri = $violationData['blocked-uri'] ?? '';
        foreach ($falsePositives as $pattern) {
            if (str_contains($blockedUri, $pattern)) {
                return false;
            }
        }
        
        return true;
    }

    private function isCriticalViolation(array $violationData): bool
    {
        $criticalPatterns = [
            'eval',
            'javascript:',
            'data:text/html',
            'unsafe-inline',
            'unsafe-eval',
        ];
        
        $sourceFile = $violationData['source-file'] ?? '';
        $blockedUri = $violationData['blocked-uri'] ?? '';
        $violatedDirective = $violationData['violated-directive'] ?? '';
        
        foreach ($criticalPatterns as $pattern) {
            if (str_contains($sourceFile . $blockedUri . $violatedDirective, $pattern)) {
                return true;
            }
        }
        
        // Check for script-src violations that might indicate XSS attempts
        if (str_contains($violatedDirective, 'script-src') && 
            !empty($blockedUri) && 
            !$this->isKnownGoodSource($blockedUri)) {
            return true;
        }
        
        return false;
    }

    private function isKnownGoodSource(string $uri): bool
    {
        $knownGoodSources = [
            'https://code.jquery.com',
            'https://cdn.jsdelivr.net',
            'https://cdnjs.cloudflare.com',
            'https://kit.fontawesome.com',
            'https://fonts.googleapis.com',
            'https://fonts.gstatic.com',
        ];
        
        foreach ($knownGoodSources as $source) {
            if (str_starts_with($uri, $source)) {
                return true;
            }
        }
        
        return false;
    }

    private function alertCriticalViolation(array $violationData): void
    {
        $alertEmail = config('security.monitoring.notification_email');
        
        if (!$alertEmail) {
            return;
        }
        
        // Queue email alert for critical violations
        \Mail::to($alertEmail)->queue(new \App\Mail\CriticalSecurityViolationAlert([
            'violation_data' => $violationData,
            'timestamp' => now(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->url(),
        ]));
    }
}