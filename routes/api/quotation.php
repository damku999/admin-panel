<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Quotation\Http\Controllers\Api\QuotationApiController;

/*
|--------------------------------------------------------------------------
| Quotation Module API Routes
|--------------------------------------------------------------------------
|
| These routes provide API access to quotation module functionality.
| All routes are prefixed with /api/quotations and include authentication.
|
*/

Route::middleware(['auth:sanctum'])->prefix('quotations')->group(function () {
    // Quotation CRUD operations
    Route::get('/', [QuotationApiController::class, 'index']);
    Route::post('/', [QuotationApiController::class, 'store']);
    Route::get('/{quotation}', [QuotationApiController::class, 'show']);
    Route::put('/{quotation}', [QuotationApiController::class, 'update']);
    Route::delete('/{quotation}', [QuotationApiController::class, 'destroy']);
    
    // Quotation company management
    Route::post('/{quotation}/companies', [QuotationApiController::class, 'generateCompanyQuotes']);
    Route::get('/{quotation}/companies', [QuotationApiController::class, 'getCompanyQuotes']);
    Route::put('/{quotation}/companies', [QuotationApiController::class, 'updateCompanyQuotes']);
    
    // Quotation documents
    Route::get('/{quotation}/pdf', [QuotationApiController::class, 'generatePdf']);
    Route::post('/{quotation}/send-whatsapp', [QuotationApiController::class, 'sendViaWhatsApp']);
    
    // Premium calculations
    Route::post('/calculate-premium', [QuotationApiController::class, 'calculatePremium']);
    
    // Quotation statistics
    Route::get('/stats/overview', [QuotationApiController::class, 'getStatistics']);
    
    // Active quotations
    Route::get('/active/list', [QuotationApiController::class, 'getActiveQuotations']);
    
    // Form data for quotation creation
    Route::get('/form/data', [QuotationApiController::class, 'getFormData']);
});