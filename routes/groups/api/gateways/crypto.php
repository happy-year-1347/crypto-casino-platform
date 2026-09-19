<?php

use App\Http\Controllers\Api\CryptoController;
use Illuminate\Support\Facades\Route;

Route::prefix('crypto')->group(function () {
    Route::get('/currencies', [CryptoController::class, 'getCurrencies'])->name('crypto.currencies');
    Route::get('/estimate', [CryptoController::class, 'estimate'])->middleware('throttle:60,1')->name('crypto.estimate');

    Route::middleware('auth.jwt')->group(function () {
        Route::post('/payment', [CryptoController::class, 'createPayment'])->middleware('throttle:10,1')->name('crypto.payment');
        Route::get('/status/{paymentId}', [CryptoController::class, 'checkStatus'])->middleware('throttle:30,1')->name('crypto.status');
    });

    // called by NOWPayments, verified with the IPN secret
    Route::post('/webhook', [CryptoController::class, 'webhook'])->name('crypto.webhook');
    Route::post('/payout/webhook', [CryptoController::class, 'payoutWebhook'])->name('crypto.payout.webhook');
});
