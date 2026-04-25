<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StationController;
use App\Http\Controllers\DailyClosingController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FuelOrderController;
use App\Http\Controllers\MaintenanceJobOrderController;
use App\Http\Controllers\FuelOrderFinanceController;
use App\Http\Controllers\FuelOrderTransportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CarrierController;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;




/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Stations
    Route::resource('stations', StationController::class);
    Route::patch('/stations/{station}/status/{status}', [StationController::class, 'changeStatus'])->name('stations.changeStatus');

    // Daily Closings
    Route::resource('daily_closings', DailyClosingController::class)->only(['index', 'create', 'store', 'show']);

    // Companies
    Route::resource('companies', CompanyController::class);

    // Set company (multi-tenant)
    Route::get('/set-company', function (\Illuminate\Http\Request $request) {
        session(['company_id' => $request->company_id]);
        return back();
    })->name('set.company');

    // Sales
    Route::resource('sales', SalesController::class);

    // Maintenance Requests
    Route::resource('maintenance-requests', MaintenanceRequestController::class);
    Route::patch('/maintenance-requests/{maintenanceRequest}/status', [MaintenanceRequestController::class, 'updatestatus'])->name('maintenance-requests.update-status');

    // Users
    Route::resource('users', UserController::class);

    Route::get('/dashboard/stations', [StationController::class, 'stationsDashboard'])
    ->name('dashboard.stations');

    // Fuel Orders
    Route::get('/fuel-orders', [FuelOrderController::class, 'index'])->name('fuel-orders.index');
    Route::get('/fuel-orders/create', [FuelOrderController::class, 'create'])->name('fuel-orders.create');
    Route::post('/fuel-orders', [FuelOrderController::class, 'store'])->name('fuel-orders.store');
    Route::get('/fuel-orders/{id}', [FuelOrderController::class, 'show'])->name('fuel-orders.show');
    Route::get('/receiving/orders', [FuelOrderController::class, 'receiving'])->name('fuel-orders.receiving');
    Route::post('/fuel-orders/items/{id}/approve', [FuelOrderController::class, 'approveItem'])->name('fuel-orders.items.approve');
    Route::post('/fuel-orders/items/{id}/reject', [FuelOrderController::class, 'rejectItem'])->name('fuel-orders.items.reject');
    Route::get('/maintenance-job-orders', [MaintenanceJobOrderController::class, 'index'])
    ->name('maintenance-job-orders.index');

    Route::get('/maintenance-job-orders/{id}', [MaintenanceJobOrderController::class, 'show'])
        ->name('maintenance-job-orders.show');

    Route::post('/maintenance-requests/{id}/create-job', [MaintenanceJobOrderController::class, 'createFromRequest'])
        ->name('maintenance-job-orders.create-from-request');

    Route::post('/maintenance-job-orders/{id}/assign-technician', [MaintenanceJobOrderController::class, 'assignTechnician'])
        ->name('maintenance-job-orders.assign-technician');

    Route::post('/maintenance-job-orders/{id}/status', [MaintenanceJobOrderController::class, 'updatestatus'])
        ->name('maintenance-job-orders.update-status');

    Route::post('/fuel-orders/{id}/transport/update', [FuelOrderTransportController::class, 'update'])
        ->name('fuel-orders.transport.update');

    Route::get('/fuel-orders/{id}/receiving/edit', [FuelOrderController::class, 'receivingEdit'])
    ->name('fuel-orders.receiving.edit');

    Route::post('/fuel-orders/{id}/receiving/update', [FuelOrderController::class, 'receivingUpdate'])
        ->name('fuel-orders.receiving.update');

    Route::get('/fuel-orders/finance', [FuelOrderController::class, 'finance'])
        ->name('fuel-orders.finance');

    Route::post('/fuel-orders/{id}/finance/approve', [FuelOrderFinanceController::class, 'approveFinance'])
        ->name('fuel-orders.finance.approve');

    Route::post('/fuel-orders/{id}/finance/reject', [FuelOrderFinanceController::class, 'rejectFinance'])
        ->name('fuel-orders.finance.reject');

    Route::post('/fuel-orders/{id}/finance/payment', [FuelOrderFinanceController::class, 'confirmPayment'])
        ->name('fuel-orders.finance.payment');

    Route::post('/fuel-orders/{id}/transport/assign', [FuelOrderTransportController::class, 'assign'])
        ->name('fuel-orders.transport.assign');

    Route::resource('suppliers', SupplierController::class);
    Route::resource('carriers', CarrierController::class);

    Route::get('/run-seed', function () {
        // هذا الأمر يشغل ملف DatabaseSeeder الأساسي
        Artisan::call('db:seed --force');
        return "تم تشغيل الـ Seed بنجاح ونسخ كافة المعلومات الأساسية!";
    });    

});

