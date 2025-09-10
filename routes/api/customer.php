<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Customer\Http\Controllers\Api\CustomerApiController;

/*
|--------------------------------------------------------------------------
| Customer Module API Routes
|--------------------------------------------------------------------------
|
| These routes provide API access to customer module functionality.
| All routes are prefixed with /api/customers and include authentication.
|
*/

Route::middleware(['auth:sanctum'])->prefix('customers')->group(function () {
    // Customer CRUD operations
    Route::get('/', [CustomerApiController::class, 'index']);
    Route::post('/', [CustomerApiController::class, 'store']);
    Route::get('/{customer}', [CustomerApiController::class, 'show']);
    Route::put('/{customer}', [CustomerApiController::class, 'update']);
    Route::delete('/{customer}', [CustomerApiController::class, 'destroy']);
    
    // Customer status management
    Route::patch('/{customer}/status', [CustomerApiController::class, 'updateStatus']);
    
    // Customer search and filtering
    Route::get('/search/{query}', [CustomerApiController::class, 'search']);
    Route::get('/type/{type}', [CustomerApiController::class, 'getByType']);
    Route::get('/family/{familyGroupId}', [CustomerApiController::class, 'getByFamily']);
    
    // Customer statistics
    Route::get('/stats/overview', [CustomerApiController::class, 'getStatistics']);
    
    // Customer communications
    Route::post('/{customer}/send-onboarding', [CustomerApiController::class, 'sendOnboardingMessage']);
    
    // Active customers for selections
    Route::get('/active/list', [CustomerApiController::class, 'getActiveForSelection']);
});