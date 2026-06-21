<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActivityLogHelper
{
    /**
     * Log a system or user activity.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|\App\Models\User|null  $user
     */
    public static function log(string $category, string $action, string $description, $user = null): void
    {
        /** @var \App\Models\User|null $targetUser */
        $targetUser = $user ?? Auth::user();

        ActivityLog::create([
            'user_id' => $targetUser ? $targetUser->id : null,
            'user_name' => $targetUser ? $targetUser->name : 'System/Guest',
            'user_email' => $targetUser ? $targetUser->email : null,
            'role' => $targetUser ? $targetUser->role : 'guest',
            'category' => $category,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
