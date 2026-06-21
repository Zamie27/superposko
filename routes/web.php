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
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProkerDocumentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LogisticController;
use App\Http\Controllers\LogbookController;
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

// Public/Auth Report Routes
Route::get('laporan/buat', [\App\Http\Controllers\ReportController::class, 'create'])->name('reports.create');
Route::post('laporan/buat', [\App\Http\Controllers\ReportController::class, 'store'])->name('reports.store');

// Midtrans Payment Webhook Notification
Route::match(['get', 'post'], 'payment/notification', [PaymentController::class, 'handleNotification'])->name('payment.notification');

Route::middleware(['auth', 'verified'])->group(function () {
    // Redirection/Rendering helper for main /dashboard entry path
    Route::get('dashboard', function (Request $request) {
        $user = $request->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('host.dashboard');
    })->name('dashboard');

    // User Group (restricted to role 'user' only via user.only middleware)
    Route::middleware(['user.only'])->group(function () {
        // Preorder
        Route::get('preorder', [UserPreorderController::class, 'index'])->name('preorder.index');
        Route::post('preorder', [UserPreorderController::class, 'store'])->name('preorder.store');

        // Beli Langganan (Subscription Checkout)
        Route::get('payment', [PaymentController::class, 'showCheckoutPage'])->name('payment.index');
        Route::post('payment/token', [PaymentController::class, 'createSnapToken'])->name('payment.token_user');
        Route::post('payment/success', [PaymentController::class, 'handlePaymentSuccess'])->name('payment.success');
    });

    // Host Group (under host.protect middleware to restrict user-role modifications)
    Route::middleware(['host.protect'])->group(function () {
        // Host Dashboard
        Route::get('host/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('host.dashboard');

        // Sidebar Menus
        Route::inertia('finance', 'finance/Index')->name('finance.index');
        
        // Logbook & Proker
        Route::get('logbook', [LogbookController::class, 'index'])->name('logbook.index');
        Route::post('logbook/proker', [LogbookController::class, 'storeProker'])->name('logbook.proker.store');
        Route::put('logbook/proker/{proker}', [LogbookController::class, 'updateProker'])->name('logbook.proker.update');
        Route::delete('logbook/proker/{proker}', [LogbookController::class, 'destroyProker'])->name('logbook.proker.destroy');
        Route::post('logbook/daily', [LogbookController::class, 'storeLogbook'])->name('logbook.daily.store');
        Route::put('logbook/daily/{logbook}', [LogbookController::class, 'updateLogbook'])->name('logbook.daily.update');
        Route::delete('logbook/daily/{logbook}', [LogbookController::class, 'destroyLogbook'])->name('logbook.daily.destroy');

        // Inventory Management
        Route::get('management/inventory', [InventoryController::class, 'index'])->name('management.inventory.index');
        Route::post('management/inventory', [InventoryController::class, 'store'])->name('management.inventory.store');
        Route::put('management/inventory/{inventory}', [InventoryController::class, 'update'])->name('management.inventory.update');
        Route::delete('management/inventory/{inventory}', [InventoryController::class, 'destroy'])->name('management.inventory.destroy');

        // Logistic Management
        Route::get('management/logistic', [LogisticController::class, 'index'])->name('management.logistic.index');
        Route::post('management/logistic', [LogisticController::class, 'store'])->name('management.logistic.store');
        Route::put('management/logistic/{logistic}', [LogisticController::class, 'update'])->name('management.logistic.update');
        Route::delete('management/logistic/{logistic}', [LogisticController::class, 'destroy'])->name('management.logistic.destroy');
        Route::post('management/logistic/checkout', [LogisticController::class, 'checkout'])->name('management.logistic.checkout');

        Route::get('management/schedule', [ScheduleController::class, 'index'])->name('management.schedule.index');
        Route::post('management/schedule/roster', [ScheduleController::class, 'storeRoster'])->name('management.schedule.roster.store');
        Route::delete('management/schedule/roster/{roster}', [ScheduleController::class, 'destroyRoster'])->name('management.schedule.roster.destroy');
        Route::post('management/schedule/event', [ScheduleController::class, 'storeEvent'])->name('management.schedule.event.store');
        Route::put('management/schedule/event/{event}', [ScheduleController::class, 'updateEvent'])->name('management.schedule.event.update');
        Route::delete('management/schedule/event/{event}', [ScheduleController::class, 'destroyEvent'])->name('management.schedule.event.destroy');

        Route::get('management/members', [MemberController::class, 'index'])->name('management.members.index');
        Route::post('management/members', [MemberController::class, 'store'])->name('management.members.store');
        Route::put('management/members/{member}', [MemberController::class, 'update'])->name('management.members.update');
        Route::delete('management/members/{member}', [MemberController::class, 'destroy'])->name('management.members.destroy');

        Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
        Route::post('contacts', [ContactController::class, 'store'])->name('contacts.store');
        Route::put('contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
        Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
        Route::get('repository', [ProkerDocumentController::class, 'index'])->name('repository.index');
        Route::post('repository', [ProkerDocumentController::class, 'store'])->name('repository.store');
        Route::delete('repository/{document}', [ProkerDocumentController::class, 'destroy'])->name('repository.destroy');
        Route::get('repository/{document}/download', [ProkerDocumentController::class, 'download'])->name('repository.download');
        Route::get('repository/{document}/view', [ProkerDocumentController::class, 'view'])->name('repository.view');
        Route::get('voting', [\App\Http\Controllers\VotingController::class, 'index'])->name('voting.index');
        Route::post('voting/poll', [\App\Http\Controllers\VotingController::class, 'storePoll'])->name('voting.poll.store');
        Route::post('voting/poll/{poll}/vote', [\App\Http\Controllers\VotingController::class, 'vote'])->name('voting.poll.vote');
        Route::delete('voting/poll/{poll}/vote', [\App\Http\Controllers\VotingController::class, 'cancelVote'])->name('voting.poll.vote.destroy');
        Route::delete('voting/poll/{poll}', [\App\Http\Controllers\VotingController::class, 'destroyPoll'])->name('voting.poll.destroy');
        Route::post('voting/aspiration', [\App\Http\Controllers\VotingController::class, 'storeAspiration'])->name('voting.aspiration.store');
        Route::post('voting/aspiration/{aspiration}/like', [\App\Http\Controllers\VotingController::class, 'likeAspiration'])->name('voting.aspiration.like');
        Route::put('voting/aspiration/{aspiration}/respond', [\App\Http\Controllers\VotingController::class, 'respondAspiration'])->name('voting.aspiration.respond');
        Route::delete('voting/aspiration/{aspiration}', [\App\Http\Controllers\VotingController::class, 'destroyAspiration'])->name('voting.aspiration.destroy');
        Route::get('documentation', [DocumentationController::class, 'index'])->name('host.documentation.index');
        Route::post('documentation/upload', [DocumentationController::class, 'store'])->name('host.documentation.upload');
        Route::post('documentation/upload-chunk', [DocumentationController::class, 'uploadChunk'])->name('host.documentation.upload_chunk');
        Route::get('documentation/thumbnail/{id}', [DocumentationController::class, 'thumbnail'])->name('host.documentation.thumbnail');
        Route::get('documentation/file/{id}', [DocumentationController::class, 'file'])->name('host.documentation.file');
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

        // Admin Documentation Configurations
        Route::get('documentation-configs', [\App\Http\Controllers\Admin\AdminDocumentationConfigController::class, 'index'])->name('documentation-configs.index');
        Route::put('documentation-configs/{host}', [\App\Http\Controllers\Admin\AdminDocumentationConfigController::class, 'update'])->name('documentation-configs.update');

        // Admin Activity Logs Panel
        Route::get('activity-logs', [\App\Http\Controllers\Admin\AdminActivityLogController::class, 'index'])->name('activity-logs.index');

        // Admin Report Panel Routes
        Route::get('reports', [\App\Http\Controllers\Admin\AdminReportController::class, 'index'])->name('reports.index');
        Route::put('reports/{report}/resolve', [\App\Http\Controllers\Admin\AdminReportController::class, 'resolve'])->name('reports.resolve');

        // Test Payment
        Route::get('payment/test', [\App\Http\Controllers\PaymentController::class, 'showTestPage'])->name('payment.test');
        Route::post('payment/test/token', [\App\Http\Controllers\PaymentController::class, 'createSnapToken'])->name('payment.token');
    });
});

require __DIR__.'/settings.php';
