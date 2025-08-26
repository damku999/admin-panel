<?php

namespace App\Exceptions;

use App\Models\CustomerAuditLog;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // Log security-related exceptions
            if ($this->isSecurityException($e)) {
                $this->logSecurityException($e);
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e): Response
    {
        // Handle security exceptions with sanitized messages
        if ($this->isSecurityException($e)) {
            return $this->renderSecurityException($request, $e);
        }

        // Handle database exceptions without revealing schema
        if ($e instanceof QueryException) {
            return $this->renderDatabaseException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Determine if this is a security-related exception.
     */
    protected function isSecurityException(Throwable $e): bool
    {
        return $e instanceof AuthorizationException ||
               $e instanceof \InvalidArgumentException ||
               str_contains(strtolower($e->getMessage()), 'sql') ||
               str_contains(strtolower($e->getMessage()), 'injection') ||
               str_contains(strtolower($e->getMessage()), 'unauthorized');
    }

    /**
     * Log security exceptions with full details for investigation.
     */
    protected function logSecurityException(Throwable $e): void
    {
        $customer = Auth::guard('customer')->user();
        
        // Log to application logs
        Log::warning('Security exception detected', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'customer_id' => $customer?->id,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'url' => request()?->fullUrl(),
            'method' => request()?->method(),
            'session_id' => session()?->getId()
        ]);

        // Log to customer audit if customer is authenticated
        if ($customer) {
            try {
                CustomerAuditLog::create([
                    'customer_id' => $customer->id,
                    'action' => 'security_exception',
                    'description' => 'Security exception occurred during request',
                    'ip_address' => request()?->ip(),
                    'user_agent' => request()?->userAgent(),
                    'session_id' => session()?->getId(),
                    'success' => false,
                    'failure_reason' => 'Security exception: ' . get_class($e),
                    'metadata' => [
                        'exception_class' => get_class($e),
                        'exception_file' => $e->getFile(),
                        'exception_line' => $e->getLine(),
                        'request_url' => request()?->fullUrl(),
                        'request_method' => request()?->method(),
                        'security_incident' => true
                    ]
                ]);
            } catch (\Exception $auditException) {
                // If audit logging fails, log to application logs
                Log::error('Failed to log security exception to audit', [
                    'original_exception' => get_class($e),
                    'audit_exception' => $auditException->getMessage()
                ]);
            }
        }
    }

    /**
     * Render security exceptions with sanitized user-facing messages.
     */
    protected function renderSecurityException(Request $request, Throwable $e): Response
    {
        $isAjax = $request->expectsJson() || $request->ajax();
        
        // Don't expose internal exception details to users
        $userMessage = $this->getSafeSecurityMessage($e);
        
        if ($isAjax) {
            return response()->json([
                'error' => $userMessage,
                'message' => $userMessage
            ], 403);
        }

        // For web requests, redirect with safe error message
        $redirectRoute = $this->getSecurityRedirectRoute($request);
        
        return redirect()->route($redirectRoute)
            ->with('error', $userMessage);
    }

    /**
     * Get a safe, user-facing message for security exceptions.
     */
    protected function getSafeSecurityMessage(Throwable $e): string
    {
        if ($e instanceof AuthorizationException) {
            return 'You do not have permission to perform this action.';
        }
        
        if (str_contains(strtolower($e->getMessage()), 'sql') || 
            str_contains(strtolower($e->getMessage()), 'injection')) {
            return 'Invalid request detected. Please try again.';
        }
        
        if (str_contains(strtolower($e->getMessage()), 'family') ||
            str_contains(strtolower($e->getMessage()), 'group')) {
            return 'Family data access error. Please contact support if this persists.';
        }
        
        // Generic safe message
        return 'An error occurred while processing your request. Please try again.';
    }

    /**
     * Determine appropriate redirect route based on request context.
     */
    protected function getSecurityRedirectRoute(Request $request): string
    {
        $customer = Auth::guard('customer')->user();
        
        if (!$customer) {
            return 'customer.login';
        }
        
        // Redirect to dashboard for authenticated users
        return 'customer.dashboard';
    }

    /**
     * Render database exceptions without revealing schema information.
     */
    protected function renderDatabaseException(Request $request, QueryException $e): Response
    {
        // Log the full database error for debugging
        Log::error('Database exception occurred', [
            'message' => $e->getMessage(),
            'sql' => $e->getSql(),
            'bindings' => $e->getBindings(),
            'customer_id' => Auth::guard('customer')->id(),
            'ip_address' => $request->ip(),
            'url' => $request->fullUrl()
        ]);

        $isAjax = $request->expectsJson() || $request->ajax();
        $userMessage = 'A database error occurred. Please try again later.';
        
        if ($isAjax) {
            return response()->json([
                'error' => $userMessage,
                'message' => $userMessage
            ], 500);
        }

        $redirectRoute = Auth::guard('customer')->check() ? 'customer.dashboard' : 'customer.login';
        
        return redirect()->route($redirectRoute)
            ->with('error', $userMessage);
    }
}
