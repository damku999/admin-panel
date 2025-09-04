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
        $this->middleware('guest:customer')->except([
            'logout',
            'dashboard',
            'showChangePasswordForm',
            'changePassword',
            'showProfile',
            'showPolicies',
            'showPolicyDetail',
            'downloadPolicy',
            'showEmailVerificationNotice',
            'resendVerification',
            'showQuotations',
            'showQuotationDetail',
            'downloadQuotation',
            'showFamilyMemberProfile',
            'showFamilyMemberPasswordForm',
            'updateFamilyMemberPassword'
        ]);
        $this->middleware('auth:customer')->only([
            'logout',
            'dashboard',
            'changePassword',           // POST method for changing password
            'showChangePasswordForm',   // GET method for showing form
            'showProfile',
            'showPolicies',
            'showPolicyDetail',
            'downloadPolicy',
            'showEmailVerificationNotice',
            'resendVerification',
            'showQuotations',
            'showQuotationDetail',
            'downloadQuotation',
            'showFamilyMemberProfile',
            'showFamilyMemberPasswordForm',
            'updateFamilyMemberPassword'
        ]);
    }

    /**
     * Show the customer login form.
     */
    public function showLoginForm()
    {
        $isHead = false;
        return view('customer.auth.login', compact('isHead'));
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

        // Set initial session activity timestamp for timeout tracking
        $request->session()->put('customer_last_activity', now()->format('Y-m-d H:i:s'));

        $customer = Auth::guard('customer')->user();

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

        // Get family policies and quotations that this customer can view
        $familyPolicies = collect();
        $expiringPolicies = collect();
        $recentQuotations = collect();

        if ($customer->hasFamily()) {
            try {
                $allPolicies = $customer->getViewableInsurance()
                    ->orderBy('created_at', 'desc')
                    ->get();

                // For dashboard, show only active policies (not expired)
                $familyPolicies = $allPolicies->filter(function ($policy) {
                    if (!$policy->expired_date) {
                        return true; // No expiry date means active
                    }
                    return \Carbon\Carbon::parse($policy->expired_date)->isFuture();
                });

                // Get policies expiring in next 30 days
                $thirtyDaysFromNow = now()->addDays(30);
                $expiringPolicies = $allPolicies->filter(function ($policy) use ($thirtyDaysFromNow) {
                    if (!$policy->expired_date) {
                        return false;
                    }

                    $expiryDate = \Carbon\Carbon::parse($policy->expired_date);
                    return $expiryDate->isFuture() && $expiryDate->lte($thirtyDaysFromNow);
                });

                // Get recent quotations
                if ($customer->isFamilyHead()) {
                    $recentQuotations = \App\Models\Quotation::with(['quotationCompanies.insuranceCompany'])
                        ->whereHas('customer', function ($query) use ($customer) {
                            $query->where('family_group_id', $customer->family_group_id);
                        })
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                } else {
                    $recentQuotations = $customer->quotations()
                        ->with(['quotationCompanies.insuranceCompany'])
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                }
            } catch (\InvalidArgumentException $e) {
                // Log SQL injection attempt
                CustomerAuditLog::create([
                    'customer_id' => $customer->id,
                    'action' => 'sql_injection_attempt',
                    'description' => 'Invalid family group ID detected in dashboard query',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'session_id' => session()->getId(),
                    'success' => false,
                    'failure_reason' => 'SQL injection prevention - Invalid family group ID',
                    'metadata' => [
                        'error_message' => $e->getMessage(),
                        'family_group_id' => $customer->family_group_id,
                        'security_violation' => 'sql_injection_attempt',
                        'location' => 'dashboard'
                    ]
                ]);

                // Show dashboard with error but don't crash
                $familyPolicies = collect();
                $expiringPolicies = collect();
                $recentQuotations = collect();
                session()->flash('error', 'Security error: Unable to load family data.');
            }
        }

        return view('customer.dashboard', [
            'customer' => $customer,
            'familyGroup' => $customer->familyGroup,
            'familyMembers' => $customer->familyMembers ?? collect(),
            'isHead' => $customer->isFamilyHead(),
            'familyPolicies' => $familyPolicies,
            'expiringPolicies' => $expiringPolicies,
            'recentQuotations' => $recentQuotations,
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
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            \Log::warning('No authenticated customer found in change password form');
            return redirect()->route('customer.login')->with('error', 'Please login to change password.');
        }

        return view('customer.auth.change-password', ['isHead' => $customer->isFamilyHead()]);
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
        $isHead = $customer->isFamilyHead();
        return view('customer.auth.verify-email', compact('customer', 'isHead'));
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

        // Send verification email
        try {
            \Mail::to($customer->email)->send(new \App\Mail\CustomerEmailVerificationMail($customer, $token));

            \Log::info('Email verification email sent successfully', [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'token' => $token,
                'verification_url' => route('customer.verify-email', $token),
            ]);

            return back()->with('success', 'Verification link sent to your email.');
        } catch (\Exception $e) {
            \Log::error('Failed to send email verification email', [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['email' => 'Failed to send verification email. Please try again later.']);
        }
    }

    /**
     * Show password reset request form.
     */
    public function showPasswordResetForm()
    {
        $isHead = false;
        return view('customer.auth.password-reset', compact('isHead'));
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

        $token = $customer->generatePasswordResetToken();

        // Send password reset email
        try {
            \Mail::to($customer->email)->send(new \App\Mail\CustomerPasswordResetMail($customer, $token));

            \Log::info('Password reset email sent successfully', [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'token' => $token,
                'reset_url' => route('customer.password.reset', ['token' => $token, 'email' => $customer->email]),
            ]);

            return back()->with('success', 'Password reset link sent to your email.');
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email', [
                'customer_id' => $customer->id,
                'email' => $customer->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['email' => 'Failed to send password reset email. Please try again later.']);
        }
    }

    /**
     * Show password reset form.
     */
    public function showPasswordResetFormWithToken($token)
    {
        $customer = Customer::where('password_reset_token', $token)->first();

        if (!$customer || !$customer->verifyPasswordResetToken($token)) {
            return redirect()->route('customer.login')
                ->with('error', 'Invalid or expired reset link.');
        }

        $isHead = $customer->isFamilyHead();
        return view('customer.auth.reset-password', compact('token', 'isHead'));
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

        $customer = Customer::where('password_reset_token', $request->token)->first();

        if (!$customer || !$customer->verifyPasswordResetToken($request->token)) {
            CustomerAuditLog::create([
                'customer_id' => $customer?->id,
                'action' => 'password_reset_failed',
                'description' => 'Failed password reset attempt with invalid or expired token',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId(),
                'success' => false,
                'failure_reason' => 'Invalid or expired reset token',
                'metadata' => [
                    'token_provided' => !empty($request->token),
                    'customer_found' => !is_null($customer),
                    'security_violation' => 'invalid_password_reset_token'
                ]
            ]);

            return redirect()->route('customer.login')
                ->with('error', 'Invalid or expired reset token.');
        }

        // Clear the reset token before changing password
        $customer->clearPasswordResetToken();
        $customer->changePassword($request->password);

        // Log successful password reset
        CustomerAuditLog::create([
            'customer_id' => $customer->id,
            'action' => 'password_reset_success',
            'description' => 'Password reset successfully using valid token',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'success' => true,
            'metadata' => [
                'password_change_method' => 'reset_token',
                'security_checks_passed' => true
            ]
        ]);

        return redirect()->route('customer.login')
            ->with('success', 'Password reset successfully. You can now login with your new password.');
    }

    /**
     * Show the customer profile page.
     */
    public function showProfile()
    {
        $customer = Auth::guard('customer')->user();

        // Get all family members (Customer records) in the same family group
        $familyMembers = collect();
        if ($customer->familyGroup) {
            $familyMembers = Customer::where('family_group_id', $customer->familyGroup->id)
                ->with('familyMember')  // Load the relationship info (relationship, is_head, etc.)
                ->get();
        }

        return view('customer.profile', [
            'customer' => $customer,
            'familyGroup' => $customer->familyGroup,
            'familyMembers' => $familyMembers,
            'isHead' => $customer->isFamilyHead(),
        ]);
    }

    /**
     * Show family member profile (read-only, any family member can view).
     */
    public function showFamilyMemberProfile(Customer $member)
    {
        $customer = Auth::guard('customer')->user();

        // Security: Ensure both customers have family groups
        if (!$customer->hasFamily()) {
            abort(403, 'You must be part of a family group to view family member profiles.');
        }

        // Security: Ensure the member is in the same family
        if (!$customer->isInSameFamilyAs($member)) {
            abort(403, 'You can only view profiles of your family members.');
        }

        // Prevent viewing your own profile via this route
        if ($customer->id === $member->id) {
            return redirect()->route('customer.profile');
        }

        return view('customer.family-member-profile', [
            'customer' => $customer,
            'member' => $member,
            'familyGroup' => $customer->familyGroup->load('members.customer'),
            'isViewingMember' => true,
            'isHead' => $customer->isFamilyHead(),
        ]);
    }

    /**
     * Show family member password change form (any family member can change).
     */
    public function showFamilyMemberPasswordForm(Customer $member)
    {
        $customer = Auth::guard('customer')->user();

        // Security: Ensure both customers have family groups
        if (!$customer->hasFamily()) {
            abort(403, 'You must be part of a family group to change family member passwords.');
        }

        // Security: Ensure the member is in the same family
        if (!$customer->isInSameFamilyAs($member)) {
            abort(403, 'You can only change passwords of your family members.');
        }

        // Security: Prevent changing your own password via this route
        if ($customer->id === $member->id) {
            return redirect()->route('customer.change-password')
                ->with('info', 'Please use the regular password change form for your own account.');
        }

        return view('customer.family-member-password', [
            'customer' => $customer,
            'member' => $member,
            'familyGroup' => $customer->familyGroup->load('members.customer'),
            'isHead' => $customer->isFamilyHead(),
        ]);
    }

    /**
     * Update family member password (any family member can change, no old password required).
     */
    public function updateFamilyMemberPassword(Customer $member, Request $request)
    {
        $customer = Auth::guard('customer')->user();

        // Security: Ensure both customers have family groups
        if (!$customer->hasFamily()) {
            abort(403, 'You must be part of a family group to change family member passwords.');
        }

        // Security: Ensure the member is in the same family
        if (!$customer->isInSameFamilyAs($member)) {
            abort(403, 'You can only change passwords of your family members.');
        }

        // Security: Prevent changing your own password via this route
        if ($customer->id === $member->id) {
            return redirect()->route('customer.change-password')
                ->with('info', 'Please use the regular password change form for your own account.');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Update the family member's password
        $member->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
            'must_change_password' => false, // Reset the flag
        ]);

        // Log the password change action
        CustomerAuditLog::create([
            'customer_id' => $member->id,
            'action' => 'password_changed_by_family_head',
            'description' => 'Password changed by family head: ' . $customer->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'success' => true,
            'metadata' => [
                'changed_by_family_head_id' => $customer->id,
                'changed_by_family_head_name' => $customer->name,
            ]
        ]);

        // Also log in family head's audit log
        CustomerAuditLog::create([
            'customer_id' => $customer->id,
            'action' => 'changed_family_member_password',
            'description' => 'Changed password for family member: ' . $member->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'success' => true,
            'metadata' => [
                'family_member_id' => $member->id,
                'family_member_name' => $member->name,
            ]
        ]);

        return redirect()->route('customer.profile')
            ->with('success', 'Password successfully changed for ' . $member->name . '.');
    }

    /**
     * Show all policies for the customer.
     */
    public function showPolicies()
    {
        $customer = Auth::guard('customer')->user();

        // Check authorization for viewing family data
        // if (!$customer->can('viewFamilyData')) {
        //     return redirect()->route('customer.dashboard')
        //         ->with('error', 'You do not have permission to view family policies.');
        // }

        $allPolicies = collect();
        if ($customer->hasFamily()) {
            try {
                $allPolicies = $customer->getViewableInsurance()->get();
            } catch (\InvalidArgumentException $e) {
                // Log SQL injection attempt or data manipulation
                CustomerAuditLog::create([
                    'customer_id' => $customer->id,
                    'action' => 'sql_injection_attempt',
                    'description' => 'Invalid family group ID detected in policy query',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'session_id' => session()->getId(),
                    'success' => false,
                    'failure_reason' => 'SQL injection prevention - Invalid family group ID',
                    'metadata' => [
                        'error_message' => $e->getMessage(),
                        'family_group_id' => $customer->family_group_id,
                        'security_violation' => 'sql_injection_attempt'
                    ]
                ]);

                return redirect()->route('customer.dashboard')
                    ->with('error', 'Security error: Invalid family data detected.');
            }
        }

        // Log policy list access
        CustomerAuditLog::logAction('view_policies', 'Customer viewed policy list', [
            'policy_count' => $allPolicies->count(),
            'is_family_head' => $customer->isFamilyHead()
        ]);

        // Categorize policies by status
        $activePolicies = $allPolicies->filter(function ($policy) {
            if (!$policy->expired_date) {
                return true;
            }
            return \Carbon\Carbon::parse($policy->expired_date)->isFuture();
        });

        $expiredPolicies = $allPolicies->filter(function ($policy) {
            if (!$policy->expired_date) {
                return false;
            }
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

        // Check authorization - customer can only view policies from their family group
        $hasAccess = false;

        if ($customer->isFamilyHead()) {
            // Family head can view all policies in their family group
            $hasAccess = $policy->customer->family_group_id === $customer->family_group_id;
        } else {
            // Regular family member can only view their own policies
            $hasAccess = $policy->customer_id === $customer->id;
        }

        if (!$hasAccess) {
            CustomerAuditLog::logFailure('view_policy_detail', 'Unauthorized access attempt to policy outside family group', [
                'policy_id' => $policyId,
                'policy_no' => $policy->policy_no,
                'policy_customer_id' => $policy->customer_id,
                'policy_family_group_id' => $policy->customer->family_group_id,
                'accessing_customer_id' => $customer->id,
                'accessing_family_group_id' => $customer->family_group_id,
                'is_family_head' => $customer->isFamilyHead(),
                'security_violation' => 'unauthorized_policy_access'
            ]);

            return redirect()->route('customer.policies')
                ->with('error', 'You do not have permission to view this policy.');
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

        // Check authorization - customer can only download policies from their family group
        $hasAccess = false;

        if ($customer->isFamilyHead()) {
            // Family head can download all policies in their family group
            $hasAccess = $policy->customer->family_group_id === $customer->family_group_id;
        } else {
            // Regular family member can only download their own policies
            $hasAccess = $policy->customer_id === $customer->id;
        }

        if (!$hasAccess) {
            CustomerAuditLog::logFailure('download_policy', 'Unauthorized download attempt to policy outside family group', [
                'policy_id' => $policyId,
                'policy_no' => $policy->policy_no,
                'policy_customer_id' => $policy->customer_id,
                'policy_family_group_id' => $policy->customer->family_group_id,
                'accessing_customer_id' => $customer->id,
                'accessing_family_group_id' => $customer->family_group_id,
                'is_family_head' => $customer->isFamilyHead(),
                'security_violation' => 'unauthorized_policy_download'
            ]);

            return redirect()->route('customer.policies')
                ->with('error', 'You do not have permission to download this policy document.');
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

    /**
     * Show all quotations for the customer.
     */
    public function showQuotations()
    {
        $customer = Auth::guard('customer')->user();

        $allQuotations = collect();
        if ($customer->hasFamily()) {
            try {
                if ($customer->isFamilyHead()) {
                    // Family head can view all quotations in their family group
                    $allQuotations = \App\Models\Quotation::with(['quotationCompanies.insuranceCompany'])
                        ->whereHas('customer', function ($query) use ($customer) {
                            $query->where('family_group_id', $customer->family_group_id);
                        })
                        ->orderBy('created_at', 'desc')
                        ->get();
                } else {
                    // Regular family member can only view their own quotations
                    $allQuotations = $customer->quotations()
                        ->with(['quotationCompanies.insuranceCompany'])
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
            } catch (\InvalidArgumentException $e) {
                // Log SQL injection attempt
                CustomerAuditLog::create([
                    'customer_id' => $customer->id,
                    'action' => 'sql_injection_attempt',
                    'description' => 'Invalid family group ID detected in quotation query',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'session_id' => session()->getId(),
                    'success' => false,
                    'failure_reason' => 'SQL injection prevention - Invalid family group ID',
                    'metadata' => [
                        'error_message' => $e->getMessage(),
                        'family_group_id' => $customer->family_group_id,
                        'security_violation' => 'sql_injection_attempt',
                        'location' => 'quotations'
                    ]
                ]);

                return redirect()->route('customer.dashboard')
                    ->with('error', 'Security error: Invalid family data detected.');
            }
        } else {
            // Customer without family can only see their own quotations
            $allQuotations = $customer->quotations()
                ->with(['quotationCompanies.insuranceCompany'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Log quotation list access
        CustomerAuditLog::logAction('view_quotations', 'Customer viewed quotation list', [
            'quotation_count' => $allQuotations->count(),
            'is_family_head' => $customer->isFamilyHead()
        ]);

        return view('customer.quotations', [
            'customer' => $customer,
            'quotations' => $allQuotations,
            'isHead' => $customer->isFamilyHead(),
        ]);
    }

    /**
     * Show detailed view of a specific quotation.
     */
    public function showQuotationDetail($quotationId)
    {
        $customer = Auth::guard('customer')->user();

        // Get the quotation and verify access
        $quotation = \App\Models\Quotation::with(['customer', 'quotationCompanies.insuranceCompany'])
            ->findOrFail($quotationId);

        // Check authorization - customer can only view quotations from their family group
        $hasAccess = false;

        if ($customer->isFamilyHead()) {
            // Family head can view all quotations in their family group
            $hasAccess = $quotation->customer->family_group_id === $customer->family_group_id;
        } else {
            // Regular family member can only view their own quotations
            $hasAccess = $quotation->customer_id === $customer->id;
        }

        if (!$hasAccess) {
            CustomerAuditLog::logFailure('view_quotation_detail', 'Unauthorized access attempt to quotation outside family group', [
                'quotation_id' => $quotationId,
                'quotation_customer_id' => $quotation->customer_id,
                'quotation_family_group_id' => $quotation->customer->family_group_id,
                'accessing_customer_id' => $customer->id,
                'accessing_family_group_id' => $customer->family_group_id,
                'is_family_head' => $customer->isFamilyHead(),
                'security_violation' => 'unauthorized_quotation_access'
            ]);

            return redirect()->route('customer.quotations')
                ->with('error', 'You do not have permission to view this quotation.');
        }

        // Log quotation access
        CustomerAuditLog::logAction('view_quotation_detail', 'Customer viewed quotation detail', [
            'quotation_id' => $quotation->id,
            'quotation_reference' => $quotation->getQuoteReference(),
            'vehicle_number' => $quotation->vehicle_number,
            'policy_holder' => $quotation->customer->name,
            'is_own_quotation' => $quotation->customer_id === $customer->id
        ]);

        return view('customer.quotation-detail', [
            'customer' => $customer,
            'quotation' => $quotation,
            'isHead' => $customer->isFamilyHead(),
        ]);
    }

    /**
     * Download quotation PDF.
     */
    public function downloadQuotation($quotationId)
    {
        $customer = Auth::guard('customer')->user();

        // Get the quotation and verify access
        $quotation = \App\Models\Quotation::with(['customer', 'quotationCompanies.insuranceCompany'])
            ->findOrFail($quotationId);

        // Check authorization - customer can only download quotations from their family group
        $hasAccess = false;

        if ($customer->isFamilyHead()) {
            // Family head can download all quotations in their family group
            $hasAccess = $quotation->customer->family_group_id === $customer->family_group_id;
        } else {
            // Regular family member can only download their own quotations
            $hasAccess = $quotation->customer_id === $customer->id;
        }

        if (!$hasAccess) {
            CustomerAuditLog::logFailure('download_quotation', 'Unauthorized download attempt to quotation outside family group', [
                'quotation_id' => $quotationId,
                'quotation_reference' => $quotation->getQuoteReference(),
                'quotation_customer_id' => $quotation->customer_id,
                'quotation_family_group_id' => $quotation->customer->family_group_id,
                'accessing_customer_id' => $customer->id,
                'accessing_family_group_id' => $customer->family_group_id,
                'is_family_head' => $customer->isFamilyHead(),
                'security_violation' => 'unauthorized_quotation_download'
            ]);

            return redirect()->route('customer.quotations')
                ->with('error', 'You do not have permission to download this quotation.');
        }

        // Check if quotation has company quotes
        if ($quotation->quotationCompanies->count() === 0) {
            return redirect()->back()->with('error', 'No company quotes available for download.');
        }

        try {
            // Log successful download request
            CustomerAuditLog::logAction('download_quotation', 'Customer requested quotation PDF download', [
                'quotation_id' => $quotation->id,
                'quotation_reference' => $quotation->getQuoteReference(),
                'vehicle_number' => $quotation->vehicle_number,
                'policy_holder' => $quotation->customer->name,
                'company_quotes_count' => $quotation->quotationCompanies->count(),
                'is_own_quotation' => $quotation->customer_id === $customer->id
            ]);

            // Use the same PDF service as admin
            $pdfService = app(\App\Services\PdfGenerationService::class);
            return $pdfService->generateQuotationPdf($quotation);

        } catch (\Throwable $e) {
            CustomerAuditLog::logFailure('download_quotation', 'Failed to generate quotation PDF', [
                'quotation_id' => $quotationId,
                'quotation_reference' => $quotation->getQuoteReference(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);

            return redirect()->back()->with('error', 'Failed to generate PDF. Please try again later.');
        }
    }
}