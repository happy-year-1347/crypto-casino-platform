<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api'], function () {

    /// 'auth' limiter: 10 tries a minute per IP and 5 per e-mail address
    Route::middleware('throttle:auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);

        Route::post('forget-password', [AuthController::class, 'submitForgetPassword']);
        Route::post('reset-password/{token}', [AuthController::class, 'submitResetPassword']);
    });

    Route::group(['middleware' => ['auth.jwt']], function () {
        Route::get('verify', [AuthController::class, 'verify']); // verifica se está autenticado
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('me', [AuthController::class, 'me']);
    });
});
