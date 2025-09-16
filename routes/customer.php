<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomerAuthController;

/*
|--------------------------------------------------------------------------
| Customer Portal Routes
|--------------------------------------------------------------------------
|
| This file contains all routes for the customer portal functionality
| including authentication, dashboard, policies, quotations, and profile
| management for insurance customers and their families.
|
*/

// Customer Authentication Routes (defined with priority)
Route::prefix('customer')->name('customer.')->group(function () {
    
    // ==========================================
    // PUBLIC ROUTES (Unauthenticated Access)
    // ==========================================
    
    // Login Routes with Rate Limiting
    Route::middleware(['throttle:10,1'])->group(function () {
        Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [CustomerAuthController::class, 'login']);
    });

    // Password Reset Routes with Enhanced Rate Limiting
    Route::middleware(['throttle:5,1'])->group(function () {
        Route::get('/password/reset', [CustomerAuthController::class, 'showPasswordResetForm'])->name('password.request');
        Route::post('/password/email', [CustomerAuthController::class, 'sendPasswordResetLink'])->name('password.email');
        Route::get('/password/reset/{token}', [CustomerAuthController::class, 'showPasswordResetFormWithToken'])->name('password.reset');
        Route::post('/password/reset', [CustomerAuthController::class, 'resetPassword'])->name('password.update');
    });

    // Email Verification Routes with Strict Rate Limiting
    Route::middleware(['throttle:3,1'])->group(function () {
        Route::get('/email/verify/{token}', [CustomerAuthController::class, 'verifyEmail'])->name('verify-email');
    });

    // ==========================================
    // AUTHENTICATED ROUTES (Customer Login Required)
    // ==========================================
    
    // Logout Route (Authenticated Customers Only)
    Route::post('/logout', [CustomerAuthController::class, 'logout'])
        ->middleware(['customer.auth'])
        ->name('logout');

    // Core Dashboard Routes (Protected with Session Timeout)
    Route::middleware(['customer.auth', 'customer.timeout', 'throttle:60,1'])->group(function () {
        
        // Main Dashboard
        Route::get('/dashboard', [CustomerAuthController::class, 'dashboard'])->name('dashboard');

        // Profile Management
        Route::get('/profile', [CustomerAuthController::class, 'showProfile'])->name('profile');

        // Password Change Functionality
        Route::get('/change-password', [CustomerAuthController::class, 'showChangePasswordForm'])->name('change-password-form');
        Route::post('/change-password', [CustomerAuthController::class, 'changePassword'])
            ->middleware(['throttle:10,1'])
            ->name('change-password');

        // Email Verification Management
        Route::get('/email/verify-notice', [CustomerAuthController::class, 'showEmailVerificationNotice'])->name('verify-email-notice');
        Route::post('/email/resend', [CustomerAuthController::class, 'resendVerification'])
            ->middleware(['throttle:3,1'])
            ->name('resend-verification');
            
        // ==========================================
        // FAMILY MEMBER MANAGEMENT (Family Heads Only)
        // ==========================================
        
        // Family Member Profile Access
        Route::get('/family-member/{member}/profile', [CustomerAuthController::class, 'showFamilyMemberProfile'])
            ->name('family-member.profile');
            
        // Family Member Password Management
        Route::get('/family-member/{member}/change-password', [CustomerAuthController::class, 'showFamilyMemberPasswordForm'])
            ->name('family-member.password-form');
        Route::put('/family-member/{member}/password', [CustomerAuthController::class, 'updateFamilyMemberPassword'])
            ->middleware(['throttle:10,1'])
            ->name('family-member.password');
    });

    // ==========================================
    // FAMILY GROUP ROUTES (Family Membership Required)
    // ==========================================
    
    // Family-Specific Routes (Require Family Group Membership)
    Route::middleware(['customer.auth', 'customer.timeout', 'customer.family', 'throttle:60,1'])->group(function () {
        
        // ==========================================
        // INSURANCE POLICIES MANAGEMENT
        // ==========================================
        
        // Policy Listing and Details
        Route::get('/policies', [CustomerAuthController::class, 'showPolicies'])->name('policies');
        Route::get('/policies/{policy}', [CustomerAuthController::class, 'showPolicyDetail'])->name('policies.detail');
        
        // Policy Document Downloads (Rate Limited)
        Route::get('/policies/{policy}/download', [CustomerAuthController::class, 'downloadPolicy'])
            ->middleware(['throttle:10,1'])
            ->name('policies.download');
        
        // ==========================================
        // QUOTATIONS MANAGEMENT
        // ==========================================
        
        // Quotation Listing and Details
        Route::get('/quotations', [CustomerAuthController::class, 'showQuotations'])->name('quotations');
        Route::get('/quotations/{quotation}', [CustomerAuthController::class, 'showQuotationDetail'])->name('quotations.detail');
        
        // Quotation Document Downloads (Rate Limited)
        Route::get('/quotations/{quotation}/download', [CustomerAuthController::class, 'downloadQuotation'])
            ->middleware(['throttle:10,1'])
            ->name('quotations.download');

        // ==========================================
        // CLAIMS MANAGEMENT (Read-Only)
        // ==========================================

        // Claims Listing and Details
        Route::get('/view-claims', [CustomerAuthController::class, 'showClaims'])->name('claims');
        Route::get('/view-claims/{claim}', [CustomerAuthController::class, 'showClaimDetail'])->name('claims.detail');
    });
});

/*
|--------------------------------------------------------------------------
| Route Security Notes
|--------------------------------------------------------------------------
|
| 1. Rate Limiting:
|    - Login attempts: 10 per minute
|    - Password reset: 5 per minute  
|    - Email verification: 3 per minute
|    - General routes: 60 per minute
|    - Downloads: 10 per minute
|
| 2. Middleware Stack:
|    - customer.auth: Validates customer authentication
|    - customer.timeout: Enforces session timeout
|    - customer.family: Ensures family group membership
|    - throttle: Rate limiting protection
|
| 3. Security Features:
|    - Session timeout enforcement
|    - Rate limiting on sensitive operations
|    - Family group access control
|    - Download throttling
|
*/