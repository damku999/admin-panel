<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrokerController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FuelTypeController;
use App\Http\Controllers\PolicyTypeController;
use App\Http\Controllers\AddonCoverController;
use App\Http\Controllers\PremiumTypeController;
use App\Http\Controllers\ReferenceUsersController;
use App\Http\Controllers\InsuranceCompanyController;
use App\Http\Controllers\CustomerInsuranceController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RelationshipManagerController;
use App\Http\Controllers\ClaimController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Customer Portal Routes are now defined in routes/customer.php

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Health check and monitoring routes
Route::get('/health', [App\Http\Controllers\HealthController::class, 'health'])->name('health.basic');
Route::get('/health/detailed', [App\Http\Controllers\HealthController::class, 'detailed'])->name('health.detailed');
Route::get('/health/liveness', [App\Http\Controllers\HealthController::class, 'liveness'])->name('health.liveness');
Route::get('/health/readiness', [App\Http\Controllers\HealthController::class, 'readiness'])->name('health.readiness');


// Admin-only monitoring routes
Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('/monitoring/metrics', [App\Http\Controllers\HealthController::class, 'metrics'])->name('monitoring.metrics');
    Route::get('/monitoring/performance', [App\Http\Controllers\HealthController::class, 'performance'])->name('monitoring.performance');
    Route::get('/monitoring/resources', [App\Http\Controllers\HealthController::class, 'resources'])->name('monitoring.resources');
    Route::get('/monitoring/logs', [App\Http\Controllers\HealthController::class, 'logs'])->name('monitoring.logs');
});

// Profile Routes
Route::prefix('profile')->name('profile.')->middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'getProfile'])->name('detail');
    Route::post('/update', [HomeController::class, 'updateProfile'])->name('update');
    Route::post('/change-password', [HomeController::class, 'changePassword'])->name('change-password');
});

// Roles
Route::resource('roles', App\Http\Controllers\RolesController::class);

// Permissions
Route::resource('permissions', App\Http\Controllers\PermissionsController::class);


// Customer
Route::middleware('auth')->prefix('customers')->name('customers.')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::get('/create', [CustomerController::class, 'create'])->name('create');
    Route::post('/store', [CustomerController::class, 'store'])->name('store');
    Route::get('/show/{customer}', [CustomerController::class, 'show'])->name('show');
    Route::get('/edit/{customer}', [CustomerController::class, 'edit'])->name('edit');
    Route::put('/update/{customer}', [CustomerController::class, 'update'])->name('update');
    Route::get('/resendOnBoardingWA/{customer}', [CustomerController::class, 'resendOnBoardingWA'])->name('resendOnBoardingWA');
    // Route::delete('/delete/{customer}', [CustomerController::class, 'delete'])->name('destroy');
    Route::get('/update/status/{customer_id}/{status}', [CustomerController::class, 'updateStatus'])->name('status');
    Route::get('export/', [CustomerController::class, 'export'])->name('export');
});

// Broker
Route::middleware('auth')->prefix('brokers')->name('brokers.')->group(function () {
    Route::get('/', [BrokerController::class, 'index'])->name('index');
    Route::get('/create', [BrokerController::class, 'create'])->name('create');
    Route::post('/store', [BrokerController::class, 'store'])->name('store');
    Route::get('/edit/{broker}', [BrokerController::class, 'edit'])->name('edit');
    Route::put('/update/{broker}', [BrokerController::class, 'update'])->name('update');
    // Route::delete('/delete/{broker}', [BrokerController::class, 'delete'])->name('destroy');
    Route::get('/update/status/{broker_id}/{status}', [BrokerController::class, 'updateStatus'])->name('status');
    Route::get('export/', [BrokerController::class, 'export'])->name('export');
});

// Broker
Route::middleware('auth')->prefix('reference_users')->name('reference_users.')->group(function () {
    Route::get('/', [ReferenceUsersController::class, 'index'])->name('index');
    Route::get('/create', [ReferenceUsersController::class, 'create'])->name('create');
    Route::post('/store', [ReferenceUsersController::class, 'store'])->name('store');
    Route::get('/edit/{reference_user}', [ReferenceUsersController::class, 'edit'])->name('edit');
    Route::put('/update/{reference_user}', [ReferenceUsersController::class, 'update'])->name('update');
    // Route::delete('/delete/{reference_user}', [ReferenceUsersController::class, 'delete'])->name('destroy');
    Route::get('/update/status/{reference_user_id}/{status}', [ReferenceUsersController::class, 'updateStatus'])->name('status');
    Route::get('export/', [ReferenceUsersController::class, 'export'])->name('export');
});

// Relationship Manager
Route::middleware('auth')->prefix('relationship_managers')->name('relationship_managers.')->group(function () {
    Route::get('/', [RelationshipManagerController::class, 'index'])->name('index');
    Route::get('/create', [RelationshipManagerController::class, 'create'])->name('create');
    Route::post('/store', [RelationshipManagerController::class, 'store'])->name('store');
    Route::get('/edit/{relationship_manager}', [RelationshipManagerController::class, 'edit'])->name('edit');
    Route::put('/update/{relationship_manager}', [RelationshipManagerController::class, 'update'])->name('update');
    // Route::delete('/delete/{relationship_manager}', [RelationshipManagerController::class, 'delete'])->name('destroy');
    Route::get('/update/status/{relationship_manager_id}/{status}', [RelationshipManagerController::class, 'updateStatus'])->name('status');
    Route::get('export/', [RelationshipManagerController::class, 'export'])->name('export');
});

// Insurance Company
Route::middleware('auth')->prefix('insurance_companies')->name('insurance_companies.')->group(function () {
    Route::get('/', [InsuranceCompanyController::class, 'index'])->name('index');
    Route::get('/create', [InsuranceCompanyController::class, 'create'])->name('create');
    Route::post('/store', [InsuranceCompanyController::class, 'store'])->name('store');
    Route::get('/edit/{insurance_company}', [InsuranceCompanyController::class, 'edit'])->name('edit');
    Route::put('/update/{insurance_company}', [InsuranceCompanyController::class, 'update'])->name('update');
    // Route::delete('/delete/{insurance_company}', [InsuranceCompanyController::class, 'delete'])->name('destroy');
    Route::get('/update/status/{insurance_company_id}/{status}', [InsuranceCompanyController::class, 'updateStatus'])->name('status');
    Route::get('export/', [InsuranceCompanyController::class, 'export'])->name('export');
});

// Customer Insurances
Route::middleware('auth')->prefix('customer_insurances')->name('customer_insurances.')->group(function () {
    Route::get('/', [CustomerInsuranceController::class, 'index'])->name('index');
    Route::get('/create', [CustomerInsuranceController::class, 'create'])->name('create');
    Route::post('/store', [CustomerInsuranceController::class, 'store'])->name('store');
    Route::get('/edit/{customer_insurance}', [CustomerInsuranceController::class, 'edit'])->name('edit');
    Route::get('/sendWADocument/{customer_insurance}', [CustomerInsuranceController::class, 'sendWADocument'])->name('sendWADocument');
    Route::get('/sendRenewalReminderWA/{customer_insurance}', [CustomerInsuranceController::class, 'sendRenewalReminderWA'])->name('sendRenewalReminderWA');
    Route::put('/update/{customer_insurance}', [CustomerInsuranceController::class, 'update'])->name('update');
    // Route::delete('/delete/{customer_insurance}', [CustomerInsuranceController::class, 'delete'])->name('destroy');
    Route::get('/update/status/{customer_insurance_id}/{status}', [CustomerInsuranceController::class, 'updateStatus'])->name('status');
    Route::get('export/', [CustomerInsuranceController::class, 'export'])->name('export');
    Route::get('/renew/{customer_insurance}', [CustomerInsuranceController::class, 'renew'])->name('renew');
    Route::put('/storeRenew/{customer_insurance}', [CustomerInsuranceController::class, 'storeRenew'])->name('storeRenew');
});

// Users
Route::middleware('auth')->prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/store', [UserController::class, 'store'])->name('store');
    Route::get('/edit/{user}', [UserController::class, 'edit'])->name('edit');
    Route::put('/update/{user}', [UserController::class, 'update'])->name('update');
    // Route::delete('/delete/{user}', [UserController::class, 'delete'])->name('destroy');
    Route::get('/update/status/{user_id}/{status}', [UserController::class, 'updateStatus'])->name('status');



    Route::get('export/', [UserController::class, 'export'])->name('export');
});

// Family Groups
Route::middleware('auth')->prefix('family_groups')->name('family_groups.')->group(function () {
    Route::get('/', [App\Http\Controllers\FamilyGroupController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\FamilyGroupController::class, 'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\FamilyGroupController::class, 'store'])->name('store');
    Route::get('/show/{familyGroup}', [App\Http\Controllers\FamilyGroupController::class, 'show'])->name('show');
    Route::get('/edit/{familyGroup}', [App\Http\Controllers\FamilyGroupController::class, 'edit'])->name('edit');
    Route::put('/update/{familyGroup}', [App\Http\Controllers\FamilyGroupController::class, 'update'])->name('update');
    Route::delete('/delete/{familyGroup}', [App\Http\Controllers\FamilyGroupController::class, 'destroy'])->name('destroy');
    Route::get('/update/status/{family_group_id}/{status}', [App\Http\Controllers\FamilyGroupController::class, 'updateStatus'])->name('status');
    Route::delete('/member/{familyMember}', [App\Http\Controllers\FamilyGroupController::class, 'removeMember'])->name('member.remove');
    Route::get('export/', [App\Http\Controllers\FamilyGroupController::class, 'export'])->name('export');
});

Route::middleware('auth')->group(function () {
    Route::post('delete_common', [CommonController::class, 'deleteCommon'])->name('delete_common');
});

// Policy Type
Route::middleware('auth')->prefix('policy_type')->name('policy_type.')->group(function () {
    Route::get('/', [PolicyTypeController::class, 'index'])->name('index');
    Route::get('/create', [PolicyTypeController::class, 'create'])->name('create');
    Route::post('/store', [PolicyTypeController::class, 'store'])->name('store');
    Route::get('/edit/{policy_type}', [PolicyTypeController::class, 'edit'])->name('edit');
    Route::put('/update/{policy_type}', [PolicyTypeController::class, 'update'])->name('update');
    Route::get('/update/status/{policy_type_id}/{status}', [PolicyTypeController::class, 'updateStatus'])->name('status');
    Route::get('export/', [PolicyTypeController::class, 'export'])->name('export');
});

// Add-on Covers
Route::middleware('auth')->prefix('addon-covers')->name('addon-covers.')->group(function () {
    Route::get('/', [AddonCoverController::class, 'index'])->name('index');
    Route::get('/create', [AddonCoverController::class, 'create'])->name('create');
    Route::post('/store', [AddonCoverController::class, 'store'])->name('store');
    Route::get('/edit/{addon_cover}', [AddonCoverController::class, 'edit'])->name('edit');
    Route::put('/update/{addon_cover}', [AddonCoverController::class, 'update'])->name('update');
    Route::get('/update/status/{addon_cover_id}/{status}', [AddonCoverController::class, 'updateStatus'])->name('status');
    Route::delete('/delete/{addon_cover}', [AddonCoverController::class, 'delete'])->name('delete');
    Route::get('export/', [AddonCoverController::class, 'export'])->name('export');
});

// Premium Type
Route::middleware('auth')->prefix('premium_type')->name('premium_type.')->group(function () {
    Route::get('/', [PremiumTypeController::class, 'index'])->name('index');
    Route::get('/create', [PremiumTypeController::class, 'create'])->name('create');
    Route::post('/store', [PremiumTypeController::class, 'store'])->name('store');
    Route::get('/edit/{premium_type}', [PremiumTypeController::class, 'edit'])->name('edit');
    Route::put('/update/{premium_type}', [PremiumTypeController::class, 'update'])->name('update');
    Route::get('/update/status/{premium_type_id}/{status}', [PremiumTypeController::class, 'updateStatus'])->name('status');
    Route::get('export/', [PremiumTypeController::class, 'export'])->name('export');
});


// Fuel Type
Route::middleware('auth')->prefix('fuel_type')->name('fuel_type.')->group(function () {
    Route::get('/', [FuelTypeController::class, 'index'])->name('index');
    Route::get('/create', [FuelTypeController::class, 'create'])->name('create');
    Route::post('/store', [FuelTypeController::class, 'store'])->name('store');
    Route::get('/edit/{fuel_type}', [FuelTypeController::class, 'edit'])->name('edit');
    Route::put('/update/{fuel_type}', [FuelTypeController::class, 'update'])->name('update');
    Route::get('/update/status/{fuel_type_id}/{status}', [FuelTypeController::class, 'updateStatus'])->name('status');
    Route::get('export/', [FuelTypeController::class, 'export'])->name('export');
});


// Quotations
Route::middleware('auth')->prefix('quotations')->name('quotations.')->group(function () {
    Route::get('/', [QuotationController::class, 'index'])->name('index');
    Route::get('/create', [QuotationController::class, 'create'])->name('create');
    Route::post('/store', [QuotationController::class, 'store'])->name('store');
    Route::get('/show/{quotation}', [QuotationController::class, 'show'])->name('show');
    Route::get('/edit/{quotation}', [QuotationController::class, 'edit'])->name('edit');
    Route::put('/update/{quotation}', [QuotationController::class, 'update'])->name('update');
    Route::post('/generate-quotes/{quotation}', [QuotationController::class, 'generateQuotes'])->name('generate-quotes');
    Route::post('/send-whatsapp/{quotation}', [QuotationController::class, 'sendToWhatsApp'])->name('send-whatsapp');
    Route::get('/download-pdf/{quotation}', [QuotationController::class, 'downloadPdf'])->name('download-pdf');
    Route::get('/get-quote-form', [QuotationController::class, 'getQuoteFormHtml'])->name('get-quote-form');
    Route::delete('/delete/{quotation}', [QuotationController::class, 'delete'])->name('delete');
});

// Reports
Route::middleware('auth')->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::post('/', [ReportController::class, 'index'])->name('index.post');
    Route::get('export/', [ReportController::class, 'export'])->name('export');
    Route::post('selected/columns', [ReportController::class, 'saveColumns'])->name('save.selected.columns');
    Route::get('load/columns/{report_name}', [ReportController::class, 'loadColumns'])->name('load.selected.columns');
});

// Marketing WhatsApp
Route::middleware('auth')->prefix('marketing/whatsapp')->name('marketing.whatsapp.')->group(function () {
    Route::get('/', [App\Http\Controllers\MarketingWhatsAppController::class, 'index'])->name('index');
    Route::post('/send', [App\Http\Controllers\MarketingWhatsAppController::class, 'send'])->name('send');
    Route::post('/preview', [App\Http\Controllers\MarketingWhatsAppController::class, 'preview'])->name('preview');
});

// Branches
Route::middleware('auth')->prefix('branches')->name('branches.')->group(function () {
    Route::get('/', [App\Http\Controllers\BranchController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\BranchController::class, 'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\BranchController::class, 'store'])->name('store');
    Route::get('/edit/{branch}', [App\Http\Controllers\BranchController::class, 'edit'])->name('edit');
    Route::put('/update/{branch}', [App\Http\Controllers\BranchController::class, 'update'])->name('update');
    Route::get('/update/status/{branch_id}/{status}', [App\Http\Controllers\BranchController::class, 'updateStatus'])->name('status');
    Route::get('export/', [App\Http\Controllers\BranchController::class, 'export'])->name('export');
});

// Claims Management
Route::middleware('auth:web')->prefix('insurance-claims')->name('claims.')->group(function () {
    Route::get('/', [ClaimController::class, 'index'])->name('index');
    Route::get('/create', [ClaimController::class, 'create'])->name('create');
    Route::post('/store', [ClaimController::class, 'store'])->name('store');
    Route::get('/show/{claim}', [ClaimController::class, 'show'])->name('show');
    Route::get('/edit/{claim}', [ClaimController::class, 'edit'])->name('edit');
    Route::put('/update/{claim}', [ClaimController::class, 'update'])->name('update');
    Route::delete('/delete/{claim}', [ClaimController::class, 'delete'])->name('delete');
    Route::get('/update/status/{claim_id}/{status}', [ClaimController::class, 'updateStatus'])->name('status');
    Route::get('export/', [ClaimController::class, 'export'])->name('export');

    // AJAX endpoints
    Route::get('/search-policies', [ClaimController::class, 'searchPolicies'])->name('searchPolicies');
    Route::get('/statistics', [ClaimController::class, 'getStatistics'])->name('statistics');

    // WhatsApp functionality
    Route::post('/whatsapp/document-list/{claim}', [ClaimController::class, 'sendDocumentListWhatsApp'])->name('whatsapp.documentList');
    Route::post('/whatsapp/pending-documents/{claim}', [ClaimController::class, 'sendPendingDocumentsWhatsApp'])->name('whatsapp.pendingDocuments');
    Route::post('/whatsapp/claim-number/{claim}', [ClaimController::class, 'sendClaimNumberWhatsApp'])->name('whatsapp.claimNumber');
    Route::get('/whatsapp/preview/{claim}/{type}', [ClaimController::class, 'getWhatsAppPreview'])->name('whatsapp.preview');

    // Document management
    Route::post('/documents/{claim}/{document}/update-status', [ClaimController::class, 'updateDocumentStatus'])->name('documents.updateStatus');

    // Stage management
    Route::post('/stages/{claim}/add', [ClaimController::class, 'addStage'])->name('stages.add');

    // Claim number management
    Route::post('/claim-number/{claim}/update', [ClaimController::class, 'updateClaimNumber'])->name('claimNumber.update');

    // Liability details management
    Route::post('/liability/{claim}/update', [ClaimController::class, 'updateLiabilityDetails'])->name('liability.update');
});

// Two-Factor Authentication Routes
Route::middleware('auth')->prefix('profile/two-factor')->name('profile.two-factor.')->group(function () {
    Route::get('/', [App\Http\Controllers\TwoFactorAuthController::class, 'index'])->name('index');
    Route::post('/enable', [App\Http\Controllers\TwoFactorAuthController::class, 'enable'])->name('enable');
    Route::post('/confirm', [App\Http\Controllers\TwoFactorAuthController::class, 'confirm'])->name('confirm');
    Route::post('/disable', [App\Http\Controllers\TwoFactorAuthController::class, 'disable'])->name('disable');
    Route::post('/recovery-codes', [App\Http\Controllers\TwoFactorAuthController::class, 'generateRecoveryCodes'])->name('recovery-codes');
    Route::post('/trust-device', [App\Http\Controllers\TwoFactorAuthController::class, 'trustDevice'])->name('trust-device');
    Route::delete('/devices/{device}', [App\Http\Controllers\TwoFactorAuthController::class, 'revokeDevice'])->name('revoke-device');
    Route::get('/status', [App\Http\Controllers\TwoFactorAuthController::class, 'status'])->name('status');
});

// 2FA Challenge Routes (during login)
Route::get('/two-factor-challenge', [App\Http\Controllers\TwoFactorAuthController::class, 'showVerification'])->name('two-factor.challenge');
Route::post('/two-factor-challenge', [App\Http\Controllers\TwoFactorAuthController::class, 'verify'])->name('two-factor.verify');

// Security Monitoring Routes
Route::middleware('auth')->prefix('security')->name('security.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\SecurityController::class, 'dashboard'])->name('dashboard');
    Route::get('/audit-logs', [App\Http\Controllers\SecurityController::class, 'auditLogs'])->name('audit-logs');
    Route::get('/export-logs', [App\Http\Controllers\SecurityController::class, 'exportLogs'])->name('export-logs');

    // API endpoints for security analytics
    Route::get('/api/analytics', [App\Http\Controllers\SecurityController::class, 'analytics'])->name('api.analytics');
    Route::get('/api/suspicious-activity', [App\Http\Controllers\SecurityController::class, 'suspiciousActivity'])->name('api.suspicious-activity');
    Route::get('/api/high-risk-activity', [App\Http\Controllers\SecurityController::class, 'highRiskActivity'])->name('api.high-risk-activity');
    Route::get('/api/user/{userId}/activity', [App\Http\Controllers\SecurityController::class, 'userActivity'])->name('api.user-activity');
    Route::get('/api/entity/{entityId}/activity', [App\Http\Controllers\SecurityController::class, 'entityActivity'])->name('api.entity-activity');
    Route::get('/api/report', [App\Http\Controllers\SecurityController::class, 'generateReport'])->name('api.report');
    Route::get('/api/alerts', [App\Http\Controllers\SecurityController::class, 'alerts'])->name('api.alerts');
    Route::get('/api/metrics-widget', [App\Http\Controllers\SecurityController::class, 'metricsWidget'])->name('api.metrics-widget');
});

// API routes removed - web application only

// Additional routes can be added here
