<?php
 
use Illuminate\Support\Facades\Route;
use Poshub\Ecommerce\Controllers\AccountController as ControllersAccountController;
use Poshub\Ecommerce\Controllers\AddressController as ControllersAddressController;
use Poshub\Ecommerce\Controllers\AuthController;
use Poshub\Ecommerce\Controllers\HomeController as ControllersHomeController;
use Poshub\Ecommerce\Controllers\MidtransController;
use Poshub\Ecommerce\Controllers\Mobile\AccountController;
use Poshub\Ecommerce\Controllers\Mobile\AddressController;
use Poshub\Ecommerce\Controllers\Mobile\AuthenticationController;
use Poshub\Ecommerce\Controllers\Mobile\CartController;
use Poshub\Ecommerce\Controllers\Mobile\CheckOutController;
use Poshub\Ecommerce\Controllers\Mobile\HomeController;
use Poshub\Ecommerce\Controllers\Mobile\OrderController;
use Poshub\Ecommerce\Controllers\Mobile\ShopController;
use Poshub\Ecommerce\Controllers\OrderController as ControllersOrderController;
use Poshub\Ecommerce\Controllers\RegisterController;
use Poshub\Ecommerce\Controllers\ResetPassword;

Route::prefix('m-ecommerce')->middleware(['device_phone_commerce', 'ecommerce', 'web', 'domain_identification'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('ecommerce.mobile.home');
    Route::get("change-session/{id}", [ControllersHomeController::class, 'changeSession']);
    Route::prefix('auth')->middleware('customers_not_login')->group(function () {
        Route::get("login", [AuthenticationController::class, 'login'])->name('ecommerce.mobile.login');
        Route::get("register", [AuthenticationController::class, 'register'])->name('ecommerce.mobile.register');
        Route::post('signin', [AuthController::class, 'login'])->name('ecommerce.mobile.signin');
        Route::post("signup", [RegisterController::class, 'signup'])->name('ecommerce.mobile.signup');

        Route::prefix('forget-password')->group(function () {
            Route::get("/", [AuthenticationController::class, 'forgetPass'])->name('ecommerce.mobile.forget');
            Route::get("reset-password", [AuthenticationController::class, 'resetPass'])->name('ecommerce.mobile.reset');

            Route::post("send-ask", [ResetPassword::class, 'forgetPassword'])->name('ecommerce.mobile.send_forget');
            Route::post("reset-pass", [ResetPassword::class, 'resetPassword'])->name('ecommerce.mobile.reset_pass');
        });
    });

    Route::prefix('account')->middleware('customers_must_login')->group(function () {

        Route::get("dashboard", [AccountController::class, 'index'])->name('ecommerce.mobile.dashboard');


        Route::get("setting-address", [AccountController::class, 'address'])->name('ecommerce.mobile.address');
        Route::get("logout", [AuthController::class, 'logout'])->name('ecommmerce.mobile.logout');


        Route::prefix('verify-email')->group(function () {
            Route::get("/", [AuthenticationController::class, 'verify'])->name('ecommerce.mobile.verify');
            Route::post("resend", [RegisterController::class, 'reSendEmailVerify'])->name('ecommerce.mobile.resend');
            Route::post("verify", [RegisterController::class, 'emailVerify'])->name('ecommerce.mobile.verifymail');
        });

        Route::middleware('customers_must_verify')->group(function () {

            Route::get('change-profile', [AccountController::class, 'changeProfile'])->name('ecommerce.mobile.profile');
            Route::get('change-password', [AccountController::class, 'changePassword'])->name('ecommerce.mobile.password');

            Route::prefix('my-profile')->group(function () { 
                Route::post("change", [ControllersAccountController::class, 'changeProfile'])->name('ecommerce.mobile.change_profile');
                Route::post("change-password", [ControllersAccountController::class, 'changePassword'])->name('ecommerce.mobile.change_password');
            });


            Route::prefix('orders')->group(function () {
                Route::get("/{status}", [OrderController::class, 'index'])->name("ecommerce.mobile.orders");
                Route::get("detail/{transaction}", [OrderController::class, 'detail'])->name('ecommerce.mobile.order_detail');
                Route::post("pay-transaction/{id}", [MidtransController::class, 'reGenerateByTransaction']);
                Route::post("get-tracking/{id}", [ControllersOrderController::class, 'tracking']);
                Route::post("confirmation/{id}", [ControllersOrderController::class, 'received']);
                Route::post('add-payment/{transaction}', [ControllersOrderController::class, 'addPayment']);
            });

            Route::prefix('cart')->group(function () {
                Route::post("add", [CartController::class, 'add']);
                Route::post("update/{cart}", [CartController::class, 'update']);
                Route::delete("delete/{cart}", [CartController::class, 'delete']);
                Route::delete("delete-all", [CartController::class, 'deleteAll']);
            });



            Route::prefix('address')->group(function () {
                Route::get("/", [AddressController::class, 'index'])->name('ecommerce.mobile.address.index');
                Route::get('create', [AddressController::class, 'create'])->name('ecommerce.mobile.address.create');
                Route::get("detail/{address}", [AddressController::class, 'update'])->name('ecommerce.mobile.address.update');
                Route::post("store", [ControllersAddressController::class, 'store']);
                Route::post("update/{address}", [ControllersAddressController::class, 'update']);
                Route::get("delete/{address}", [AddressController::class, 'delete'])->name('ecommerce.mobile.address.delete');
            });
        });
    });

    Route::prefix('shop')->group(function () {
        Route::get('/', [ShopController::class, 'index'])->name('ecommerce.mobile.shop');
        Route::get('detail/{product}', [ShopController::class, 'detail'])->name('ecommerce.mobile.shop_detail');
        Route::get("variation-detail/{id}", [ShopController::class, 'getDetailVariation']);

        Route::prefix('checkout')->middleware('customers_must_verify')->group(function () {
            Route::get("/", [CheckOutController::class, 'index'])->name('ecommerce.mobile.checkout');
            Route::post("by-check", [CheckOutController::class, 'index'])->name('ecommerce.mobile.checkout_checked');
            Route::get("get-shipping-cost", [CheckOutController::class, 'getShippingCost']);
            Route::get("cart", [CartController::class, 'cart'])->name('ecommerce.mobile.cart');
            Route::post('transactions', [MidtransController::class, 'create']);
        });
    });
});
