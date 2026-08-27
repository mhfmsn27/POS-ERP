<?php

use App\Http\Controllers\Api\Auth\BusinessRegisterController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use Illuminate\Support\Facades\Route;


Route::prefix('authentication')->group(function() {

    Route::post("login", [LoginController::class, 'login']);
    Route::post("register", [RegisterController::class, 'register']);

    Route::prefix('verify')->middleware('auth:sanctum')->group(function () {
        Route::post("re-send", [EmailVerificationController::class, 'resend']);
        Route::post("store", [EmailVerificationController::class, 'store']);
    });

    Route::prefix('forget-pass')->group(function () {
        Route::post("ask", [ForgetPasswordController::class, 'sendAsk']);
        Route::post("verify", [ForgetPasswordController::class, 'verifyTwoFactor']);
        Route::post("reset", [ForgetPasswordController::class, 'changePassword']);
    });

    Route::prefix('business-register')->middleware('auth:sanctum')->group(function() {
        Route::post('store',[BusinessRegisterController::class,'businessCreate']);
    });

});