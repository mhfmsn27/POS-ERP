<?php

use App\Http\Controllers\Api\Pos\PosController;
use App\Http\Controllers\Api\RestController;
use App\Http\Controllers\Auth\PackageTransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('transaction')->group(function () {
    Route::get('get-invoice/{id}', [RestController::class, 'getInvoice']);
});

Route::prefix('transaction-package')->group(function () {
    Route::post('callback-midtrans', [PackageTransactionController::class, 'callBackMidtrans']);
});

Route::get('/health', [\App\Http\Controllers\Api\System\HealthCheckController::class, 'check']);
Route::get('/customer-display-state/{token}', [\App\Http\Controllers\Api\Enterprise\NextGenEnterpriseController::class, 'getCustomerDisplay']);

// Strategic Enterprise Webhooks & KDS
Route::post('/qris/callback', [\App\Http\Controllers\Api\Enterprise\StrategicEnterpriseController::class, 'qrisCallback']);
Route::get('/kds/tickets/active', [\App\Http\Controllers\Api\Enterprise\StrategicEnterpriseController::class, 'getActiveKdsTickets']);
Route::post('/kds/tickets/{id}/status', [\App\Http\Controllers\Api\Enterprise\StrategicEnterpriseController::class, 'updateKdsStatus']);
Route::post('/shifts/{id}/send-wa-zreport', [\App\Http\Controllers\Api\Enterprise\StrategicEnterpriseController::class, 'sendShiftZReportWa']);
Route::post('/inventory/stock-alerts/generate-auto-po', [\App\Http\Controllers\Api\Inventory\StockAlertPoController::class, 'generate']);
Route::get('/crm/customers/{id}/loyalty-card', [\App\Http\Controllers\Api\Crm\CustomerLoyaltyController::class, 'getLoyaltyCard']);
Route::post('/crm/customers/{id}/redeem-points', [\App\Http\Controllers\Api\Crm\CustomerLoyaltyController::class, 'redeem']);

// Public Ecommerce Storefront APIs
Route::get('/ecommerce/flash-sales', [\App\Http\Controllers\Api\Enterprise\EcommerceEnterpriseController::class, 'getActiveFlashSales']);
Route::post('/ecommerce/cart/track', [\App\Http\Controllers\Api\Enterprise\EcommerceEnterpriseController::class, 'trackCart']);

// System Database Backup Routes
Route::prefix('system/backups')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\System\DatabaseBackupController::class, 'index']);
    Route::post('/create', [\App\Http\Controllers\Api\System\DatabaseBackupController::class, 'create']);
    Route::get('/download/{filename}', [\App\Http\Controllers\Api\System\DatabaseBackupController::class, 'download']);
    Route::delete('/{filename}', [\App\Http\Controllers\Api\System\DatabaseBackupController::class, 'destroy']);
});

// System Maintenance API Routes
Route::prefix('system/maintenance')->group(function () {
    Route::get('/metrics', [\App\Http\Controllers\Api\System\MaintenanceController::class, 'getMetrics']);
    Route::post('/clear-cache', [\App\Http\Controllers\Api\System\MaintenanceController::class, 'clearCache']);
    Route::post('/optimize-db', [\App\Http\Controllers\Api\System\MaintenanceController::class, 'optimizeDb']);
});

// Domain-Locked Enterprise License API Routes
Route::prefix('license')->group(function () {
    Route::get('/status', [\App\Http\Controllers\Admin\LicenseController::class, 'status']);
    Route::post('/check', [\App\Http\Controllers\Admin\LicenseController::class, 'check']);
    Route::post('/refresh', [\App\Http\Controllers\Admin\LicenseController::class, 'refresh']);
});


