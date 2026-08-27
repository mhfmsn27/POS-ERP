<?php

use App\Http\Controllers\Api\Company\AllowanceController;
use App\Http\Controllers\Api\Company\CuttingController;
use App\Http\Controllers\Api\Company\DesignationController;
use App\Http\Controllers\Api\Company\DevisiController;
use App\Http\Controllers\Api\Company\EmployeeController;
use App\Http\Controllers\Api\Company\ExpedisiController;
use App\Http\Controllers\Api\Company\PrinterController;
use App\Http\Controllers\Api\Company\TaxrateController as CompanyTaxrateController;
use App\Http\Controllers\Api\Master\PaymentMethodController;
use App\Http\Controllers\Api\Master\SmartlinkBankController;
use App\Http\Controllers\Api\Master\TaxrateController;
use App\Http\Controllers\Api\Master\TermPaymentController;
use App\Http\Controllers\Api\Master\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Master Data API Route
|--------------------------------------------------------------------------
*/

Route::prefix('master')->group(function () {

    Route::prefix('couriers')->group(function () {
        Route::get("/", [ExpedisiController::class, 'index']);
        Route::post('create', [ExpedisiController::class, 'store']);
        Route::post('update/{courier}', [ExpedisiController::class, 'update']);
        Route::delete('delete/{courier}', [ExpedisiController::class, 'delete']);
    });

    Route::prefix('departments')->group(function () {
        Route::get("/", [DevisiController::class, 'index']);
        Route::post('create', [DevisiController::class, 'store']);
        Route::post('update/{department}', [DevisiController::class, 'update']);
        Route::delete('delete/{department}', [DevisiController::class, 'delete']);
    });

    Route::prefix('designations')->group(function () {
        Route::get("/", [DesignationController::class, 'index']);
        Route::post('create', [DesignationController::class, 'store']);
        Route::post('update/{designation}', [DesignationController::class, 'update']);
        Route::delete('delete/{designation}', [DesignationController::class, 'delete']);
    });

    Route::prefix('allowances')->group(function () {
        Route::get("/", [AllowanceController::class, 'index']);
        Route::post('create', [AllowanceController::class, 'store']);
        Route::post('update/{allowance}', [AllowanceController::class, 'update']);
        Route::delete('delete/{allowance}', [AllowanceController::class, 'delete']);
    });

    Route::prefix('cuttings')->group(function () {
        Route::get("/", [CuttingController::class, 'index']);
        Route::post('create', [CuttingController::class, 'store']);
        Route::post('update/{cutting}', [CuttingController::class, 'update']);
        Route::delete('delete/{cutting}', [CuttingController::class, 'delete']);
    });

    Route::prefix('printers')->group(function () {
        Route::get("/", [PrinterController::class, 'index']);
        Route::post('create', [PrinterController::class, 'store']);
        Route::post('update/{printer}', [PrinterController::class, 'update']);
        Route::delete('delete/{printer}', [PrinterController::class, 'delete']);
    });

    Route::prefix('taxrates')->group(function () {
        Route::get("/", [CompanyTaxrateController::class, 'index']);
        Route::post('create', [CompanyTaxrateController::class, 'store']);
        Route::post('update/{taxrate}', [CompanyTaxrateController::class, 'update']);
        Route::delete('delete/{taxrate}', [CompanyTaxrateController::class, 'delete']);
    });

    Route::prefix('employees')->group(function () {
        Route::get("/", [EmployeeController::class, 'index']);
        Route::get("detail/{employee}", [EmployeeController::class, 'detail']);
        Route::post('create', [EmployeeController::class, 'store']);
        Route::post('update/{employee}', [EmployeeController::class, 'update']);
        Route::delete('delete/{employee}', [EmployeeController::class, 'delete']);
    });


    Route::prefix('payment-method')->group(function () {
        Route::get('/', [PaymentMethodController::class, 'index']);
        Route::post('create', [PaymentMethodController::class, 'create']);
        Route::post('update/{method}', [PaymentMethodController::class, 'update']);
        Route::get('history/{method}', [PaymentMethodController::class, 'history']);
        Route::delete('delete/{method}', [PaymentMethodController::class, 'delete']);
        Route::post('add-saldo/{method}', [PaymentMethodController::class, 'addSaldo']);
        Route::post('update-saldo/{transaction}', [PaymentMethodController::class, 'updateSaldo']);
        Route::delete('delete-saldo/{transaction}', [PaymentMethodController::class, 'deleteSaldo']);
    });

    Route::prefix('smartlinks')->group(function () {
        Route::get('/', [SmartlinkBankController::class, 'index']);
        Route::get('detail/{smartlink}', [SmartlinkBankController::class, 'detail']);
        Route::post('create', [SmartlinkBankController::class, 'create']);
        Route::post('update/{smartlink}', [SmartlinkBankController::class, 'update']);
        Route::delete('delete/{smartlink}', [SmartlinkBankController::class, 'delete']);
    });

    Route::prefix('term')->group(function () {
        Route::get('/', [TermPaymentController::class, 'index']);
        Route::post('create', [TermPaymentController::class, 'create']);
        Route::post('update/{term}', [TermPaymentController::class, 'update']);
        Route::post('set/{term}', [TermPaymentController::class, 'setDefault']);
        Route::delete('delete/{term}', [TermPaymentController::class, 'delete']);
    });

    Route::prefix('components')->group(function () {
        Route::get('users', [UserController::class, 'simple']);
        Route::get('sign', [UserController::class, 'sign']);
    });

    Route::prefix('tax')->group(function () {
        Route::get('/', [TaxrateController::class, 'index']);
        Route::get('sett', [TaxrateController::class, 'settings']);
    });
});
