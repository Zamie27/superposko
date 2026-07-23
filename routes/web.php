<?php

use App\Http\Controllers\Admin\AdminActivityLogController;
use App\Http\Controllers\Admin\AdminBugReportController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminDocumentationConfigController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminPreorderController;
use App\Http\Controllers\Admin\AdminPriceController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminSubscriptionController;
use App\Http\Controllers\Admin\AdminSubscriptionRequestController;
use App\Http\Controllers\Admin\AdminTrialController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\EmailVerificationOtpController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\BugReportController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomRoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\DplController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\FinancialAdministrationController;
use App\Http\Controllers\CashDueController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\LogisticController;
use App\Http\Controllers\MemberActivityLogController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsManagementController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Payment\SubscriptionRequestController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PersonalBelongingController;
use App\Http\Controllers\Preorder\UserPreorderController;
use App\Http\Controllers\ProkerDocumentController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\TripayController;
use App\Http\Controllers\VotingController;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'packageName' => Setting::get('package_name', 'Paket Posko'),
        'packagePrice' => (int) Setting::get('package_price', 100000),
        'packageStrikePrice' => (int) Setting::get('package_strike_price', 150000),
        'packageDescription' => Setting::get('package_description', 'Dapatkan akses penuh ke seluruh modul platform untuk satu posko KKN tanpa batasan kuota user/anggota kelompok.'),
        'preorderPrice' => (int) Setting::get('preorder_price', 50000),
        'preorderStrikePrice' => (int) Setting::get('preorder_strike_price', 100000),
        'preorderPromoActive' => filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN),
        'footerAbout' => Setting::get('footer_about', 'SuperPosko adalah platform kolaborasi kelompok KKN (Kuliah Kerja Nyata) berbasis web untuk menunjang keterbukaan informasi, kebersamaan, dan kerapian administrasi posko.'),
        'footerEmail' => Setting::get('footer_email', 'kuukok.id@gmail.com'),
        'footerPhone' => Setting::get('footer_phone', '+62 851-7173-9232'),
        'footerCopyright' => Setting::get('footer_copyright', 'Kuukok.id'),
    ]);
})->name('home');

Route::get('event', function () {
    return Inertia::render('event/Index', [
        'title' => Setting::get('event_title', 'SuperPosko Beta Bug Hunting & Feedback Event'),
        'description' => Setting::get('event_description', 'Event testing ini diadakan untuk menguji kesiapan, kesesuaian fitur, kestabilan kinerja, dan keamanan aplikasi SuperPosko sebelum peluncuran resmi. Mari berkontribusi untuk menciptakan aplikasi KKN terbaik!'),
        'youtubeEmbedUrl' => Setting::get('event_youtube_embed_url', 'https://www.youtube.com/embed/dQw4w9WgXcQ'),
        'prize' => Setting::get('event_prize', 'Rp 100.000'),
        'startDate' => Setting::get('event_start_date', '2026-06-23'),
        'endDate' => Setting::get('event_end_date', '2026-06-28'),
        'rules' => Setting::get('event_rules', "1. Peserta wajib mendaftar akun di SuperPosko.\n2. Peserta menguji fungsionalitas fitur (E-Bendahara, Piket, Logbook, dll.) dan melaporkan setiap bug yang ditemukan melalui bubble Lapor Bug.\n3. Bug yang valid dan belum pernah dilaporkan sebelumnya akan mendapatkan poin.\n4. Pemenang ditentukan berdasarkan jumlah temuan bug valid terbanyak.\n5. Keputusan juri bersifat mutlak."),
        'footerAbout' => Setting::get('footer_about', 'SuperPosko adalah platform kolaborasi kelompok KKN (Kuliah Kerja Nyata) berbasis web untuk menunjang keterbukaan informasi, kebersamaan, dan kerapian administrasi posko.'),
        'footerEmail' => Setting::get('footer_email', 'kuukok.id@gmail.com'),
        'footerPhone' => Setting::get('footer_phone', '+62 851-7173-9232'),
        'footerCopyright' => Setting::get('footer_copyright', 'Kuukok.id'),
    ]);
})->name('event.public');

Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('auth/google/complete', [GoogleLoginController::class, 'showCompleteProfile'])->name('auth.google.complete');
Route::post('auth/google/complete', [GoogleLoginController::class, 'completeProfile'])->name('auth.google.complete.store');

// Public/Auth Report Routes
Route::get('banned', function () {
    return Inertia::render('auth/Banned');
})->name('banned');

Route::get('panduan', [DocumentationController::class, 'showPublicDoc'])->name('documentation.public');

// Public News / Articles Routes
Route::get('berita', [NewsController::class, 'index'])->name('news.index');
Route::get('berita/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::post('berita/{slug}/track-cta', [NewsController::class, 'trackCta'])->name('news.track-cta');

Route::get('about', function () {
    return Inertia::render('static/About', [
        'footerAbout' => Setting::get('footer_about', 'SuperPosko adalah platform kolaborasi kelompok KKN (Kuliah Kerja Nyata) berbasis web untuk menunjang keterbukaan informasi, kebersamaan, dan kerapian administrasi posko.'),
        'footerEmail' => Setting::get('footer_email', 'kuukok.id@gmail.com'),
        'footerPhone' => Setting::get('footer_phone', '+62 851-7173-9232'),
        'footerCopyright' => Setting::get('footer_copyright', 'Kuukok.id'),
    ]);
})->name('static.about');

Route::get('privacy', function () {
    return Inertia::render('static/Privacy', [
        'footerAbout' => Setting::get('footer_about', 'SuperPosko adalah platform kolaborasi kelompok KKN (Kuliah Kerja Nyata) berbasis web untuk menunjang keterbukaan informasi, kebersamaan, dan kerapian administrasi posko.'),
        'footerEmail' => Setting::get('footer_email', 'kuukok.id@gmail.com'),
        'footerPhone' => Setting::get('footer_phone', '+62 851-7173-9232'),
        'footerCopyright' => Setting::get('footer_copyright', 'Kuukok.id'),
    ]);
})->name('static.privacy');

Route::get('terms', function () {
    return Inertia::render('static/Terms', [
        'footerAbout' => Setting::get('footer_about', 'SuperPosko adalah platform kolaborasi kelompok KKN (Kuliah Kerja Nyata) berbasis web untuk menunjang keterbukaan informasi, kebersamaan, dan kerapian administrasi posko.'),
        'footerEmail' => Setting::get('footer_email', 'kuukok.id@gmail.com'),
        'footerPhone' => Setting::get('footer_phone', '+62 851-7173-9232'),
        'footerCopyright' => Setting::get('footer_copyright', 'Kuukok.id'),
    ]);
})->name('static.terms');

Route::get('contact', function () {
    return Inertia::render('static/Contact', [
        'footerAbout' => Setting::get('footer_about', 'SuperPosko adalah platform kolaborasi kelompok KKN (Kuliah Kerja Nyata) berbasis web untuk menunjang keterbukaan informasi, kebersamaan, dan kerapian administrasi posko.'),
        'footerEmail' => Setting::get('footer_email', 'kuukok.id@gmail.com'),
        'footerPhone' => Setting::get('footer_phone', '+62 851-7173-9232'),
        'footerCopyright' => Setting::get('footer_copyright', 'Kuukok.id'),
        'socialInstagram' => Setting::get('social_instagram', 'superposko'),
    ]);
})->name('static.contact');

Route::get('laporan/buat', [ReportController::class, 'create'])->name('reports.create');
Route::post('laporan/buat', [ReportController::class, 'store'])->name('reports.store');
Route::post('bug-report', [BugReportController::class, 'store'])->name('bug-report.store');

// Tripay Payment Webhook Notification
Route::post('payment/tripay/callback', [TripayController::class, 'handleCallback'])->name('payment.tripay.callback');

Route::middleware(['auth'])->group(function () {
    // Media storage proxy for MinIO/S3 assets
    Route::get('/media/{path}', function (string $path) {
        $disk = env('FILESYSTEM_DISK', 's3');
        $cleanPath = ltrim($path, '/');

        try {
            $stream = Storage::disk($disk)->readStream($cleanPath);
            if ($stream) {
                $mimeType = null;
                try {
                    $mimeType = Storage::disk($disk)->mimeType($cleanPath);
                } catch (\Throwable $e) {
                    $mimeType = null;
                }

                if (! $mimeType) {
                    $ext = strtolower(pathinfo($cleanPath, PATHINFO_EXTENSION));
                    $mimeTypes = [
                        'jpg' => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                        'webp' => 'image/webp',
                        'svg' => 'image/svg+xml',
                        'pdf' => 'application/pdf',
                        'mp4' => 'video/mp4',
                    ];
                    $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
                }

                return response()->stream(function () use ($stream) {
                    fpassthru($stream);
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                }, 200, [
                    'Content-Type' => $mimeType,
                    'Cache-Control' => 'public, max-age=86400',
                    'Content-Disposition' => 'inline',
                ]);
            }
        } catch (\Throwable $e) {
            // Fallback to local public disk if configured locally
            try {
                if (Storage::disk('public')->exists($cleanPath)) {
                    return Storage::disk('public')->response($cleanPath);
                }
            } catch (\Throwable $ex) {
                // Ignore
            }
        }

        abort(404, 'File media tidak ditemukan.');
    })->where('path', '.*')->name('media.show');

    Route::post('email/verify-otp', [EmailVerificationOtpController::class, 'verify'])->name('verification.verify_otp');
    Route::post('email/resend-otp', [EmailVerificationOtpController::class, 'resend'])->name('verification.resend_otp');
    Route::post('logout-to-register', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('register');
    })->name('logout_to_register');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('dpl/switch-posko', [DplController::class, 'switchPosko'])->name('dpl.switch-posko');
    Route::post('dpl/request-monitoring', [DplController::class, 'requestMonitoring'])->name('dpl.request-monitoring');

    // Redirection/Rendering helper for main /dashboard entry path
    Route::get('dashboard', function (Request $request) {
        $user = $request->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('host.dashboard');
    })->name('dashboard');

    Route::get('payment/tripay/return', [TripayController::class, 'handleReturn'])->name('payment.tripay.return');
    Route::get('payment/tripay/status', [TripayController::class, 'checkStatus'])->name('payment.tripay.status');

    // User Group (restricted to role 'user' only via user.only middleware)
    Route::middleware(['user.only'])->group(function () {
        // Preorder
        Route::get('preorder', [UserPreorderController::class, 'index'])->name('preorder.index');
        Route::post('preorder', [UserPreorderController::class, 'store'])->name('preorder.store');

        // Beli Langganan (Subscription Checkout)
        Route::get('payment', [PaymentController::class, 'showCheckoutPage'])->name('payment.index');
        Route::post('payment/qris', [SubscriptionRequestController::class, 'store'])->name('payment.qris.store');

        // Tripay Payment Initation
        Route::post('payment/tripay/create', [TripayController::class, 'createPayment'])->name('payment.tripay.create');
        Route::post('payment/tripay/cancel', [TripayController::class, 'cancelCurrentPayment'])->name('payment.tripay.cancel');
    });

    // Host Group (under host.protect middleware to restrict user-role modifications)
    Route::middleware(['host.protect'])->group(function () {
        // Host Dashboard
        Route::get('host/dashboard', [DashboardController::class, 'index'])->name('host.dashboard');

        // Sidebar Menus
        // Struktur Organisasi
        Route::get('struktur', [StructureController::class, 'index'])->name('structure.index');

        // Absensi
        Route::get('absensi', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('absensi', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('absensi/scan-qr', [AttendanceController::class, 'scanQr'])->name('attendance.scan_qr');
        Route::post('absensi/scan-qr', [AttendanceController::class, 'storeScanQr'])->name('attendance.scan_qr.store');
        Route::get('absensi/qr-code-image', [AttendanceController::class, 'qrCodeImage'])->name('attendance.qr_code_image');
        Route::post('absensi/qr-poster/save-to-minio', [AttendanceController::class, 'savePosterToMinio'])->name('attendance.qr_poster.save');
        Route::get('absensi/qr-poster/download', [AttendanceController::class, 'downloadQrPoster'])->name('attendance.qr_poster.download');
        Route::post('absensi/settings', [AttendanceController::class, 'updateSettings'])->name('attendance.settings.update');

        // E-Bendahara (Kas & Keuangan)
        Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::post('finance', [FinanceController::class, 'store'])->name('finance.store');
        Route::post('finance/tags', [FinanceController::class, 'storeTag'])->name('finance.tags.store');
        Route::delete('finance/tags/{financeTag}', [FinanceController::class, 'destroyTag'])->name('finance.tags.destroy');
        Route::post('finance/{finance}', [FinanceController::class, 'update'])->name('finance.update');
        Route::delete('finance/{finance}', [FinanceController::class, 'destroy'])->name('finance.destroy');

        // Modul Administrasi Keuangan (7 Modul Baru)
        Route::get('financial-administration/buku-kas-umum', [FinancialAdministrationController::class, 'bukuKasUmum'])->name('financial-admin.buku-kas-umum');
        Route::get('financial-administration/buku-penerimaan-dana', [FinancialAdministrationController::class, 'bukuPenerimaan'])->name('financial-admin.buku-penerimaan');
        Route::get('financial-administration/buku-pengeluaran-dana', [FinancialAdministrationController::class, 'bukuPengeluaran'])->name('financial-admin.buku-pengeluaran');
        Route::get('financial-administration/bukti-pembayaran', [FinancialAdministrationController::class, 'buktiPembayaran'])->name('financial-admin.bukti-pembayaran');
        Route::get('financial-administration/kwitansi', [FinancialAdministrationController::class, 'kwitansi'])->name('financial-admin.kwitansi');
        Route::get('financial-administration/nota-belanja', [FinancialAdministrationController::class, 'notaBelanja'])->name('financial-admin.nota-belanja');
        Route::get('financial-administration/lpj-keuangan', [FinancialAdministrationController::class, 'lpjKeuangan'])->name('financial-admin.lpj-keuangan');

        // Pencatatan Kas Mingguan
        Route::get('catatan-kas', [CashDueController::class, 'index'])->name('cash-dues.index');
        Route::post('catatan-kas', [CashDueController::class, 'store'])->name('cash-dues.store');
        Route::post('catatan-kas/settings', [CashDueController::class, 'updateSettings'])->name('cash-dues.settings.update');
        Route::delete('catatan-kas/{cashDue}', [CashDueController::class, 'destroy'])->name('cash-dues.destroy');

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
        Route::post('management/logistic/barang-keluar', [LogisticController::class, 'barangKeluar'])->name('management.logistic.barang-keluar');

        // News / Article Management (Berita Posko KKN)
        Route::get('management/news', [NewsManagementController::class, 'index'])->name('news-management.index');
        Route::get('management/news/create', [NewsManagementController::class, 'create'])->name('news-management.create');
        Route::post('management/news', [NewsManagementController::class, 'store'])->name('news-management.store');
        Route::post('management/news/upload-image', [NewsManagementController::class, 'uploadImage'])->name('news-management.upload-image');
        Route::get('management/news/{article}/edit', [NewsManagementController::class, 'edit'])->name('news-management.edit');
        Route::post('management/news/{article}', [NewsManagementController::class, 'update'])->name('news-management.update');
        Route::delete('management/news/{article}', [NewsManagementController::class, 'destroy'])->name('news-management.destroy');

        // Personal Belongings Checklist (Barang Bawaan Pribadi KKN)
        Route::get('personal-belongings', [PersonalBelongingController::class, 'index'])->name('personal-belongings.index');
        Route::post('personal-belongings', [PersonalBelongingController::class, 'store'])->name('personal-belongings.store');
        Route::put('personal-belongings/{personalBelonging}', [PersonalBelongingController::class, 'update'])->name('personal-belongings.update');
        Route::delete('personal-belongings/{personalBelonging}', [PersonalBelongingController::class, 'destroy'])->name('personal-belongings.destroy');
        Route::post('personal-belongings/{personalBelonging}/toggle-packed', [PersonalBelongingController::class, 'togglePacked'])->name('personal-belongings.toggle-packed');

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
        Route::post('management/members/transfer-ketua', [MemberController::class, 'transferKetua'])->name('management.members.transfer-ketua');
        Route::post('management/members/batch', [MemberController::class, 'storeBatch'])->name('management.members.store-batch');
        Route::post('management/members/dpl-approve/{id}', [MemberController::class, 'approveDpl'])->name('management.members.dpl-approve');
        Route::post('management/members/dpl-reject/{id}', [MemberController::class, 'rejectDpl'])->name('management.members.dpl-reject');

        Route::get('management/custom-roles', [CustomRoleController::class, 'index'])->name('management.custom-roles.index');
        Route::post('management/custom-roles', [CustomRoleController::class, 'store'])->name('management.custom-roles.store');
        Route::put('management/custom-roles/{customRole}', [CustomRoleController::class, 'update'])->name('management.custom-roles.update');
        Route::delete('management/custom-roles/{customRole}', [CustomRoleController::class, 'destroy'])->name('management.custom-roles.destroy');

        // Tracking Log Aktifitas Anggota
        Route::get('management/activity-logs', [MemberActivityLogController::class, 'index'])->name('management.activity-logs.index');

        Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
        Route::post('contacts', [ContactController::class, 'store'])->name('contacts.store');
        Route::put('contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
        Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
        Route::get('repository', [ProkerDocumentController::class, 'index'])->name('repository.index');
        Route::post('repository', [ProkerDocumentController::class, 'store'])->name('repository.store');
        Route::delete('repository/{document}', [ProkerDocumentController::class, 'destroy'])->name('repository.destroy');
        Route::get('repository/{document}/download', [ProkerDocumentController::class, 'download'])->name('repository.download');
        Route::get('repository/{document}/view', [ProkerDocumentController::class, 'view'])->name('repository.view');
        Route::get('voting', [VotingController::class, 'index'])->name('voting.index');
        Route::post('voting/poll', [VotingController::class, 'storePoll'])->name('voting.poll.store');
        Route::post('voting/poll/{poll}/vote', [VotingController::class, 'vote'])->name('voting.poll.vote');
        Route::delete('voting/poll/{poll}/vote', [VotingController::class, 'cancelVote'])->name('voting.poll.vote.destroy');
        Route::delete('voting/poll/{poll}', [VotingController::class, 'destroyPoll'])->name('voting.poll.destroy');
        Route::post('voting/aspiration', [VotingController::class, 'storeAspiration'])->name('voting.aspiration.store');
        Route::post('voting/aspiration/{aspiration}/like', [VotingController::class, 'likeAspiration'])->name('voting.aspiration.like');
        Route::put('voting/aspiration/{aspiration}/respond', [VotingController::class, 'respondAspiration'])->name('voting.aspiration.respond');
        Route::delete('voting/aspiration/{aspiration}', [VotingController::class, 'destroyAspiration'])->name('voting.aspiration.destroy');
        Route::get('documentation', [DocumentationController::class, 'index'])->name('host.documentation.index');
        Route::post('documentation/upload', [DocumentationController::class, 'store'])->name('host.documentation.upload');
        Route::post('documentation/upload-chunk', [DocumentationController::class, 'uploadChunk'])->name('host.documentation.upload_chunk');
        Route::get('documentation/thumbnail/{id}', [DocumentationController::class, 'thumbnail'])->name('host.documentation.thumbnail');
        Route::get('documentation/file/{id}', [DocumentationController::class, 'file'])->name('host.documentation.file');

        // Web Push Subscriptions
        Route::post('push-subscriptions', [PushSubscriptionController::class, 'store'])->name('push_subscriptions.store');
        Route::delete('push-subscriptions', [PushSubscriptionController::class, 'destroy'])->name('push_subscriptions.destroy');
    });

    // Admin Group
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [AdminController::class, 'index'])->name('dashboard');

        // User Management
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
        Route::post('users/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset_password');
        Route::post('users/send-reset-email', [AdminUserController::class, 'sendResetEmail'])->name('users.send_reset_email');
        Route::put('users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.update_role');
        Route::put('users/{user}/trial', [AdminUserController::class, 'updateTrial'])->name('users.update_trial');
        Route::post('users/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
        Route::post('users/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');

        // Price Management
        Route::get('prices', [AdminPriceController::class, 'index'])->name('prices.index');
        Route::put('prices', [AdminPriceController::class, 'update'])->name('prices.update');
        Route::post('prices/qris', [AdminPriceController::class, 'updateQris'])->name('prices.qris.update');
        Route::delete('prices/qris', [AdminPriceController::class, 'deleteQris'])->name('prices.qris.delete');

        // Subscription Management
        Route::get('subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::put('subscriptions/{user}/bypass', [AdminSubscriptionController::class, 'bypass'])->name('subscriptions.bypass');
        Route::put('subscriptions/{user}/duration', [AdminSubscriptionController::class, 'updateDuration'])->name('subscriptions.duration');
        Route::delete('subscriptions/{user}/revoke', [AdminSubscriptionController::class, 'revoke'])->name('subscriptions.revoke');

        // Trial Management
        Route::get('trials', [AdminTrialController::class, 'index'])->name('trials.index');
        Route::put('trials/{user}', [AdminTrialController::class, 'update'])->name('trials.update');
        Route::delete('trials/{user}/revoke', [AdminTrialController::class, 'revoke'])->name('trials.revoke');

        // Preorder Management
        Route::get('preorders', [AdminPreorderController::class, 'index'])->name('preorders.index');
        Route::post('preorders/{preorder}/approve', [AdminPreorderController::class, 'approve'])->name('preorders.approve');
        Route::post('preorders/{preorder}/reject', [AdminPreorderController::class, 'reject'])->name('preorders.reject');

        // QRIS Subscription Request Management
        Route::get('subscription-requests', [AdminSubscriptionRequestController::class, 'index'])->name('subscription-requests.index');
        Route::post('subscription-requests/{subscriptionRequest}/approve', [AdminSubscriptionRequestController::class, 'approve'])->name('subscription-requests.approve');
        Route::post('subscription-requests/{subscriptionRequest}/reject', [AdminSubscriptionRequestController::class, 'reject'])->name('subscription-requests.reject');

        // Notification & Announcement Management
        Route::get('notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/send-push', [AdminNotificationController::class, 'sendPush'])->name('notifications.send_push');
        Route::post('notifications/send-email', [AdminNotificationController::class, 'sendEmail'])->name('notifications.send_email');

        // General Website Settings
        Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');

        // Admin Documentation Configurations
        Route::get('documentation-configs', [AdminDocumentationConfigController::class, 'index'])->name('documentation-configs.index');
        Route::put('documentation-configs/{host}', [AdminDocumentationConfigController::class, 'update'])->name('documentation-configs.update');

        // Admin Activity Logs Panel
        Route::get('activity-logs', [AdminActivityLogController::class, 'index'])->name('activity-logs.index');

        // Admin Report Panel Routes
        Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::put('reports/{report}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');

        // Admin Bug Report Management
        Route::get('bug-reports', [AdminBugReportController::class, 'index'])->name('bug-reports.index');
        Route::put('bug-reports/{bugReport}/resolve', [AdminBugReportController::class, 'resolve'])->name('bug-reports.resolve');

        // Test Payment Preview
        Route::get('payment/test', [PaymentController::class, 'showTestPage'])->name('payment.test');
    });
});

require __DIR__.'/settings.php';
