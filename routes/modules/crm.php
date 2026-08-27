<?php

use App\Http\Controllers\Api\Crm\CustomerController;
use App\Http\Controllers\Api\Crm\SupplierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CRM API Route
|--------------------------------------------------------------------------
*/

Route::prefix('crm')->group(function () {

    Route::prefix('suppliers')->group(function () {
        Route::get('/', [SupplierController::class, 'index']);
        Route::get('detail/{supplier}', [SupplierController::class, 'detail']);
        Route::post('create', [SupplierController::class, 'create']);
        Route::post('update/{supplier}', [SupplierController::class, 'update']);
        Route::delete('delete/{supplier}', [SupplierController::class, 'delete']);

        Route::prefix('due')->group(function () {
            Route::post('add/{supplier}', [SupplierController::class, 'addDue']);
            Route::delete('delete/{transaction}', [SupplierController::class, 'deleteDue']);
        });

        Route::prefix('import')->group(function () {
            Route::get('download-sample', [SupplierController::class, 'downloadSample']);
            Route::get('download-saldo', [SupplierController::class, 'downloadSaldo']);
            Route::get('download-due', [SupplierController::class, 'downloadDue']);
            Route::post('/', [SupplierController::class, 'import']);
            Route::post('saldo/{supplier}', [SupplierController::class, 'importSaldo']);
            Route::post('due/{supplier}', [SupplierController::class, 'importDue']);
        });
    });

    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::get('detail/{customer}', [CustomerController::class, 'detail']);
        Route::post('create', [CustomerController::class, 'create']);
        Route::post('update/{customer}', [CustomerController::class, 'update']);
        Route::delete('delete/{customer}', [CustomerController::class, 'delete']);
        Route::get('download-spt', [CustomerController::class, 'downloadSpt']);
        Route::post('set-default/{customer}', [CustomerController::class, 'setDefault']);

        Route::prefix('due')->group(function () {
            Route::post('add/{customer}', [CustomerController::class, 'addDue']);
            Route::delete('delete/{transaction}', [CustomerController::class, 'deleteDue']);
        });

        Route::prefix('import')->group(function () {
            Route::get('download-sample', [CustomerController::class, 'downloadSample']);
            Route::get('download-saldo', [CustomerController::class, 'downloadSaldo']);
            Route::get('download-due', [CustomerController::class, 'downloadDue']);
            Route::post('/', [CustomerController::class, 'import']);
            Route::post('saldo/{customer}', [CustomerController::class, 'importSaldo']);
            Route::post('due/{customer}', [CustomerController::class, 'importDue']);
        });
    });

    Route::prefix('components')->group(function () {
        Route::get('suppliers', [SupplierController::class, 'simple']);
        Route::get('customers', [CustomerController::class, 'simple']);
    });
});
