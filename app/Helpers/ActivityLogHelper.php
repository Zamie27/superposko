<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActivityLogHelper
{
    /**
     * Log a system or user activity.
     */
    public static function log(string $category, string $action, string $description, ?User $user = null): void
    {
        // Fallback to currently authenticated user if no user is passed
        $targetUser = $user ?? Auth::user();

        ActivityLog::create([
            'user_id' => $targetUser?->id,
            'user_name' => $targetUser?->name ?? 'System/Guest',
            'user_email' => $targetUser?->email,
            'role' => $targetUser?->role ?? 'guest',
            'category' => $category,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
