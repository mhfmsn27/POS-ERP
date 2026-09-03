<?php 

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RmaController;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('print', function () {
    $transaction = Transaction::find(43);
    return view('print.sales.label_pengiriman', compact('transaction'));
});

Auth::routes(['verify' => true]);

$installed = Storage::disk('storage')->exists('installed');
if ($installed == false) {
    Route::get('/', function () {
        return redirect('/install');
    });
} else {
    Route::get('/', function () {
        return redirect('/web');
    })->name('home')->middleware('ecommerce');


    Route::get('/home', [HomeController::class, 'redirect'])->middleware(['auth', 'verified'])->name('redirect');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/business-register', [\App\Http\Controllers\Auth\BusinessRegisterController::class, 'index'])->name('business.register');
        Route::post('/business-register/create', [\App\Http\Controllers\Auth\BusinessRegisterController::class, 'businessCreate'])->name('business.register.create');
    });

    Route::get('locale/{locale}', function ($locale) {
        Session::put('locale', $locale);
        return redirect()->back();
    });

    Route::prefix('authentication')->group(function () {
        Route::get('/{vue?}', [PageController::class, 'auth'])->where('vue', '^(?!setup|update|password).*$')->name('page.auth');
    });

    Route::prefix('starter')->group(function () {
        Route::get('/{vue?}', [PageController::class, 'starter'])->where('vue', '^(?!setup|update|password).*$')->name('page.starter');
    });

    Route::prefix('pos')->middleware(['check_license'])->group(function () {
        Route::get('/customer-display', function () {
            return view('pos.customer_display');
        })->name("pos.customer_display");

        Route::get('/kitchen-display', function () {
            return view('pos.kitchen_display');
        })->name("pos.kitchen_display");

        Route::get('/print/{id}', [\App\Http\Controllers\Pos\PosReceiptPrintController::class, 'printThermal'])->name('pos.print');
        Route::get('/print-thermal/{id}', [\App\Http\Controllers\Pos\PosReceiptPrintController::class, 'printThermal'])->name('pos.print_thermal');
        Route::get('/print-download/{id}', [\App\Http\Controllers\Pos\PosReceiptPrintController::class, 'printThermal'])->name('pos.print_download');
        Route::get('/print-raw-escpos/{id}', [\App\Http\Controllers\Pos\PosReceiptPrintController::class, 'getRawEscPos'])->name('pos.raw_escpos');
        Route::post('/send-whatsapp-receipt/{id}', [\App\Http\Controllers\Pos\PosReceiptPrintController::class, 'sendWhatsapp'])->name('pos.send_wa_receipt');

        Route::get('/{vue?}', function () {
            return view('pos', ["page" => "POSHUB - POS"]);
        })->where('vue', '^(?!setup|update|password).*$')->name("pos.module");
    });

    Route::prefix('panel')->middleware(['check_license'])->group(function () {
        Route::get('/{vue?}', [PageController::class, 'panel'])->where('vue', '^(?!setup|update|password).*$');
    });

    Route::prefix('app')->middleware(['check_license'])->group(function () {
        Route::get('/{vue?}', [PageController::class, 'index'])->where('vue', '^(?!setup|update|password).*$')->name('page.home');
    });



    Route::get('/license/locked', [\App\Http\Controllers\Admin\LicenseController::class, 'showLocked'])->name('license.locked');

    Route::prefix('rma')->group(function () {
        Route::get('/', [RmaController::class, 'index'])->name('rma');
        Route::post('check-rma', [RmaController::class, 'check'])->name('check.rma');
        Route::get('record/{any}', [RmaController::class, 'detail'])->name('detail.rma');
    });
}
