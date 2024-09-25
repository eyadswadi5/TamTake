<?php

use App\Http\Controllers\api\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/email/verify/{hash}', [VerifyEmailController::class, 'verify'])->name('verification.verify');
