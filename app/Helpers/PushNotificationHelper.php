<?php

namespace App\Helpers;

use App\Models\PushSubscription;
use App\Models\User;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushNotificationHelper
{
    /**
     * Send a web push notification to a list of users.
     */
    public static function sendToUsers(iterable $userIds, string $title, string $body, ?string $url = null): void
    {
        $subscriptions = PushSubscription::whereIn('user_id', $userIds)->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        // Configure VAPID keys
        $auth = [
            'VAPID' => [
                'subject' => config('services.vapid.subject'),
                'publicKey' => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ];

        // Ensure keys exist to prevent crashing if not configured
        if (empty($auth['VAPID']['publicKey']) || empty($auth['VAPID']['privateKey'])) {
            return;
        }

        try {
            $webPush = new WebPush($auth);

            foreach ($subscriptions as $sub) {
                $payload = json_encode([
                    'title' => $title,
                    'body' => $body,
                    'data' => [
                        'url' => $url ?? '/',
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

            foreach ($webPush->flush() as $report) {
                if (!$report->isSuccess()) {
                    $endpoint = $report->getEndpoint();
                    if ($report->isSubscriptionExpired()) {
                        PushSubscription::where('endpoint', $endpoint)->delete();
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim Web Push Notification: ' . $e->getMessage());
        }
    }

    /**
     * Send a web push notification to all members of a KKN group (including the host).
     */
    public static function sendToHostGroup(int $hostId, string $title, string $body, ?string $url = null, ?int $exceptUserId = null): void
    {
        // Fetch all users belonging to this host_id, or the host user itself
        $userIds = User::where(function ($query) use ($hostId) {
                $query->where('host_id', $hostId)
                      ->orWhere('id', $hostId);
            })
            ->when($exceptUserId, function ($query) use ($exceptUserId) {
                $query->where('id', '!=', $exceptUserId);
            })
            ->pluck('id');

        self::sendToUsers($userIds, $title, $body, $url);
    }
}
