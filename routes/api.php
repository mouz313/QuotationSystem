<?php

use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Company\UserController;
use Illuminate\Support\Facades\Route;

// ── Auth (Public, throttled) ──
Route::middleware('throttle:api')->group(function () {
    Route::post('/v1/register', [AuthController::class, 'register']);
    Route::post('/v1/login', [AuthController::class, 'login']);
});

// ── Auth (Protected) ──
Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('v1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // ── Quotation Store (Company only, NOT admin) ──
    Route::middleware('not.admin')->group(function () {
        Route::post('/quotations', [\App\Http\Controllers\QuotationController::class, 'store'])->name('quotations.store');
        Route::get('/taxes', [\App\Http\Controllers\Company\TaxController::class, 'index']);
        Route::post('/taxes', [\App\Http\Controllers\Company\TaxController::class, 'store']);
    });

    // ── Admin: Company Management ──
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/companies', [CompanyController::class, 'index']);
        Route::get('/companies/{company}', [CompanyController::class, 'show']);
        Route::patch('/companies/{company}/status', [CompanyController::class, 'updateStatus']);
        Route::post('/companies/{company}/assign-package', [CompanyController::class, 'assignPackage']);
        Route::delete('/companies/{company}', [CompanyController::class, 'destroy']);

        // ── Admin: Package Management ──
        Route::apiResource('/packages', PackageController::class);
    });

    // ── Company Admin: User Management ──
    Route::middleware(['company.admin', 'company.active'])->prefix('company')->group(function () {
        Route::get('/users', [UserController::class, 'apiIndex']);
        Route::post('/users', [UserController::class, 'apiStore']);
        Route::get('/users/{user}', [UserController::class, 'apiShow']);
        Route::put('/users/{user}', [UserController::class, 'apiUpdate']);
        Route::delete('/users/{user}', [UserController::class, 'apiDestroy']);
    });
});
