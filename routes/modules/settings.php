<?php

use App\Http\Controllers\Api\Master\UserController;
use App\Http\Controllers\Api\Setting\HrmSettingController;
use App\Http\Controllers\Api\Setting\KeySettingController;
use App\Http\Controllers\Api\Setting\NotificationSettingController;
use App\Http\Controllers\Api\Setting\NotificationTemplateController;
use App\Http\Controllers\Api\Setting\SettingAccountController;
use App\Http\Controllers\Api\Setting\WarehouseController;
use App\Http\Controllers\Api\Setting\WhatsappDeviceController;
use App\Http\Controllers\Api\Store\StoreController;
use App\Http\Controllers\Api\User\UserGroupController;
use App\Http\Controllers\Api\User\UsersController;
use App\Http\Resources\User\UserGroupResource;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CRM API Route
|--------------------------------------------------------------------------
*/

Route::prefix('settings')->group(function () {

    Route::prefix('keys')->group(function () {
        Route::get('/', [KeySettingController::class, 'index']);
        Route::post('store', [KeySettingController::class, 'store']);
    });

    Route::prefix('hrm')->group(function () {
        Route::get('/', [HrmSettingController::class, 'index']);
        Route::post('store', [HrmSettingController::class, 'store']);
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationSettingController::class, 'index']);
        Route::post('store', [NotificationSettingController::class, 'store']);
    });

    Route::prefix('stores')->group(function () {
        Route::get('/', [StoreController::class, 'detail']);
        Route::post('ask-otp', [StoreController::class, 'sendOtp']);
        Route::post('delete', [StoreController::class, 'delete']);
        Route::post('update', [StoreController::class, 'update']);
    });

    Route::prefix('roles')->group(function () {
        Route::get("/", [UserGroupController::class, 'index']);
        Route::get('modules', [UserGroupController::class, 'modules']);
        Route::post('create', [UserGroupController::class, 'store']);
        Route::post('update/{role}', [UserGroupController::class, 'update']);
        Route::delete('delete/{role}', [UserGroupController::class, 'delete']);
        Route::get('permissions/{role}', [UserGroupController::class, 'permissions']);
        Route::post('change-permission/{role}/{permission}', [UserGroupController::class, 'changePermission']);
    });

    Route::prefix('users')->group(function () {
        Route::get("/", [UsersController::class, 'index']);
        Route::get("detail/{user}", [UsersController::class, 'detail']);
        Route::post('create', [UsersController::class, 'store']);
        Route::post('update/{user}', [UsersController::class, 'update']);
        Route::delete('delete/{user}', [UsersController::class, 'delete']);
    });

    Route::prefix('devices')->group(function () {
        Route::get("/", [WhatsappDeviceController::class, 'index']);
        Route::post('create', [WhatsappDeviceController::class, 'store']);
        Route::post('update/{device}', [WhatsappDeviceController::class, 'update']);
        Route::delete('delete/{device}', [WhatsappDeviceController::class, 'delete']);
    });

    Route::prefix('templates')->group(function () {
        Route::get("/", [NotificationTemplateController::class, 'index']);
        Route::get("detail/{template}", [NotificationTemplateController::class, 'detail']);
        Route::post('create', [NotificationTemplateController::class, 'store']);
        Route::post('update/{template}', [NotificationTemplateController::class, 'update']);
        Route::delete('delete/{template}', [NotificationTemplateController::class, 'delete']);
    });

    Route::prefix('account')->group(function () {
        Route::get("data", [SettingAccountController::class, 'index']);
        Route::post('crm', [SettingAccountController::class, 'updateCrm']);
        Route::post('product', [SettingAccountController::class, 'updateProduct']);
        Route::post('transaction', [SettingAccountController::class, 'updateTransaction']);
        Route::post('taxrate', [SettingAccountController::class, 'updateTaxrate']);
    });

    Route::prefix('warehouses')->group(function () {
        Route::get("/", [WarehouseController::class, 'index']);
        Route::get('search', [WarehouseController::class, 'forChoose']);
        Route::post('create', [WarehouseController::class, 'store']);
        Route::post('update/{warehouse}', [WarehouseController::class, 'update']);
        Route::delete('delete/{warehouse}', [WarehouseController::class, 'delete']);
    });

    Route::prefix('table-view')->group(function () {
        Route::get('/', [UserController::class, 'tableOptions']);
        Route::post('store', [UserController::class, 'createOptions']);
    });
});
