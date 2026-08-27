<?php

use App\Http\Controllers\Api\Master\CourierController;
use App\Http\Controllers\Api\Rma\RmaController;
use App\Http\Controllers\Api\Transaction\OfferController;
use App\Http\Controllers\Api\Transaction\PaymentSalesController;
use App\Http\Controllers\Api\Transaction\POController;
use App\Http\Controllers\Api\Transaction\PurchaseController;
use App\Http\Controllers\Api\Transaction\PurchasePaymentController;
use App\Http\Controllers\Api\Transaction\ReceivedProductController;
use App\Http\Controllers\Api\Transaction\Return\ReturnPurchaseController;
use App\Http\Controllers\Api\Transaction\Return\ReturnSaleController;
use App\Http\Controllers\Api\Transaction\SalesController;
use App\Http\Controllers\Api\Transaction\ShippingProductController;
use App\Http\Controllers\Api\Transaction\StockOpnameController;
use App\Http\Controllers\Api\Transaction\TransactionDueController;
use App\Http\Controllers\Api\Transaction\TransactionPaymentController;
use App\Http\Controllers\Api\Transaction\WarehouseTransferController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Transactions API Route
|--------------------------------------------------------------------------
*/

Route::prefix('transactions')->group(function () {

    // Purchase Route
    Route::prefix('purchases')->group(function () {

        Route::prefix('po')->group(function () {
            Route::get('/', [POController::class, 'index']);
            Route::get('detail/{transaction}', [POController::class, 'detail']);
            Route::post('create', [POController::class, 'createData']);
            Route::post('update/{transaction}', [POController::class, 'updateData']);
            Route::post('update-item/{purchase}', [POController::class, 'updateItem']);
            Route::delete('delete-item/{purchase}', [POController::class, 'deleteItem']);
            Route::delete('delete/{transaction}', [POController::class, 'delete']);
        });

        Route::prefix('received')->group(function () {
            Route::get('/', [ReceivedProductController::class, 'index']);
            Route::get('detail/{transaction}', [ReceivedProductController::class, 'detail']);
            Route::post('create', [ReceivedProductController::class, 'createData']);
            Route::post('update/{transaction}', [ReceivedProductController::class, 'updateData']);
            Route::post('update-item/{purchase}', [ReceivedProductController::class, 'updateItem']);
            Route::delete('delete-item/{purchase}', [ReceivedProductController::class, 'deleteItem']);
            Route::delete('delete/{transaction}', [ReceivedProductController::class, 'delete']);
        });

        Route::prefix('faktur')->group(function () {
            Route::get('/', [PurchasePaymentController::class, 'index']);
            Route::post('create', [PurchasePaymentController::class, 'store']);
            Route::post('update/{transaction}', [PurchasePaymentController::class, 'update']);
            Route::delete('delete-item/{faktur}', [PurchasePaymentController::class, 'deleteItem']);
            Route::delete('delete-draft/{transaction}', [PurchasePaymentController::class, 'deleteDraft']);
            Route::get('detail/{transaction}', [PurchasePaymentController::class, 'detail']);
        });

        Route::prefix('returns')->group(function () {
            Route::get('/', [ReturnPurchaseController::class, 'index']);
            Route::post('create/{transaction}', [ReturnPurchaseController::class, 'create']);
            Route::get('detail/{transaction}', [ReturnPurchaseController::class, 'detail']);
            Route::delete('delete/{transaction}', [ReturnPurchaseController::class, 'delete']);
        });

        Route::get('/', [PurchaseController::class, 'index']);
        Route::post('create', [PurchaseController::class, 'store']);
        Route::post('update/{transaction}', [PurchaseController::class, 'update']);
        Route::delete('delete-item/{purchase}', [PurchaseController::class, 'deleteItem']);
        Route::delete('delete-draft/{transaction}', [PurchaseController::class, 'deleteDraft']);
        Route::get('detail/{transaction}', [PurchaseController::class, 'detail']);
        Route::get('edit/{transaction}', [PurchaseController::class, 'editDraft']);
    });

    // Sales Route
    Route::prefix('sales')->group(function () {

        Route::prefix('offer')->group(function () {
            Route::get('/', [OfferController::class, 'index']);
            Route::get('detail/{transaction}', [OfferController::class, 'detail']);
            Route::post('update/{transaction}', [OfferController::class, 'updateData']);
            Route::post('create', [OfferController::class, 'createData']);
            Route::post('update-item/{sales}', [OfferController::class, 'updateItem']);
            Route::delete('delete-item/{sales}', [OfferController::class, 'deleteItem']);
            Route::delete('delete/{transaction}', [OfferController::class, 'delete']);
        });

        Route::prefix('shipping')->group(function () {
            Route::get('/', [ShippingProductController::class, 'index']);
            Route::get('detail/{transaction}', [ShippingProductController::class, 'detail']);
            Route::post('update/{transaction}', [ShippingProductController::class, 'updateData']);
            Route::post('create', [ShippingProductController::class, 'createData']);
            Route::post('update-item/{sales}', [ShippingProductController::class, 'updateItem']);
            Route::delete('delete-item/{sales}', [ShippingProductController::class, 'deleteItem']);
            Route::delete('delete/{transaction}', [ShippingProductController::class, 'delete']);
        });

        Route::prefix('faktur')->group(function () {
            Route::get('/', [PaymentSalesController::class, 'index']);
            Route::post('create', [PaymentSalesController::class, 'store']);
            Route::post('update/{transaction}', [PaymentSalesController::class, 'update']);
            Route::delete('delete-item/{faktur}', [PaymentSalesController::class, 'deleteItem']);
            Route::delete('delete-draft/{transaction}', [PaymentSalesController::class, 'deleteDraft']);
            Route::get('detail/{transaction}', [PaymentSalesController::class, 'detail']);
        });

        Route::prefix('returns')->group(function () {
            Route::get('/', [ReturnSaleController::class, 'index']);
            Route::post('create/{transaction}', [ReturnSaleController::class, 'create']);
            Route::get('detail/{transaction}', [ReturnSaleController::class, 'detail']);
            Route::delete('delete/{transaction}', [ReturnSaleController::class, 'delete']);
        });

        Route::get('/', [SalesController::class, 'index']);
        Route::post('create', [SalesController::class, 'store']);
        Route::post('update/{transaction}', [SalesController::class, 'update']);
        Route::delete('delete-item/{sell}', [SalesController::class, 'deleteItem']);
        Route::delete('delete-draft/{transaction}', [SalesController::class, 'deleteDraft']);
        Route::get('detail/{transaction}', [SalesController::class, 'detail']);
        Route::get('edit/{transaction}', [SalesController::class, 'editDraft']);

        Route::get('price-history', [SalesController::class, 'historysells']);
    });

    // Due Transactions
    Route::prefix('transaction-due')->group(function () {
        Route::get('/', [TransactionDueController::class, 'index']);
        Route::get('history', [TransactionPaymentController::class, 'index']);
        Route::post('payment/{transaction}', [TransactionDueController::class, 'addPay']);
        Route::post('update-payment/{payment}', [TransactionDueController::class, 'updatePayment']);
        Route::delete('delete-payment/{payment}', [TransactionPaymentController::class, 'delete']);
    });

    Route::prefix('stock-opname')->group(function () {
        Route::get('/', [StockOpnameController::class, 'index']);
        Route::post('store', [StockOpnameController::class, 'store']);
        Route::get('detail/{transaction}', [StockOpnameController::class, 'detail']);
    });

    Route::prefix('transfer-warehouse')->group(function () {
        Route::get('/', [WarehouseTransferController::class, 'index']);
        Route::post('store', [WarehouseTransferController::class, 'store']);
        Route::get('detail/{transaction}', [WarehouseTransferController::class, 'detail']);
    });

    Route::prefix('components')->group(function () {
        Route::get('couriers', [CourierController::class, 'search']);
    });

    // Rma Routes
    Route::prefix('rma')->group(function () {

        Route::get('/', [RmaController::class, 'index']);
        Route::get('detail/{transaction}', [RmaController::class, 'detail']);
        Route::post('update/{transaction}', [RmaController::class, 'update']);
        Route::post('store', [RmaController::class, 'store']);
        Route::delete('delete/{transaction}', [RmaController::class, 'delete']);
        Route::delete('delete-item/{detail}', [RmaController::class, 'deleteItem']);

        Route::prefix('records')->group(function () {
            Route::post('add/{detail}',[RmaController::class,'updateDetails']);
            Route::get('delete/{record}', [RmaController::class, 'deleteRecord']);
        });
    });
});
