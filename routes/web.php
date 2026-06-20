<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminPreorderController;
use App\Http\Controllers\Admin\AdminPriceController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminSubscriptionController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Preorder\UserPreorderController;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'packageName' => Setting::get('package_name', 'Paket Posko'),
        'packagePrice' => (int) Setting::get('package_price', 100000),
        'packageStrikePrice' => (int) Setting::get('package_strike_price', 150000),
        'packageDescription' => Setting::get('package_description', 'Dapatkan akses penuh ke seluruh modul platform untuk satu posko KKN tanpa batasan kuota user/anggota kelompok.'),
        'footerAbout' => Setting::get('footer_about', 'SuperPosko adalah platform kolaborasi kelompok KKN (Kuliah Kerja Nyata) berbasis web untuk menunjang keterbukaan informasi, kebersamaan, dan kerapian administrasi posko.'),
        'footerEmail' => Setting::get('footer_email', 'kuukok.id@gmail.com'),
        'footerPhone' => Setting::get('footer_phone', '+62 851-7173-9232'),
        'footerCopyright' => Setting::get('footer_copyright', 'Kuukok.id'),
    ]);
})->name('home');

Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('auth/google/complete', [GoogleLoginController::class, 'showCompleteProfile'])->name('auth.google.complete');
Route::post('auth/google/complete', [GoogleLoginController::class, 'completeProfile'])->name('auth.google.complete.store');

Route::middleware(['auth', 'verified'])->group(function () {
    // Redirection helper for main /dashboard entry path
    Route::get('dashboard', function (Request $request) {
        $user = $request->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('host.dashboard');
    })->name('dashboard');

    // User Group (restricted to role 'user' only via user.only middleware)
    Route::middleware(['user.only'])->prefix('user')->name('user.')->group(function () {
        // Preorder
        Route::get('preorder', [UserPreorderController::class, 'index'])->name('preorder.index');
        Route::post('preorder', [UserPreorderController::class, 'store'])->name('preorder.store');
    });

    // Host Group (under host.protect middleware to restrict user-role modifications)
    Route::middleware(['host.protect'])->prefix('host')->name('host.')->group(function () {
        Route::inertia('dashboard', 'Dashboard')->name('dashboard');

        // Test Payment
        Route::get('payment/test', [PaymentController::class, 'showTestPage'])->name('payment.test');
        Route::post('payment/test/token', [PaymentController::class, 'createSnapToken'])->name('payment.token');

        // Sidebar Menus
        Route::inertia('finance', 'finance/Index')->name('finance.index');
        Route::inertia('logbook', 'logbook/Index')->name('logbook.index');
        Route::inertia('management/inventory', 'management/inventory/Index')->name('management.inventory.index');
        Route::inertia('management/logistic', 'management/logistic/Index')->name('management.logistic.index');
        Route::inertia('management/schedule', 'management/schedule/Index')->name('management.schedule.index');

        Route::get('management/members', [MemberController::class, 'index'])->name('management.members.index');
        Route::post('management/members', [MemberController::class, 'store'])->name('management.members.store');
        Route::put('management/members/{member}', [MemberController::class, 'update'])->name('management.members.update');
        Route::delete('management/members/{member}', [MemberController::class, 'destroy'])->name('management.members.destroy');

        Route::inertia('contacts', 'contacts/Index')->name('contacts.index');
        Route::inertia('repository', 'repository/Index')->name('repository.index');
        Route::inertia('voting', 'voting/Index')->name('voting.index');
        Route::get('documentation', [DocumentationController::class, 'index'])->name('documentation.index');
        Route::post('documentation/upload', [DocumentationController::class, 'store'])->name('documentation.upload');
        Route::post('documentation/upload-chunk', [DocumentationController::class, 'uploadChunk'])->name('documentation.upload_chunk');
        Route::get('documentation/thumbnail/{id}', [DocumentationController::class, 'thumbnail'])->name('documentation.thumbnail');
        Route::get('documentation/file/{id}', [DocumentationController::class, 'file'])->name('documentation.file');
    });

    // Admin Group
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [AdminController::class, 'index'])->name('dashboard');

        // User Management
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('users/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset_password');
        Route::post('users/send-reset-email', [AdminUserController::class, 'sendResetEmail'])->name('users.send_reset_email');
        Route::put('users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.update_role');

        // Price Management
        Route::get('prices', [AdminPriceController::class, 'index'])->name('prices.index');
        Route::put('prices', [AdminPriceController::class, 'update'])->name('prices.update');

        // Subscription Management
        Route::get('subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::put('subscriptions/{user}/bypass', [AdminSubscriptionController::class, 'bypass'])->name('subscriptions.bypass');
        Route::put('subscriptions/{user}/duration', [AdminSubscriptionController::class, 'updateDuration'])->name('subscriptions.duration');
        Route::delete('subscriptions/{user}/revoke', [AdminSubscriptionController::class, 'revoke'])->name('subscriptions.revoke');

        // Preorder Management
        Route::get('preorders', [AdminPreorderController::class, 'index'])->name('preorders.index');
        Route::post('preorders/{preorder}/approve', [AdminPreorderController::class, 'approve'])->name('preorders.approve');
        Route::post('preorders/{preorder}/reject', [AdminPreorderController::class, 'reject'])->name('preorders.reject');

        // General Website Settings
        Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });
});

require __DIR__.'/settings.php';
