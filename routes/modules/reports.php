<?php



/*
|--------------------------------------------------------------------------
| Reports API Route
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\Reports\ActivityReportsController;
use App\Http\Controllers\Api\Reports\BankMutationReportController;
use App\Http\Controllers\Api\Reports\CommissionReportController;
use App\Http\Controllers\Api\Reports\HutangReportController;
use App\Http\Controllers\Api\Reports\NeracaController;
use App\Http\Controllers\Api\Reports\NonAccountant\ProfitSellController as NonAccountantProfitSellController;
use App\Http\Controllers\Api\Reports\PiutangReportController;
use App\Http\Controllers\Api\Reports\ProductsReportController;
use App\Http\Controllers\Api\Reports\ProfitSellController;
use App\Http\Controllers\Api\Reports\StockReportsController;
use Illuminate\Support\Facades\Route;

Route::prefix('reports')->group(function () {

    // Stock Reports
    Route::prefix('stocks')->group(function () {
        Route::get('histories', [StockReportsController::class, 'histories']);
        Route::get('stocks', [StockReportsController::class, 'stocks']);
    });

    Route::prefix('profits')->group(function () {
        Route::post('standart', [ProfitSellController::class, 'index']);
        Route::post('priode', [ProfitSellController::class, 'priode']);
        Route::post('non-standart', [NonAccountantProfitSellController::class, 'index']);
        Route::post('non-priode', [NonAccountantProfitSellController::class, 'priode']);
    });

    Route::prefix('neraca')->group(function () {
        Route::post('standart', [NeracaController::class, 'index']);
    });

    Route::prefix('commission')->group(function () {
        Route::get('list', [CommissionReportController::class, 'index']);
        Route::get('detail/{user}', [CommissionReportController::class, 'detail']);

        Route::get('bank-mutation', [BankMutationReportController::class, 'index']);
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductsReportController::class, 'index']);
        Route::get('minus', [ProductsReportController::class, 'minus']);
    });

    Route::prefix('crm')->group(function () {
        Route::prefix('customers')->group(function () {
            Route::get('/', [PiutangReportController::class, 'index']);
            Route::get('saldo', [PiutangReportController::class, 'hutang']);
        });

        Route::prefix('suppliers')->group(function () {
            Route::get('/', [HutangReportController::class, 'index']);
            Route::get('saldo', [HutangReportController::class, 'hutang']);
        });
    });

    Route::prefix('activities')->group(function () {
        Route::get('/', [ActivityReportsController::class, 'activity']); 
    });
});
