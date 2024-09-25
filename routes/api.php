<?php

use App\Http\Controllers\api\AddressController;
use App\Http\Controllers\api\PackageController;
use App\Http\Controllers\api\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => 'api'], function ($router) {

    Route::apiResources([
        'addresses' => AddressController::class,
        'packages' => PackageController::class,   
        'shipments' => ShipmentController::class, 
    ]);

    Route::get("/email/resend", "api\VerifyEmailController@resend")->name('verification.resend');
    Route::post("/forget-password", "api\ResetPasswordController@sendResetLink")->name("password.send-link");
    Route::put("/forget-password/{token}", "api\ResetPasswordController@resetPassword")->name("password.reset");

    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', 'api\AuthController@login');
        Route::post('register', 'api\AuthController@register');
        Route::post('register-business', 'api\AuthController@registerBusiness');
        Route::post('logout', 'api\AuthController@logout')->middleware('auth:api');
        Route::post('refresh', 'api\AuthController@refresh')->middleware('auth:api');
        Route::get('user-profile', 'api\AuthController@userProfile');   
    });

});


