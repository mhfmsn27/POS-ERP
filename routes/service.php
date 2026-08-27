<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Starter\BusinessTransactionController;
use App\Http\Controllers\Api\Starter\PackageController;
use App\Http\Controllers\Api\Store\StoreController;
use Illuminate\Support\Facades\Route;

include('service/authentication.php');


Route::prefix('starter')->middleware('auth:sanctum')->group(function () {
    Route::get('packages', [PackageController::class, 'index']);
    Route::get('package-detail/{package}', [PackageController::class, 'detail']);

    Route::prefix('transactions')->group(function () {
        Route::get('/', [BusinessTransactionController::class, 'index']);
        Route::get('midtrans-key', [BusinessTransactionController::class, 'midtransKey']);
        Route::post("store", [BusinessTransactionController::class, 'store']);
        Route::post('add-payment/{transaction}', [BusinessTransactionController::class, 'addPayment']);
        Route::delete('delete/{transaction}', [BusinessTransactionController::class, 'deleteTransaction']);
    });
});


Route::prefix('app')->middleware('auth:sanctum')->group(function () {

    Route::prefix('dashboard')->group(function () {
        Route::get('activity', [DashboardController::class, 'activityLog']);
        Route::get('alerts', [DashboardController::class, 'stockAlert']);
        Route::get('profitable', [DashboardController::class, 'profitCost']);
        Route::get('active-user', [DashboardController::class, 'activeUsers']);
        Route::get("top-products", [DashboardController::class, 'topProducts']);
        Route::get("top-customers", [DashboardController::class, 'topCustomers']);
        Route::get("daily-sales", [DashboardController::class, 'dailySale']);
        Route::get('rekonsiliastions', [DashboardController::class, 'rekonsiliasiBank']);
        Route::get('monthly-analisis', [DashboardController::class, 'monthlyAnalisis']);
        Route::get('piutang', [DashboardController::class, 'dataPiutang']);

        Route::prefix('dues')->group(function () {
            Route::get('customer', [DashboardController::class, 'customerDue']);
        });
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index']);
        Route::post('update', [ProfileController::class, 'changeProfile']);
        Route::post('password', [ProfileController::class, 'changePassword']);
    });

    Route::prefix('stores')->group(function () {
        Route::get('/', [StoreController::class, 'index']);
        Route::post('store', [StoreController::class, 'createStore']);
    });

    Route::middleware('is_store')->group(function () {

        // Accounting Route
        include('modules/account.php');

        // Master Data Route
        include('modules/master.php');

        // Master Data Inventori
        include('modules/inventori.php');

        // CRM
        include('modules/crm.php');

        // HRM
        include('modules/hrm.php');

        // Transactions
        include('modules/transactions.php');

        // Reports
        include('modules/reports.php');

        // Settings
        include('modules/settings.php');

        // Taxes
        include('modules/taxes.php');
    });
});
