<?php

namespace App\Http\Controllers;

use App\Services\TwoFactorAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class TwoFactorAuthController extends Controller
{
    public function __construct(
        private TwoFactorAuthService $twoFactorService
    ) {
        // Support both web and customer guards
        $this->middleware(['auth:web,customer'])->except(['showVerification', 'verify']);
    }

    /**
     * Get the authenticated user from the appropriate guard
     */
    public function getAuthenticatedUser()
    {
        // Check if customer guard is authenticated (customer portal)
        if (Auth::guard('customer')->check()) {
            return Auth::guard('customer')->user();
        }

        // Default to web guard (admin portal)
        return Auth::guard('web')->user();
    }

    /**
     * Get the appropriate guard name based on current authentication
     */
    public function getGuardName(): string
    {
        return Auth::guard('customer')->check() ? 'customer' : 'web';
    }

    /**
     * Show 2FA settings page
     */
    public function index(): View
    {
        $user = $this->getAuthenticatedUser();
        $status = $this->twoFactorService->getTwoFactorStatus($user);
        $trustedDevices = $this->twoFactorService->getTrustedDevices($user);

        // Use different views based on guard to ensure zero conflicts
        $guardName = $this->getGuardName();
        $viewData = compact('status', 'trustedDevices');

        if ($guardName === 'customer') {
            // Customer portal uses separate view with customer layout
            return view('customer.two-factor', $viewData);
        } else {
            // Admin portal uses original view with admin layout
            return view('profile.two-factor', $viewData);
        }
    }

    /**
     * Enable 2FA - Start setup process
     */
    public function enable(Request $request): JsonResponse
    {
        try {
            $user = $this->getAuthenticatedUser();
            $guardName = $this->getGuardName();

            \Log::info('🔧 [2FA Enable Controller] Starting 2FA enable process', [
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'guard' => $guardName,
                'request_ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            \Log::info('🔧 [2FA Enable Controller] User loaded', [
                'user_id' => $user->id,
                'user_email' => $user->email ?? 'N/A',
                'has_2fa_enabled' => method_exists($user, 'hasTwoFactorEnabled') ? $user->hasTwoFactorEnabled() : 'method_not_exists'
            ]);

            $result = $this->twoFactorService->enableTwoFactor($user);

            \Log::info('✅ [2FA Enable Controller] Service call successful', [
                'user_id' => $user->id,
                'qr_code_present' => isset($result['qr_code_svg']) && !empty($result['qr_code_svg']),
                'recovery_codes_count' => isset($result['recovery_codes']) ? count($result['recovery_codes']) : 0,
                'setup_url_present' => isset($result['qr_code_url']) && !empty($result['qr_code_url'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Two-factor authentication setup started. Please scan the QR code with your authenticator app.',
                'data' => [
                    'qr_code_svg' => $result['qr_code_svg'],
                    'recovery_codes' => $result['recovery_codes'],
                    'setup_url' => $result['qr_code_url'],
                ]
            ]);
        } catch (\Exception $e) {
            $user = $this->getAuthenticatedUser();
            \Log::error('🚨 [2FA Enable Controller] Exception occurred', [
                'user_id' => $user ? $user->id : null,
                'user_type' => $user ? get_class($user) : null,
                'guard' => $this->getGuardName(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Confirm 2FA setup with verification code
     */
    public function confirm(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6|regex:/^[0-9]{6}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid 6-digit verification code.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $this->getAuthenticatedUser();
            $this->twoFactorService->confirmTwoFactor($user, $request->code, $request);

            // Update security settings to reflect 2FA is enabled
            $user->enableTwoFactorInSettings();

            return response()->json([
                'success' => true,
                'message' => 'Two-factor authentication has been successfully enabled for your account.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'confirmation' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide your current password and confirm the action.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $this->getAuthenticatedUser();
            $this->twoFactorService->disableTwoFactor($user, $request->current_password);

            // Update security settings to reflect 2FA is disabled
            $user->disableTwoFactorInSettings();

            return response()->json([
                'success' => true,
                'message' => 'Two-factor authentication has been disabled for your account.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Generate new recovery codes
     */
    public function generateRecoveryCodes(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide your current password.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $this->getAuthenticatedUser();

            // Verify current password
            if (!$user->checkPassword($request->current_password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect.'
                ], 400);
            }

            $codes = $this->twoFactorService->generateNewRecoveryCodes($user);

            return response()->json([
                'success' => true,
                'message' => 'New recovery codes have been generated. Please store them safely.',
                'data' => [
                    'recovery_codes' => $codes
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Trust current device
     */
    public function trustDevice(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device_name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid device name.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $this->getAuthenticatedUser();
            $result = $this->twoFactorService->trustDevice(
                $user,
                $request,
                $request->device_name ?: 'My Device'
            );

            $device = $result['device'];
            $wasAlreadyTrusted = $result['was_already_trusted'];

            $message = $wasAlreadyTrusted
                ? 'This device is already trusted and has been updated.'
                : 'This device has been added to your trusted devices list.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'device' => [
                        'id' => $device->id,
                        'name' => $device->device_name,
                        'display_name' => $device->getDisplayName(),
                        'trusted_at' => $device->trusted_at->format('M j, Y g:i A'),
                    ],
                    'was_already_trusted' => $wasAlreadyTrusted
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Revoke device trust
     */
    public function revokeDevice(Request $request, int $deviceId): JsonResponse
    {
        try {
            $user = $this->getAuthenticatedUser();
            $success = $this->twoFactorService->revokeDeviceTrust($user, $deviceId);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found or already revoked.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Device trust has been revoked.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get 2FA status for AJAX requests
     */
    public function status(): JsonResponse
    {
        $user = $this->getAuthenticatedUser();
        $status = $this->twoFactorService->getTwoFactorStatus($user);
        $trustedDevices = $this->twoFactorService->getTrustedDevices($user);

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $status,
                'trusted_devices' => $trustedDevices,
                'current_device_trusted' => $this->twoFactorService->isDeviceTrusted($user, request()),
            ]
        ]);
    }

    /**
     * Show verification form during login (for 2FA challenge)
     */
    public function showVerification(): View
    {
        \Log::info('🔐 [2FA Challenge] showVerification accessed', [
            'session_id' => session()->getId(),
            'session_2fa_user_id' => session('2fa_user_id'),
            'session_2fa_guard' => session('2fa_guard'),
            'session_2fa_remember' => session('2fa_remember'),
            'all_session_data' => session()->all()
        ]);

        // This should only be accessible during 2FA challenge
        if (!session('2fa_user_id')) {
            \Log::warning('🚨 [2FA Challenge] No 2FA user ID in session, redirecting to login', [
                'session_id' => session()->getId(),
                'all_session_keys' => array_keys(session()->all())
            ]);

            // Redirect to appropriate login based on guard
            $guard = session('2fa_guard', 'web');
            $loginRoute = $guard === 'customer' ? 'customer.login' : 'login';
            return redirect()->route($loginRoute);
        }

        \Log::info('✅ [2FA Challenge] Showing 2FA challenge form', [
            'user_id' => session('2fa_user_id')
        ]);

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify 2FA code during login
     */
    public function verify(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'code_type' => 'required|in:totp,recovery',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $userId = session('2fa_user_id');
            $guard = session('2fa_guard', 'web');

            if (!$userId) {
                $loginRoute = $guard === 'customer' ? 'customer.login' : 'login';
                return redirect()->route($loginRoute)->withErrors([
                    'code' => 'Session expired. Please login again.'
                ]);
            }

            // Get user from appropriate guard
            $user = $guard === 'customer'
                ? \App\Models\Customer::find($userId)
                : \App\Models\User::find($userId);

            if (!$user) {
                $loginRoute = $guard === 'customer' ? 'customer.login' : 'login';
                return redirect()->route($loginRoute)->withErrors([
                    'code' => 'User not found. Please login again.'
                ]);
            }

            // Verify 2FA code
            $this->twoFactorService->verifyTwoFactorLogin(
                $user,
                $request->code,
                $request->code_type,
                $request
            );

            // Clear 2FA session data
            session()->forget(['2fa_user_id', '2fa_guard', '2fa_remember']);

            // Complete login with the correct guard
            Auth::guard($guard)->login($user, session('2fa_remember', false));

            // Trust device if requested (admin only)
            if ($guard === 'web' && $request->has('trust_device') && $request->trust_device) {
                $this->twoFactorService->trustDevice($user, $request);
            }

            // Redirect to intended location
            $redirectTo = $guard === 'customer' ? route('customer.dashboard') : route('home');
            return redirect()->intended($redirectTo);

        } catch (\Exception $e) {
            return back()->withErrors([
                'code' => $e->getMessage()
            ])->withInput();
        }
    }
}