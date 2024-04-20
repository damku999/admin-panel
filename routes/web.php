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
use App\Http\Controllers\PremiumTypeController;
use App\Http\Controllers\ReferenceUsersController;
use App\Http\Controllers\InsuranceCompanyController;
use App\Http\Controllers\CustomerInsuranceController;
use App\Http\Controllers\RelationshipManagerController;

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

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

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
    Route::get('/edit/{customer}', [CustomerController::class, 'edit'])->name('edit');
    Route::put('/update/{customer}', [CustomerController::class, 'update'])->name('update');
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


    Route::get('/import-users', [UserController::class, 'importUsers'])->name('import');
    Route::post('/upload-users', [UserController::class, 'uploadUsers'])->name('upload');

    Route::get('export/', [UserController::class, 'export'])->name('export');
});

Route::middleware('auth')->name('delete_common')->group(function () {
    Route::post('delete_common', [CommonController::class, 'deleteCommon']);
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


// Fuel Type
Route::middleware('auth')->prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('export/', [ReportController::class, 'export'])->name('export');
    Route::post('selected/columns', [ReportController::class, 'saveColumns'])->name('save.selected.columns');
    Route::get('load/columns/{report_name}', [ReportController::class, 'loadColumns'])->name('load.selected.columns');
});
