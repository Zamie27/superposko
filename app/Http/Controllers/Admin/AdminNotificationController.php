<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Mail\AdminAnnouncementMail;
use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class AdminNotificationController extends Controller
{
    /**
     * Display the notification center dashboard.
     */
    public function index(): Response
    {
        $totalUsers = User::where('role', '!=', 'admin')->count();
        $totalHosts = User::where('role', 'host')->count();
        $totalTrials = User::where('role', 'trial')->count();
        $totalRegularUsers = User::where('role', 'user')->count();

        $totalPushSubscriptions = PushSubscription::count();
        $activePushSubscriptions = PushSubscription::whereHas('user', function ($query) {
            $query->where('role', '!=', 'admin');
        })->count();

        return Inertia::render('admin/Notifications', [
            'stats' => [
                'totalUsers' => $totalUsers,
                'totalHosts' => $totalHosts,
                'totalTrials' => $totalTrials,
                'totalRegularUsers' => $totalRegularUsers,
                'totalPushSubscriptions' => $totalPushSubscriptions,
                'activePushSubscriptions' => $activePushSubscriptions,
            ],
            'vapidPublicKey' => config('services.vapid.public_key'),
        ]);
    }

    /**
     * Send Web Push Notifications to selected user role segment.
     */
    public function sendPush(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:500'],
            'url' => ['nullable', 'string'],
            'target' => ['required', 'string', 'in:all,host,trial,user'],
        ]);

        $target = $validated['target'];

        $subscriptions = PushSubscription::query()
            ->whereHas('user', function ($query) use ($target) {
                if ($target !== 'all') {
                    $query->where('role', $target);
                } else {
                    $query->where('role', '!=', 'admin');
                }
            })->get();

        if ($subscriptions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada browser terdaftar yang aktif untuk segmen ini.',
            ], 400);
        }

        // Configure WebPush client
        $auth = [
            'VAPID' => [
                'subject' => config('services.vapid.subject'),
                'publicKey' => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ];

        $webPush = new WebPush($auth);

        foreach ($subscriptions as $sub) {
            $payload = json_encode([
                'title' => $validated['title'],
                'body' => $validated['body'],
                'data' => [
                    'url' => $validated['url'] ?? '/',
                ],
            ]);

            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                ]),
                $payload !== false ? $payload : null
            );
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                $sentCount++;
            } else {
                $failedCount++;
                $endpoint = $report->getEndpoint();
                if ($report->isSubscriptionExpired()) {
                    PushSubscription::where('endpoint', $endpoint)->delete();
                }
            }
        }

        ActivityLogHelper::log(
            'notification',
            'send_push_notification',
            "Admin sent global push notification: '{$validated['title']}' to {$sentCount} endpoints (failed: {$failedCount})"
        );

        return response()->json([
            'success' => true,
            'message' => "Push Notification berhasil dikirim ke {$sentCount} browser (gagal/expired: {$failedCount}).",
        ]);
    }

    /**
     * Send Broadcast Emails to selected user role segment.
     */
    public function sendEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'target' => ['required', 'string', 'in:all,host,trial,user'],
        ]);

        $target = $validated['target'];

        $users = User::query()
            ->where('role', '!=', 'admin')
            ->when($target !== 'all', function ($query) use ($target) {
                $query->where('role', $target);
            })
            ->whereNotNull('email')
            ->get();

        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada email user terdaftar untuk segmen ini.',
            ], 400);
        }

        foreach ($users as $user) {
            Mail::to($user->email)->queue(new AdminAnnouncementMail(
                $validated['subject'],
                $validated['body']
            ));
        }

        ActivityLogHelper::log(
            'notification',
            'send_broadcast_email',
            "Admin queued announcement emails: '{$validated['subject']}' to {$users->count()} users."
        );

        return response()->json([
            'success' => true,
            'message' => "Email pengumuman berhasil masuk antrean pengiriman untuk {$users->count()} pengguna.",
        ]);
    }
}
