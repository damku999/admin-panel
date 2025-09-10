<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrokerController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerInsuranceController;
use App\Http\Controllers\Api\InsuranceCompanyController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Api\QuotationController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public API routes
Route::prefix('v1')->group(function () {
    // Authentication routes (stricter throttling)
    Route::middleware('throttle:auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
    });
    
    // Protected routes with Sanctum authentication
    Route::middleware(['auth:sanctum', 'api.rate.limit'])->group(function () {
        
        // Authentication management
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->middleware('throttle:write');
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->middleware('throttle:write');
        });
        
        // Customer management
        Route::prefix('customers')->group(function () {
            Route::get('/', [CustomerController::class, 'index']);
            Route::post('/', [CustomerController::class, 'store'])->middleware('throttle:write');
            Route::get('/{customer}', [CustomerController::class, 'show']);
            Route::put('/{customer}', [CustomerController::class, 'update'])->middleware('throttle:write');
            Route::delete('/{customer}', [CustomerController::class, 'destroy'])->middleware('throttle:write');
            Route::patch('/{customer}/status', [CustomerController::class, 'updateStatus'])->middleware('throttle:write');
        });
        
        // Quotation management
        Route::prefix('quotations')->group(function () {
            Route::get('/', [QuotationController::class, 'index']);
            Route::post('/', [QuotationController::class, 'store'])->middleware('throttle:write');
            Route::get('/{quotation}', [QuotationController::class, 'show']);
            Route::put('/{quotation}', [QuotationController::class, 'update'])->middleware('throttle:write');
            Route::delete('/{quotation}', [QuotationController::class, 'destroy'])->middleware('throttle:write');
            Route::patch('/{quotation}/status', [QuotationController::class, 'updateStatus'])->middleware('throttle:write');
            Route::post('/{quotation}/duplicate', [QuotationController::class, 'duplicate'])->middleware('throttle:write');
            Route::get('/{quotation}/comparison', [QuotationController::class, 'getComparison']);
        });
        
        // Customer Insurance management
        Route::prefix('customer-insurances')->group(function () {
            Route::get('/', [CustomerInsuranceController::class, 'index']);
            Route::post('/', [CustomerInsuranceController::class, 'store'])->middleware('throttle:write');
            Route::get('/expiring', [CustomerInsuranceController::class, 'getExpiringPolicies']);
            Route::get('/{customerInsurance}', [CustomerInsuranceController::class, 'show']);
            Route::put('/{customerInsurance}', [CustomerInsuranceController::class, 'update'])->middleware('throttle:write');
            Route::delete('/{customerInsurance}', [CustomerInsuranceController::class, 'destroy'])->middleware('throttle:write');
            Route::patch('/{customerInsurance}/status', [CustomerInsuranceController::class, 'updateStatus'])->middleware('throttle:write');
            Route::post('/{customerInsurance}/renew', [CustomerInsuranceController::class, 'renewPolicy'])->middleware('throttle:write');
        });
        
        // Insurance Company management
        Route::prefix('insurance-companies')->group(function () {
            Route::get('/', [InsuranceCompanyController::class, 'index']);
            Route::post('/', [InsuranceCompanyController::class, 'store'])->middleware('throttle:write');
            Route::get('/active', [InsuranceCompanyController::class, 'getActiveCompanies']);
            Route::get('/{insuranceCompany}', [InsuranceCompanyController::class, 'show']);
            Route::put('/{insuranceCompany}', [InsuranceCompanyController::class, 'update'])->middleware('throttle:write');
            Route::delete('/{insuranceCompany}', [InsuranceCompanyController::class, 'destroy'])->middleware('throttle:write');
            Route::patch('/{insuranceCompany}/status', [InsuranceCompanyController::class, 'updateStatus'])->middleware('throttle:write');
            Route::get('/{insuranceCompany}/statistics', [InsuranceCompanyController::class, 'getCompanyStatistics']);
        });
        
        // Broker management
        Route::prefix('brokers')->group(function () {
            Route::get('/', [BrokerController::class, 'index']);
            Route::post('/', [BrokerController::class, 'store'])->middleware('throttle:write');
            Route::get('/active', [BrokerController::class, 'getActiveBrokers']);
            Route::get('/{broker}', [BrokerController::class, 'show']);
            Route::put('/{broker}', [BrokerController::class, 'update'])->middleware('throttle:write');
            Route::delete('/{broker}', [BrokerController::class, 'destroy'])->middleware('throttle:write');
            Route::patch('/{broker}/status', [BrokerController::class, 'updateStatus'])->middleware('throttle:write');
            Route::get('/{broker}/commissions', [BrokerController::class, 'getBrokerCommissions']);
            Route::get('/{broker}/statistics', [BrokerController::class, 'getBrokerStatistics']);
        });
        
        // Reports (with stricter throttling)
        Route::middleware('throttle:report')->prefix('reports')->group(function () {
            Route::get('/dashboard', [ReportController::class, 'getDashboardStats']);
            Route::get('/customers', [ReportController::class, 'getCustomerStatistics']);
            Route::get('/policies', [ReportController::class, 'getPolicyStatistics']);
            Route::get('/commissions', [ReportController::class, 'getCommissionReport']);
            Route::get('/cross-selling', [ReportController::class, 'getCrossSellingReport']);
            Route::get('/expiring-policies', [ReportController::class, 'getExpiringPoliciesReport']);
            Route::get('/business-trends', [ReportController::class, 'getBusinessTrendsReport']);
            Route::get('/top-performers', [ReportController::class, 'getTopPerformersReport']);
            Route::post('/custom', [ReportController::class, 'getCustomReport']);
        });
        
        // Lookup data (cached responses)
        Route::prefix('lookups')->group(function () {
            Route::get('/policy-types', [LookupController::class, 'getPolicyTypes']);
            Route::get('/fuel-types', [LookupController::class, 'getFuelTypes']);
            Route::get('/branches', [LookupController::class, 'getBranches']);
            Route::get('/premium-types', [LookupController::class, 'getPremiumTypes']);
            Route::get('/addon-covers', [LookupController::class, 'getAddonCovers']);
            Route::get('/customer-types', [LookupController::class, 'getCustomerTypes']);
            Route::get('/quotation-statuses', [LookupController::class, 'getQuotationStatuses']);
            Route::get('/commission-types', [LookupController::class, 'getCommissionTypes']);
            Route::get('/statuses', [LookupController::class, 'getStatuses']);
            Route::get('/all', [LookupController::class, 'getAllLookups']);
        });
        
        // General user info
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
    
    // API Health check (no authentication required)
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'version' => 'v1',
        ]);
    });
});
