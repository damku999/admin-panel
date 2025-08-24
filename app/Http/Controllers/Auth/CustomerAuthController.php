<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class CustomerAuthController extends Controller
{
    use ThrottlesLogins;

    protected $maxAttempts = 5;
    protected $decayMinutes = 15;

    public function __construct()
    {
        $this->middleware('guest:customer')->except('logout', 'dashboard');
        $this->middleware('auth:customer')->only('logout', 'dashboard');
    }

    /**
     * Show the customer login form.
     */
    public function showLoginForm()
    {
        return view('customer.auth.login');
    }

    /**
     * Handle customer login attempt.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Check for too many login attempts
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            // Log successful login
            CustomerAuditLog::logAction('login', 'Customer logged in successfully', [
                'login_method' => 'email_password',
                'remember_me' => $request->boolean('remember')
            ]);
            
            return $this->sendLoginResponse($request);
        }

        // Log failed login attempt
        $customer = Customer::where('email', $request->email)->first();
        if ($customer) {
            CustomerAuditLog::create([
                'customer_id' => $customer->id,
                'action' => 'login_failed',
                'description' => 'Failed login attempt with incorrect password',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId(),
                'success' => false,
                'failure_reason' => 'Invalid credentials'
            ]);
        }

        // Increment login attempts
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the login request.
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
    }

    /**
     * Attempt to log the customer into the application.
     */
    protected function attemptLogin(Request $request): bool
    {
        $credentials = $this->credentials($request);
        $credentials['status'] = true; // Only allow active customers to login

        return Auth::guard('customer')->attempt($credentials, $request->boolean('remember'));
    }

    /**
     * Get the needed authorization credentials from the request.
     */
    protected function credentials(Request $request): array
    {
        return $request->only('email', 'password');
    }

    /**
     * Send the response after the customer was authenticated.
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);

        $customer = Auth::guard('customer')->user();

        // Check if customer needs to change password
        if ($customer->needsPasswordChange()) {
            return redirect()->route('customer.change-password')
                ->with('warning', 'You must change your password before accessing the dashboard.');
        }

        // Check if email needs verification
        if (!$customer->hasVerifiedEmail() && $customer->email_verification_token) {
            return redirect()->route('customer.verify-email-notice')
                ->with('info', 'Please verify your email address to continue.');
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the failed login response instance.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return back()->withErrors([
            'email' => 'These credentials do not match our records or your account is inactive.',
        ])->withInput($request->only('email'));
    }

    /**
     * Where to redirect customers after login.
     */
    protected function redirectPath(): string
    {
        return route('customer.dashboard');
    }

    /**
     * Log the customer out of the application.
     */
    public function logout(Request $request)
    {
        // Log logout before ending session
        CustomerAuditLog::logAction('logout', 'Customer logged out successfully');
        
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Ensure we redirect to customer login with success message
        return redirect()->route('customer.login')
            ->with('message', 'You have been logged out successfully.');
    }

    /**
     * Show the customer dashboard.
     */
    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();

        // Get family policies that this customer can view
        $familyPolicies = collect();
        $expiringPolicies = collect();
        
        if ($customer->hasFamily()) {
            $allPolicies = $customer->getViewableInsurance()
                ->orderBy('created_at', 'desc')
                ->get();
            
            $familyPolicies = $allPolicies;
            
            // Get policies expiring in next 30 days
            $thirtyDaysFromNow = now()->addDays(30);
            $expiringPolicies = $allPolicies->filter(function ($policy) use ($thirtyDaysFromNow) {
                if (!$policy->expired_date) return false;
                
                $expiryDate = \Carbon\Carbon::parse($policy->expired_date);
                return $expiryDate->isFuture() && $expiryDate->lte($thirtyDaysFromNow);
            });
        }

        return view('customer.dashboard', [
            'customer' => $customer,
            'familyGroup' => $customer->familyGroup,
            'familyMembers' => $customer->familyMembers ?? collect(),
            'isHead' => $customer->isFamilyHead(),
            'familyPolicies' => $familyPolicies,
            'expiringPolicies' => $expiringPolicies,
        ]);
    }

    /**
     * Get the login username to be used by the throttler.
     */
    public function username(): string
    {
        return 'email';
    }

    /**
     * Show the change password form.
     */
    public function showChangePasswordForm()
    {
        return view('customer.auth.change-password');
    }

    /**
     * Handle password change request.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $customer = Auth::guard('customer')->user();

        // Verify current password
        if (!Hash::check($request->current_password, $customer->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password
        $customer->changePassword($request->password);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Password changed successfully.');
    }

    /**
     * Show email verification notice.
     */
    public function showEmailVerificationNotice()
    {
        $customer = Auth::guard('customer')->user();
        return view('customer.auth.verify-email', compact('customer'));
    }

    /**
     * Handle email verification.
     */
    public function verifyEmail(Request $request, $token)
    {
        $customer = Customer::where('email_verification_token', $token)->first();

        if (!$customer) {
            return redirect()->route('customer.login')
                ->with('error', 'Invalid verification link.');
        }

        if ($customer->verifyEmail($token)) {
            Auth::guard('customer')->login($customer);
            return redirect()->route('customer.dashboard')
                ->with('success', 'Email verified successfully.');
        }

        return redirect()->route('customer.login')
            ->with('error', 'Email verification failed.');
    }

    /**
     * Resend email verification.
     */
    public function resendVerification(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        if ($customer->hasVerifiedEmail()) {
            return redirect()->route('customer.dashboard');
        }

        $token = $customer->generateEmailVerificationToken();

        // TODO: Send verification email
        \Log::info('Email verification resend', [
            'customer_id' => $customer->id,
            'email' => $customer->email,
            'token' => $token,
            'verification_url' => route('customer.verify-email', $token),
        ]);

        return back()->with('success', 'Verification link sent to your email.');
    }

    /**
     * Show password reset request form.
     */
    public function showPasswordResetForm()
    {
        return view('customer.auth.password-reset');
    }

    /**
     * Handle password reset request.
     */
    public function sendPasswordResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            return back()->withErrors(['email' => 'Email address not found.']);
        }

        $token = $customer->generateEmailVerificationToken();
        $customer->update(['password_reset_sent_at' => now()]);

        // TODO: Send password reset email
        \Log::info('Password reset requested', [
            'customer_id' => $customer->id,
            'email' => $customer->email,
            'token' => $token,
            'reset_url' => route('customer.password.reset', $token),
        ]);

        return back()->with('success', 'Password reset link sent to your email.');
    }

    /**
     * Show password reset form.
     */
    public function showPasswordResetFormWithToken($token)
    {
        $customer = Customer::where('email_verification_token', $token)->first();

        if (!$customer) {
            return redirect()->route('customer.login')
                ->with('error', 'Invalid reset link.');
        }

        return view('customer.auth.reset-password', compact('token'));
    }

    /**
     * Handle password reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $customer = Customer::where('email_verification_token', $request->token)->first();

        if (!$customer) {
            return redirect()->route('customer.login')
                ->with('error', 'Invalid reset token.');
        }

        $customer->changePassword($request->password);

        return redirect()->route('customer.login')
            ->with('success', 'Password reset successfully. You can now login with your new password.');
    }

    /**
     * Show the customer profile page.
     */
    public function showProfile()
    {
        $customer = Auth::guard('customer')->user();

        return view('customer.profile', [
            'customer' => $customer,
            'familyGroup' => $customer->familyGroup,
            'familyMembers' => $customer->familyMembers ?? collect(),
            'isHead' => $customer->isFamilyHead(),
        ]);
    }

    /**
     * Show all policies for the customer.
     */
    public function showPolicies()
    {
        $customer = Auth::guard('customer')->user();
        
        // Check authorization for viewing family data
        if (!$customer->can('viewFamilyData')) {
            return redirect()->route('customer.dashboard')
                ->with('error', 'You do not have permission to view family policies.');
        }
        
        $allPolicies = collect();
        if ($customer->hasFamily()) {
            $allPolicies = $customer->getViewableInsurance()->get();
        }

        // Log policy list access
        CustomerAuditLog::logAction('view_policies', 'Customer viewed policy list', [
            'policy_count' => $allPolicies->count(),
            'is_family_head' => $customer->isFamilyHead()
        ]);
        
        // Categorize policies by status
        $activePolicies = $allPolicies->filter(function ($policy) {
            if (!$policy->expired_date) return true;
            return \Carbon\Carbon::parse($policy->expired_date)->isFuture();
        });
        
        $expiredPolicies = $allPolicies->filter(function ($policy) {
            if (!$policy->expired_date) return false;
            return \Carbon\Carbon::parse($policy->expired_date)->isPast();
        });
        
        return view('customer.policies', [
            'customer' => $customer,
            'activePolicies' => $activePolicies,
            'expiredPolicies' => $expiredPolicies,
            'isHead' => $customer->isFamilyHead(),
        ]);
    }

    /**
     * Show detailed view of a specific policy.
     */
    public function showPolicyDetail($policyId)
    {
        $customer = Auth::guard('customer')->user();
        
        // Get the policy and verify access
        $policy = \App\Models\CustomerInsurance::with(['customer', 'insuranceCompany', 'policyType', 'premiumType'])
            ->findOrFail($policyId);
        
        // Check authorization using policy
        if (!$customer->can('viewPolicy', $policy)) {
            CustomerAuditLog::logFailure('view_policy_detail', 'Unauthorized access attempt', [
                'policy_id' => $policyId,
                'policy_no' => $policy->policy_no
            ]);
            abort(403, 'You do not have permission to view this policy.');
        }

        // Log policy access
        CustomerAuditLog::logPolicyAction('view_policy_detail', $policy);
        
        // Calculate policy status and renewal info
        $isExpired = $policy->expired_date ? \Carbon\Carbon::parse($policy->expired_date)->isPast() : false;
        $isExpiringSoon = false;
        $daysUntilExpiry = null;
        
        if ($policy->expired_date && !$isExpired) {
            $expiryDate = \Carbon\Carbon::parse($policy->expired_date);
            $daysUntilExpiry = now()->diffInDays($expiryDate, false);
            $isExpiringSoon = $daysUntilExpiry <= 30;
        }
        
        return view('customer.policy-detail', [
            'customer' => $customer,
            'policy' => $policy,
            'isExpired' => $isExpired,
            'isExpiringSoon' => $isExpiringSoon,
            'daysUntilExpiry' => $daysUntilExpiry,
            'isHead' => $customer->isFamilyHead(),
        ]);
    }

    /**
     * Download policy document.
     */
    public function downloadPolicy($policyId)
    {
        $customer = Auth::guard('customer')->user();
        
        // Get the policy and verify access
        $policy = \App\Models\CustomerInsurance::findOrFail($policyId);
        
        // Check authorization using policy
        if (!$customer->can('downloadPolicy', $policy)) {
            CustomerAuditLog::logFailure('download_policy', 'Unauthorized download attempt', [
                'policy_id' => $policyId,
                'policy_no' => $policy->policy_no
            ]);
            abort(403, 'You do not have permission to download this policy document.');
        }
        
        // Check if policy document exists
        if (!$policy->policy_document_path) {
            return redirect()->back()->with('error', 'No policy document is available for download.');
        }
        
        // SECURITY FIX: Validate and sanitize file path to prevent path traversal attacks
        $documentPath = $policy->policy_document_path;
        
        // Remove any path traversal attempts and normalize path
        $documentPath = str_replace(['../', '..\\', '../', '..\\'], '', $documentPath);
        $documentPath = ltrim($documentPath, '/\\');
        
        // Validate that the path only contains allowed characters (alphanumeric, dash, underscore, slash, dot)
        if (!preg_match('/^[a-zA-Z0-9\/_\-\.]+$/', $documentPath)) {
            CustomerAuditLog::logFailure('download_policy', 'Invalid file path detected', [
                'policy_id' => $policyId,
                'policy_no' => $policy->policy_no,
                'attempted_path' => $policy->policy_document_path,
                'security_violation' => 'path_traversal_attempt'
            ]);
            return redirect()->back()->with('error', 'Invalid policy document path.');
        }
        
        // Ensure the path stays within the allowed directory structure
        $allowedDirectory = storage_path('app/public/');
        $fullPath = realpath($allowedDirectory . $documentPath);
        
        // Verify the resolved path is within the allowed directory
        if (!$fullPath || !str_starts_with($fullPath, $allowedDirectory)) {
            CustomerAuditLog::logFailure('download_policy', 'Path traversal attack blocked', [
                'policy_id' => $policyId,
                'policy_no' => $policy->policy_no,
                'attempted_path' => $policy->policy_document_path,
                'resolved_path' => $fullPath,
                'security_violation' => 'directory_traversal_blocked'
            ]);
            return redirect()->back()->with('error', 'Access denied. Invalid file path.');
        }
        
        // Check if file exists at the validated path
        if (!file_exists($fullPath)) {
            CustomerAuditLog::logFailure('download_policy', 'Policy document not found', [
                'policy_id' => $policyId,
                'policy_no' => $policy->policy_no,
                'file_path' => $documentPath
            ]);
            return redirect()->back()->with('error', 'Policy document file not found on server.');
        }
        
        // Validate file type to ensure it's a PDF (additional security layer)
        $fileInfo = pathinfo($fullPath);
        $allowedExtensions = ['pdf', 'PDF'];
        
        if (!isset($fileInfo['extension']) || !in_array($fileInfo['extension'], $allowedExtensions)) {
            CustomerAuditLog::logFailure('download_policy', 'Invalid file type detected', [
                'policy_id' => $policyId,
                'policy_no' => $policy->policy_no,
                'file_path' => $documentPath,
                'file_extension' => $fileInfo['extension'] ?? 'none',
                'security_violation' => 'invalid_file_type'
            ]);
            return redirect()->back()->with('error', 'Only PDF documents can be downloaded.');
        }
        
        $fileName = 'Policy_' . $policy->policy_no . '_' . $policy->customer->name . '.pdf';
        
        // Log successful download with security validation details
        CustomerAuditLog::logPolicyAction('download_policy', $policy, 'Policy document downloaded successfully', [
            'file_path' => $documentPath,
            'validated_path' => $fullPath,
            'file_name' => $fileName,
            'file_size' => filesize($fullPath),
            'security_checks_passed' => true
        ]);
        
        return response()->download($fullPath, $fileName);
    }
}