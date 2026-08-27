<?php

use App\Http\Controllers\Administrator\AdministratorComponentController;
use App\Http\Controllers\Administrator\DashboardController;
use App\Http\Controllers\Administrator\MerchantController;
use App\Http\Controllers\Administrator\Notification\DeviceController;
use App\Http\Controllers\Administrator\Notification\SettingController as NotificationSettingController;
use App\Http\Controllers\Administrator\Notification\TemplateController;
use App\Http\Controllers\Administrator\PackageController;
use App\Http\Controllers\Administrator\PackageTransactionController;
use App\Http\Controllers\Administrator\ProfileController;
use App\Http\Controllers\Administrator\SettingController;
use App\Http\Controllers\Administrator\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('administrator')->middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/analisis', [DashboardController::class, 'analisis']);

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('admin.profile');
        Route::post('update', [ProfileController::class, 'changeProfile'])->name('admin.profile.change');
        Route::post('password', [ProfileController::class, 'changePassword'])->name('admin.password.change');
    });

    Route::prefix('package')->group(function () {
        Route::get('/', [PackageController::class, 'index'])->name('admin.package.index');
        Route::get('create', [PackageController::class, 'create'])->name('admin.package.create');
        Route::get('update/{package}', [PackageController::class, 'edit'])->name('admin.package.update');
        Route::post('store', [PackageController::class, 'store'])->name('admin.package.store');
        Route::post('edit/{package}', [PackageController::class, 'update'])->name('admin.package.edit');
        Route::get('delete/{package}', [PackageController::class, 'delete'])->name('admin.package.delete');
    });

    Route::prefix('transactions')->group(function () {
        Route::get('/', [PackageTransactionController::class, 'index'])->name('admin.transaction.package');
        Route::get('detail/{transaction}', [PackageTransactionController::class, 'detail']);
        Route::post('change-status/{transaction}', [PackageTransactionController::class, 'changeStatus']);
    });

    Route::prefix('merchants')->group(function () {
        Route::get('/', [MerchantController::class, 'index'])->name('admin.merchant');
        Route::get('/detail/{merchant}', [MerchantController::class, 'detail'])->name('admin.merchant.detail');
        Route::get('activation-user/{user}', [MerchantController::class, 'activationUser'])->name('admin.merchant.user.activation');
    });

    Route::prefix('settings')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', [UsersController::class, 'index'])->name('administrator.user');
            Route::get('create', [UsersController::class, 'create'])->name('administrator.user.create');
            Route::get('update/{user}', [UsersController::class, 'update'])->name('administrator.user.update');
            Route::get('delete/{user}', [UsersController::class, 'delete'])->name('administrator.user.delete');
            Route::post('store', [UsersController::class, 'store'])->name('administrator.user.store');
            Route::post('edit/{user}', [UsersController::class, 'edit'])->name('administrator.user.edit');
        });

        Route::prefix('settings')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('administrator.setting');
            Route::post('update', [SettingController::class, 'update'])->name('administrator.update');
        });

        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationSettingController::class, 'index'])->name('admin.notification');
            Route::post('update', [NotificationSettingController::class, 'store'])->name('admin.notification.store');
        });
    });

    Route::prefix('components')->group(function () {
        Route::get('stores', [AdministratorComponentController::class, 'stores']);
        Route::get('merchants', [AdministratorComponentController::class, 'merchants']);
    });

    Route::prefix('notifications')->group(function () {
        Route::prefix('device')->group(function () {
            Route::get('/', [DeviceController::class, 'index'])->name('admin.device');
            Route::get('create', [DeviceController::class, 'create'])->name('admin.device.create');
            Route::get('update/{device}', [DeviceController::class, 'update'])->name('admin.device.update');
            Route::get('delete/{device}', [DeviceController::class, 'delete'])->name('admin.device.delete');
            Route::post('store', [DeviceController::class, 'store'])->name('admin.device.store');
            Route::post('edit/{device}', [DeviceController::class, 'edit'])->name('admin.device.edit');
        });

        Route::prefix('template')->group(function () {
            Route::get('/', [TemplateController::class, 'index'])->name('admin.template');
            Route::get('create', [TemplateController::class, 'create'])->name('admin.template.create');
            Route::get('update/{template}', [TemplateController::class, 'update'])->name('admin.template.update');
            Route::get('delete/{template}', [TemplateController::class, 'delete'])->name('admin.template.delete');
            Route::post('store', [TemplateController::class, 'store'])->name('admin.template.store');
            Route::post('edit/{template}', [TemplateController::class, 'edit'])->name('admin.template.edit');
        });
    });
});
