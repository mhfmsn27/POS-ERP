<?php

use Illuminate\Support\Facades\Route;
use Poshub\Ecommerce\Controllers\Api\BannersController;
use Poshub\Ecommerce\Controllers\Api\BlogCategoryController;
use Poshub\Ecommerce\Controllers\Api\BlogsController;
use Poshub\Ecommerce\Controllers\Api\CategoryController;
use Poshub\Ecommerce\Controllers\Api\EcommerceBankController;
use Poshub\Ecommerce\Controllers\Api\FeaturedController;
use Poshub\Ecommerce\Controllers\Api\SettingsController;
use Poshub\Ecommerce\Controllers\Api\SlidersController;
use Poshub\Ecommerce\Controllers\Api\AboutUsController;
use Poshub\Ecommerce\Controllers\Api\TransactionController;
use Poshub\Ecommerce\Controllers\HomeController;
use Poshub\Ecommerce\Controllers\MidtransController;

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

Route::prefix('payment-callback')->group(function () {
      Route::post('midtrans', [MidtransController::class, 'callBackMidtrans']);
});


Route::middleware(['auth:sanctum', 'is_store'])->prefix('service-api/app/ecommerce')->group(function () {


      Route::prefix('location')->group(function () {
            Route::get("provinces", [HomeController::class, 'getProvinces']);
            Route::get("cities", [HomeController::class, 'getCities']);
            Route::get("district", [HomeController::class, 'district']);
      });

      Route::prefix('media-content')->group(function () {

            Route::prefix('sliders')->group(function () {
                  Route::get("/", [SlidersController::class, 'index']);
                  Route::get('detail/{id}', [SlidersController::class, 'detail']);
                  Route::delete("delete/{id}", [SlidersController::class, 'delete']);
                  Route::post("create", [SlidersController::class, 'store']);
                  Route::post("update/{id}", [SlidersController::class, 'edit']);
            });

            Route::prefix('banners')->group(function () {
                  Route::get("/", [BannersController::class, 'index']);
                  Route::get('detail/{id}', [BannersController::class, 'detail']);
                  Route::delete("delete/{id}", [BannersController::class, 'delete']);
                  Route::post("create", [BannersController::class, 'store']);
                  Route::post("update/{id}", [BannersController::class, 'edit']);
            });

            Route::prefix('featureds')->group(function () {
                  Route::get("/", [FeaturedController::class, 'index']);
                  Route::get('detail/{id}', [FeaturedController::class, 'detail']);
                  Route::delete("delete/{id}", [FeaturedController::class, 'delete']);
                  Route::post("create", [FeaturedController::class, 'store']);
                  Route::post("update/{featured}", [FeaturedController::class, 'edit']);
            });

            Route::prefix('categories')->group(function () {
                  Route::get("/", [CategoryController::class, 'index']);
                  Route::delete("delete/{id}", [CategoryController::class, 'delete']);
                  Route::post("create", [CategoryController::class, 'store']);
                  Route::post('change/{id}', [CategoryController::class, 'changeFeatures']);
            });
      });

      Route::prefix('blogs')->group(function () {
            Route::prefix('article')->group(function () {
                  Route::get("/", [BlogsController::class, 'index']);
                  Route::get("detail/{id}", [BlogsController::class, 'detail']);
                  Route::delete("delete/{id}", [BlogsController::class, 'delete']);
                  Route::post("create", [BlogsController::class, 'store']);
                  Route::post("update/{id}", [BlogsController::class, 'edit']);
            });

            Route::prefix('categories')->group(function () {
                  Route::get("/", [BlogCategoryController::class, 'index']);
                  Route::get("detail/{id}", [BlogCategoryController::class, 'detail']);
                  Route::delete("delete/{id}", [BlogCategoryController::class, 'delete']);
                  Route::post("create", [BlogCategoryController::class, 'store']);
                  Route::post("update/{id}", [BlogCategoryController::class, 'edit']);
            });

            Route::prefix('abouts')->group(function () {
                  Route::get("/", [AboutUsController::class, 'index']);
                  Route::post("update", [AboutUsController::class, 'store']);
                  Route::get("social", [AboutUsController::class, 'social']);
                  Route::post("social-store", [AboutUsController::class, 'socialStore']);
            });
      });

      Route::prefix('settings')->group(function () {

            Route::prefix('integrations')->group(function () {
                  Route::get("/", [SettingsController::class, 'index']);
                  Route::post("store", [SettingsController::class, 'store']);
            });

            Route::prefix('bank-account')->group(function () {
                  Route::get("/", [EcommerceBankController::class, 'index']);
                  Route::get("delete/{bank}", [EcommerceBankController::class, 'delete']);
                  Route::post("store", [EcommerceBankController::class, 'store']);
                  Route::post("edit/{bank}", [EcommerceBankController::class, 'edit']);
            });

            // Route::prefix('curir')->group(function () {
            //       Route::get("/", [KurirController::class, 'index']);
            //       Route::get("delete/{curir}", [KurirController::class, 'delete']);
            //       Route::post("store", [KurirController::class, 'store']);
            //       Route::post("edit/{curir}", [KurirController::class, 'edit']);
            // });


            // Route::prefix('province')->group(function () {
            //       Route::get("/", [ProvinceController::class, 'index']);
            //       Route::get("/{province}", [ProvinceController::class, 'updateProvince']);
            // });

            // Route::prefix('city')->group(function () {
            //       Route::get("/", [ProvinceController::class, 'city'])->name('ecommerce.sett.city');
            //       Route::get("/{province}", [ProvinceController::class, 'updateCity'])->name('ecommerce.sett.city.status');
            // });

            // Route::prefix('district')->group(function () {
            //       Route::get("/", [ProvinceController::class, 'district'])->name('ecommerce.sett.district');
            //       Route::get("/{province}", [ProvinceController::class, 'updateDistrict'])->name('ecommerce.sett.district.status');
            // });
      });

      Route::prefix('orders')->group(function () {
            Route::get("/", [TransactionController::class, 'index']); 
            Route::get("detail/{id}", [TransactionController::class, 'detail']);
            Route::post("send-order/{id}", [TransactionController::class, 'sendOrder']);
            Route::post('confirmation-payment/{id}', [TransactionController::class, 'confirmationPayment']);
            Route::post('rejected-payment/{id}', [TransactionController::class, 'rejectedPayment']);
      });
});
