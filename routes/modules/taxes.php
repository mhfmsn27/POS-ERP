<?php



/*
|--------------------------------------------------------------------------
| Taxes API Route
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\Tax\TaxNoRefController;
use Illuminate\Support\Facades\Route;

Route::prefix('taxes')->group(function () {
    Route::prefix('number')->group(function () {
        Route::get('/', [TaxNoRefController::class, 'index']);
        Route::get('detail/{taxes}', [TaxNoRefController::class, 'details']);
        Route::post('create', [TaxNoRefController::class, 'store']);
        Route::delete('delete/{taxes}', [TaxNoRefController::class, 'delete']);
        Route::get('get', [TaxNoRefController::class, 'getNumber']);
    });
});
