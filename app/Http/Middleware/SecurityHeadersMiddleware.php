<?php

namespace App\Http\Middleware;

use App\Services\SecurityService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function __construct(
        private SecurityService $securityService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Add security headers
        $headers = $this->securityService->getSecurityHeaders();
        
        foreach ($headers as $header => $value) {
            $response->headers->set($header, $value);
        }
        
        // Add Content Security Policy
        $csp = $this->securityService->getContentSecurityPolicy();
        $cspString = '';
        
        foreach ($csp as $directive => $value) {
            $cspString .= $directive . ' ' . $value . '; ';
        }
        
        $response->headers->set('Content-Security-Policy', rtrim($cspString, '; '));
        
        return $response;
    }
}