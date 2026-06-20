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

    // Sidebar Menus
    Route::inertia('finance', 'finance/Index')->name('finance.index');
    Route::inertia('logbook', 'logbook/Index')->name('logbook.index');
    Route::inertia('management/inventory', 'management/inventory/Index')->name('management.inventory.index');
    Route::inertia('management/logistic', 'management/logistic/Index')->name('management.logistic.index');
    Route::inertia('management/schedule', 'management/schedule/Index')->name('management.schedule.index');
    
    Route::get('management/members', [\App\Http\Controllers\MemberController::class, 'index'])->name('management.members.index');
    Route::post('management/members', [\App\Http\Controllers\MemberController::class, 'store'])->name('management.members.store');
    Route::put('management/members/{member}', [\App\Http\Controllers\MemberController::class, 'update'])->name('management.members.update');
    Route::delete('management/members/{member}', [\App\Http\Controllers\MemberController::class, 'destroy'])->name('management.members.destroy');
    
    Route::inertia('contacts', 'contacts/Index')->name('contacts.index');
    Route::inertia('repository', 'repository/Index')->name('repository.index');
    Route::inertia('voting', 'voting/Index')->name('voting.index');
    Route::get('documentation', [\App\Http\Controllers\DocumentationController::class, 'index'])->name('documentation.index');
    Route::post('documentation/upload', [\App\Http\Controllers\DocumentationController::class, 'store'])->name('documentation.upload');
    Route::get('documentation/thumbnail/{id}', [\App\Http\Controllers\DocumentationController::class, 'thumbnail'])->name('documentation.thumbnail');
    Route::get('documentation/file/{id}', [\App\Http\Controllers\DocumentationController::class, 'file'])->name('documentation.file');
});

require __DIR__.'/settings.php';
