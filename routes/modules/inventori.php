<?php

use App\Http\Controllers\Api\Inventory\BrandController;
use App\Http\Controllers\Api\Inventory\CategoryController;
use App\Http\Controllers\Api\Inventory\RakController;
use App\Http\Controllers\Api\Inventory\UnitController;
use App\Http\Controllers\Api\Inventory\ProductComponentController;
use App\Http\Controllers\Api\Inventory\ProductsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Inventori API Route
|--------------------------------------------------------------------------
*/

Route::prefix('inventory')->group(function () {

    Route::get('/', [ProductsController::class, 'index']);
    Route::get('variations', [ProductsController::class, 'variations']);
    Route::post('create', [ProductsController::class, 'create']);
    Route::get('detail/{product}', [ProductsController::class, 'details']);
    Route::post('update/{product}', [ProductsController::class, 'update']);
    Route::post('update-account/{product}', [ProductsController::class, 'updateAccount']);
    Route::delete('delete-media/{media}', [ProductsController::class, 'deleteMedia']);
    Route::post('update-variations/{product}', [ProductsController::class, 'updateVariations']);
    Route::delete('delete-variations/{variation}', [ProductsController::class, 'deleteVariation']);
    Route::post('delete-many-product', [ProductsController::class, 'deleteManyProduct']);
    Route::delete('delete/{product}', [ProductsController::class, 'delete']);
    Route::post('import', [ProductsController::class, 'import']);
    Route::get('download-sample', [ProductsController::class, 'downloadSample']);
    Route::get('download', [ProductsController::class, 'download']);
    Route::get('download-spt', [ProductsController::class, 'downloadSpt']);
    Route::post('change-price/{variation}', [ProductsController::class, 'changePrice']);


    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('create', [CategoryController::class, 'create']);
        Route::post('update/{category}', [CategoryController::class, 'update']);
        Route::delete('delete/{category}', [CategoryController::class, 'delete']);
        Route::post('import', [CategoryController::class, 'import']);
        Route::get('download-sample', [CategoryController::class, 'downloadSample']);
    });

    Route::prefix('brands')->group(function () {
        Route::get('/', [BrandController::class, 'index']);
        Route::post('create', [BrandController::class, 'create']);
        Route::post('update/{brand}', [BrandController::class, 'update']);
        Route::delete('delete/{brand}', [BrandController::class, 'delete']);
    });

    Route::prefix('units')->group(function () {
        Route::get('/', [UnitController::class, 'index']);
        Route::post('create', [UnitController::class, 'create']);
        Route::post('update/{unit}', [UnitController::class, 'update']);
        Route::delete('delete/{unit}', [UnitController::class, 'delete']);
    });

    Route::prefix('raks')->group(function () {
        Route::get('/', [RakController::class, 'index']);
        Route::post('create', [RakController::class, 'create']);
        Route::post('update/{rak}', [RakController::class, 'update']);
        Route::delete('delete/{rak}', [RakController::class, 'delete']);
    });

    Route::prefix('components')->group(function () {
        Route::get('categories', [ProductComponentController::class, 'category']);
        Route::get('brands', [ProductComponentController::class, 'brands']);
        Route::get('units', [ProductComponentController::class, 'units']);
        Route::get('raks', [ProductComponentController::class, 'raks']);
        Route::get('variations', [ProductComponentController::class, 'variations']);
    });
});
