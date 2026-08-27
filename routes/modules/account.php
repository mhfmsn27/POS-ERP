<?php

use App\Http\Controllers\Api\Account\CashIntOut\CashIntController;
use App\Http\Controllers\Api\Account\CashIntOut\CategoryController;
use App\Http\Controllers\Api\Account\JurnalUmumController;
use App\Http\Controllers\Api\Account\LedgerController;
use App\Http\Controllers\Api\Account\RekonsiliasiBankController;
use App\Http\Controllers\Api\Account\Tax\TaxController;
use App\Http\Controllers\Api\Account\TypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Accounting API Route
|--------------------------------------------------------------------------
*/

Route::prefix('account')->group(function () {

    Route::get('/', [LedgerController::class, 'index']);
    Route::post('create', [LedgerController::class, 'create']);
    Route::post('update/{account}', [LedgerController::class, 'update']);
    Route::post('deposit/{account}', [LedgerController::class, 'deposit']);
    Route::post('transfer/{account}', [LedgerController::class, 'transfer']);
    Route::delete('delete/{account}', [LedgerController::class, 'delete']);
    Route::get('history', [LedgerController::class, 'history']);

    Route::prefix('type')->group(function () {
        Route::get('/', [TypeController::class, 'index']);
        Route::post('create', [TypeController::class, 'create']);
        Route::post('update/{type}', [TypeController::class, 'update']);
        Route::delete('delete/{type}', [TypeController::class, 'delete']);
    });

    Route::prefix('components')->group(function () {
        Route::get('/', [LedgerController::class, 'simple']);
        Route::get('setting', [LedgerController::class, 'ledgerDefault']);
    });

    Route::prefix('rekonsiliasi')->group(function () {
        Route::get('/', [RekonsiliasiBankController::class, 'index']);
        Route::post('rejected/{transaction}', [RekonsiliasiBankController::class, 'rejected']);

        Route::post('import/{account}', [RekonsiliasiBankController::class, 'import']);
        Route::post('auto-match', [RekonsiliasiBankController::class, 'autoMatch']);
        Route::prefix('data')->group(function () {
            Route::get('nota/{mutasi}', [RekonsiliasiBankController::class, 'getNota']);
            Route::get('mutasi/{transaction}', [RekonsiliasiBankController::class, 'getMutasi']);
        });

        Route::prefix('action')->group(function () {
            Route::post('basic/{transaction}', [RekonsiliasiBankController::class, 'action']);
            Route::delete('remove-mutasi/{mutasi}',[RekonsiliasiBankController::class,'rekonsiliasiRemove']);
            Route::post('jurnal/{transaction}', [RekonsiliasiBankController::class, 'actionForNota']);
            Route::post('mutasi/{mutasi}', [RekonsiliasiBankController::class, 'actionForMutasi']);
            Route::post('create/{account}/{mutasi}', [RekonsiliasiBankController::class, 'createTransaction']);
        });
    });
});


Route::prefix('expenses')->middleware('fiscal_period_check')->group(function () {

    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('create', [CategoryController::class, 'create']);
        Route::post('update/{category}', [CategoryController::class, 'update']);
        Route::delete('delete/{category}', [CategoryController::class, 'delete']);
    });

    Route::get('/', [CashIntController::class, 'index']);
    Route::post('create', [CashIntController::class, 'create']);
    Route::post('update/{expense}', [CashIntController::class, 'update']);
    Route::get('detail/{expense}', [CashIntController::class, 'detail']);
    Route::delete('delete/{expense}', [CashIntController::class, 'delete']);
});

Route::prefix('jurnal')->middleware('fiscal_period_check')->group(function () {
    Route::get('/', [JurnalUmumController::class, 'index']);
    Route::post('create', [JurnalUmumController::class, 'create']);
    Route::post('update/{transaction}', [JurnalUmumController::class, 'update']);
    Route::get('detail/{transaction}', [JurnalUmumController::class, 'detail']);
    Route::delete('delete/{transaction}', [JurnalUmumController::class, 'delete']);
});

Route::prefix('taxs')->group(function () {
    Route::get('/', [TaxController::class, 'index']);
    Route::get('download', [TaxController::class, 'download']);
    Route::post('change-status/{transaction}', [TaxController::class, 'changeAction']);
    Route::post('summary', [TaxController::class, 'summary']);
    Route::post('store', [TaxController::class, 'store']);

    Route::prefix('spt')->group(function () {
        Route::get('/', [TaxController::class, 'spt']);
        Route::get('detail/{spt}', [TaxController::class, 'detail']);
        Route::delete('delete/{spt}', [TaxController::class, 'delete']);
    });
});
