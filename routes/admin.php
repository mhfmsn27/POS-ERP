<?php
 
use App\Http\Controllers\Admin\StoreController; 
use App\Http\Controllers\Api\Rma\RmaController;
use App\Http\Controllers\Api\Transaction\PurchaseController;
use App\Http\Controllers\Api\Transaction\PurchasePaymentController;
use App\Http\Controllers\Api\Transaction\SalesController;
use App\Http\Controllers\Auth\PackageController;
use App\Http\Controllers\Auth\PackageTransactionController; 
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'is_merchant'])->group(function () {


    // Store Route
    Route::prefix('store')->group(function () {
        Route::get('choose-store', [StoreController::class, 'index'])->name('store.choose');
        Route::get('choose/{store}', [StoreController::class, 'choose'])->name('choose.store');
        Route::get('create', [StoreController::class, 'create'])->name('store.create');
        Route::post("create-data", [StoreController::class, 'createData'])->name('store.add');
        Route::middleware('store')->group(function () {
            Route::post('store/{any}', [StoreController::class, 'store'])->name('store.store');
            Route::get('update', [StoreController::class, 'update'])->name('store.update');
            Route::get('delete/{store}', [StoreController::class, 'delete'])->name('store.delete');
        });

        Route::prefix('transactions')->group(function () {
            Route::get('order/{store}', [PackageTransactionController::class, 'order'])->name('store.order');
        });
    });

    Route::prefix('packages')->group(function () {
        Route::get('/', [PackageController::class, 'index'])->name('choose.package');
    });

    Route::prefix('transaction-package')->group(function () {
        Route::get('/', [PackageTransactionController::class, 'index'])->name('package.order');
        Route::post('store/{store}', [PackageTransactionController::class, 'transaction'])->name('package.order.store');
       
    });

    Route::middleware(['store'])->group(function () {

        Route::middleware('package_active')->group(function () {

            Route::get('/{vue?}', function () {
                return view('app', ["page" => "Faktur Online - Aplikasi Akuntansi Penjualan Online"]);
            })->where('vue', '^(?!setup|update|password).*$')->name("index");

        });
    });

    Route::prefix('prints')->group(function () {
        Route::get('rma/{transaction}', [RmaController::class, 'print']);
        Route::get('faktur-pengiriman/{transaction}', [SalesController::class, 'printPengiriman']);
        Route::get('faktur-penjualan/{transaction}', [SalesController::class, 'print']);
        Route::get('label-pengiriman/{transaction}', [SalesController::class, 'label']);
        Route::get('pembayaran-pembelian/{transaction}', [PurchasePaymentController::class, 'print']);
        Route::get('penerimaan/{transaction}', [PurchaseController::class, 'penerimaanprint']);
        Route::match(['get', 'post'], 'barcode-print', [\App\Http\Controllers\Api\Inventory\BarcodeController::class, 'printView'])->name('product.barcode.print');
    });

    Route::get('backup-database', [\App\Http\Controllers\Api\System\DatabaseBackupController::class, 'viewIndex'])->name('settings.backup');
    Route::get('system-maintenance', [\App\Http\Controllers\Api\System\MaintenanceController::class, 'viewIndex'])->name('settings.maintenance');
});
