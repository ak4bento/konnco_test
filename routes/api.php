<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('api.auth.login');

Route::middleware(['auth:api', 'throttle:api'])->group(function () {
    Route::post('/transactions', [PaymentController::class, 'store']);
    Route::patch('/transactions/{id}', [PaymentController::class, 'processTransaction']);

    Route::get('/user/transactions', [PaymentController::class, 'index']);
    Route::get('/user/transactions/summary', [PaymentController::class, 'userTransactionSummary']);
    Route::get('/user/transactions/all-summary', [PaymentController::class, 'allTransactionSummary']);

});
