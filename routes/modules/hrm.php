<?php

use App\Http\Controllers\Api\Hrm\CommissionController;
use App\Http\Controllers\Api\Hrm\ComponentController;
use App\Http\Controllers\Api\Hrm\EmployeeController;
use App\Http\Controllers\Api\Hrm\KasbonController;
use App\Http\Controllers\Api\Hrm\SalaryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HRM API Route
|--------------------------------------------------------------------------
*/

Route::prefix('hrm')->group(function () {

    Route::prefix('kasbon')->group(function () {
        Route::get('/', [KasbonController::class, 'index']);
        Route::get('detail/{kasbon}', [KasbonController::class, 'detail']);
        Route::post('create', [KasbonController::class, 'store']);
        Route::post('update/{kasbon}', [KasbonController::class, 'update']);
        Route::delete('delete/{kasbon}', [KasbonController::class, 'delete']);
    });

    Route::prefix('salaries')->group(function () {
        Route::get('/', [SalaryController::class, 'index']);
        Route::post('generate', [SalaryController::class, 'generate']);
        Route::post('create', [SalaryController::class, 'store']);
        Route::get('detail/{salary}',[SalaryController::class,'detail']);
        Route::post('update/{salary}',[SalaryController::class,'update']);
        Route::delete('delete/{salary}',[SalaryController::class,'delete']);
    });

    Route::prefix('commission')->group(function () {
        Route::get('/', [CommissionController::class, 'index']);
    });


    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index']);
        Route::get('departments', [ComponentController::class, 'departments']);
    });
});
