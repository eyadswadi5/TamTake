<?php

use App\Http\Controllers\api\AddressController;
use App\Http\Controllers\api\PackageController;
use App\Http\Controllers\api\ShipmentController;
use App\Http\Controllers\api\VerifyEmailController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::apiResources([
    'addresses' => AddressController::class,
    'packages' => PackageController::class,   
    'shipments' => ShipmentController::class, 
]);

Route::group(['namespace' => 'App\Http\Controllers\api', 'middleware' => 'api'], function ($router) {

    Route::group(['prefix' => 'auth'], function () {
        Route::get('/email/resend', [VerifyEmailController::class, 'resend'])->name('verification.resend');
        Route::post('login', 'AuthController@login');
        Route::post('register', 'AuthController@register');
        Route::post('register-business', 'AuthController@registerBusiness');
        Route::post('logout', 'AuthController@logout')->middleware('auth:api');
        Route::post('refresh', 'AuthController@refresh')->middleware('auth:api');
        Route::get('user-profile', 'AuthController@userProfile');   
    });

});


