<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Poshub\Ecommerce\Controllers\AccountController;
use Poshub\Ecommerce\Controllers\AddressController;
use Poshub\Ecommerce\Controllers\AuthController;
use Poshub\Ecommerce\Controllers\HomeController;
use Poshub\Ecommerce\Controllers\ShopController;
use Poshub\Ecommerce\Controllers\BlogController;
use Poshub\Ecommerce\Controllers\CartController;
use Poshub\Ecommerce\Controllers\CheckOutController;
use Poshub\Ecommerce\Controllers\MidtransController;
use Poshub\Ecommerce\Controllers\OrderController;
use Poshub\Ecommerce\Controllers\RegisterController;
use Poshub\Ecommerce\Controllers\ResetPassword;

// Auth::routes();

Route::prefix('web')->group(function () {
    Route::prefix('location')->group(function () {
        Route::get("provinces", [HomeController::class, 'getProvinces']);
        Route::get("cities", [HomeController::class, 'getCities']);
        Route::get("district", [HomeController::class, 'district']);
    });
});

Route::prefix('/web')->middleware(['ecommerce', 'web', 'domain_identification'])->group(function () {


    Route::get("/", [HomeController::class, 'index'])->name('ecommerce.home');
    Route::get("branch", [HomeController::class, 'branch'])->name('ecommerce.branch');
    Route::get("about", [HomeController::class, 'about'])->name('ecommerce.about');
    Route::get("top-sells", [ShopController::class, 'topSells'])->name('ecommerce.top_sell');

    Route::get("change-session/{id}", [HomeController::class, 'changeSession'])->name('ecommerce.change_session');



    Route::prefix('shop')->group(function () {
        Route::get("/", [ShopController::class, 'index'])->name('ecommerce.shop');

        Route::get("categories", [ShopController::class, 'getCategory']);
        Route::get("cart", [CartController::class, 'index']);
        Route::get("detail/{product}", [ShopController::class, 'detail'])->name('ecommerce.shop_detail');
        Route::get("variation-detail/{id}", [ShopController::class, 'getDetailVariation']);

        Route::prefix('checkout')->middleware('customers_must_verify')->group(function () {
            Route::get("/", [CheckOutController::class, 'index'])->name('ecommerce.checkout');
            Route::post("by-check", [CheckOutController::class, 'index'])->name('ecommerce.checkout_checked');
            Route::get("get-shipping-cost", [CheckOutController::class, 'getShippingCost']);
            Route::get("cart", [CartController::class, 'cart'])->name('ecommerce.cart');

            Route::post('transactions', [MidtransController::class, 'create']);
        });
    });

    Route::prefix('blog')->group(function () {
        Route::get("/", [BlogController::class, 'index'])->name("ecommerce.blog");
        Route::get("detail/{blog}", [BlogController::class, 'detail'])->name("ecommerce.blog_detail");
    });

    Route::prefix('auth')->middleware('customers_not_login')->group(function () {
        Route::get("login", [AuthController::class, 'index'])->name('ecommerce.login');
        Route::post('signin', [AuthController::class, 'login'])->name('ecommerce.signin');
        Route::get("register", [RegisterController::class, 'index'])->name('ecommerce.register');
        Route::post("signup", [RegisterController::class, 'signup'])->name('ecommerce.signup');

        Route::prefix('forget-password')->group(function () {
            Route::get("/", [ResetPassword::class, 'index'])->name('ecommerce.forget');
            Route::get("reset-password", [ResetPassword::class, 'reset'])->name('ecommerce.reset');
            Route::post("send-ask", [ResetPassword::class, 'forgetPassword'])->name('ecommerce.send_forget');
            Route::post("reset-pass", [ResetPassword::class, 'resetPassword'])->name('ecommerce.reset_pass');
        });
    });

    Route::prefix('account')->middleware('customers_must_login')->group(function () {

        Route::get("dashboard", [AccountController::class, 'index'])->name('ecommerce.dashboard');
        Route::get("setting-address", [AccountController::class, 'address'])->name('ecommerce.address');
        Route::get("logout", [AuthController::class, 'logout'])->name('ecommmerce.logout');


        Route::prefix('verify-email')->group(function () {
            Route::get("/", [RegisterController::class, 'verify'])->name('ecommerce.verify');
            Route::post("resend", [RegisterController::class, 'reSendEmailVerify'])->name('ecommerce.resend');
            Route::post("verify", [RegisterController::class, 'emailVerify'])->name('ecommerce.verifymail');
        });

        Route::middleware('customers_must_verify')->group(function () {

            Route::prefix('my-profile')->group(function () {
                Route::get("/", [AccountController::class, 'profile'])->name('ecommerce.profile');
                Route::post("change", [AccountController::class, 'changeProfile'])->name('ecommerce.change_profile');
                Route::post("change-password", [AccountController::class, 'changePassword'])->name('ecommerce.change_password');
            });


            Route::prefix('orders')->group(function () {
                Route::get("/", [OrderController::class, 'index'])->name("ecommerce.orders");
                Route::get("detail/{id}", [OrderController::class, 'detail']);
                Route::post("pay-transaction/{id}", [MidtransController::class, 'reGenerateByTransaction']);
                Route::post("get-tracking/{id}", [OrderController::class, 'tracking']);
                Route::post("confirmation/{id}", [OrderController::class, 'received']);
                Route::post('add-payment/{transaction}', [OrderController::class, 'addPayment']);
            });

            Route::prefix('cart')->group(function () {
                Route::post("add", [CartController::class, 'add']);
                Route::post("update/{cart}", [CartController::class, 'update']);
                Route::delete("delete/{cart}", [CartController::class, 'delete']);
                Route::delete("delete-all", [CartController::class, 'deleteAll']);
            });



            Route::prefix('address')->group(function () {
                Route::get("/", [AddressController::class, 'index']);
                Route::get("detail/{address}", [AddressController::class, 'detail']);
                Route::post("store", [AddressController::class, 'store']);
                Route::post("update/{address}", [AddressController::class, 'update']);
                Route::delete("delete/{address}", [AddressController::class, 'delete']);
            });
        });
    });
});
