<?php

use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\PaymentController;

Route::inertia('/', 'Welcome')->name('home');

Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('auth/google/complete', [GoogleLoginController::class, 'showCompleteProfile'])->name('auth.google.complete');
Route::post('auth/google/complete', [GoogleLoginController::class, 'completeProfile'])->name('auth.google.complete.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::get('payment/test', [PaymentController::class, 'showTestPage'])->name('payment.test');
    Route::post('payment/test/token', [PaymentController::class, 'createSnapToken'])->name('payment.token');
});

require __DIR__.'/settings.php';
