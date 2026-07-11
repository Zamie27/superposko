<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminSubscriptionController extends Controller
{
    /**
     * Display subscription listing.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $subscriptions = User::query()
            ->whereNull('host_id') // Only show posko owners
            ->where('role', '!=', 'admin')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('university', 'like', "%{$search}%");
                });
            })
            ->orderBy('subscription_expires_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/Subscriptions', [
            'subscriptions' => $subscriptions,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Bypass payment to grant host subscription access.
     */
    public function bypass(Request $request, User $user): JsonResponse
    {
        // Only set role to ketua if user doesn't already have an organizational role
        $updateData = [
            'subscription_expires_at' => now()->addDays(40),
        ];
        if (in_array($user->role, ['user', 'trial'])) {
            $updateData['role'] = 'ketua';
        }
        $user->update($updateData);

        ActivityLogHelper::log(
            'payment',
            'bypass_payment',
            "Admin bypassed payment for user {$user->name} ({$user->email}), granting 40 days host access."
        );

        return response()->json([
            'success' => true,
            'message' => "Hak akses langganan posko berhasil diberikan (Bypass Payment) ke user {$user->name} untuk 40 hari.",
        ]);
    }

    /**
     * Update subscription duration / expires_at.
     */
    public function updateDuration(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'expires_at' => ['required', 'date'],
        ]);

        $updateData = [
            'subscription_expires_at' => $validated['expires_at'],
        ];
        // Only set role to ketua if user doesn't already have an organizational role
        if (in_array($user->role, ['user', 'trial'])) {
            $updateData['role'] = 'ketua';
        }
        $user->update($updateData);

        ActivityLogHelper::log(
            'payment',
            'update_subscription_duration',
            "Admin updated subscription duration for user {$user->name} ({$user->email}) to {$validated['expires_at']}."
        );

        return response()->json([
            'success' => true,
            'message' => "Masa aktif langganan user {$user->name} berhasil diperbarui hingga {$validated['expires_at']}.",
        ]);
    }

    /**
     * Revoke host subscription access.
     */
    public function revoke(User $user): JsonResponse
    {
        $user->update([
            'role' => 'user',
            'subscription_expires_at' => null, // set to null
        ]);

        ActivityLogHelper::log(
            'payment',
            'revoke_subscription',
            "Admin revoked subscription access from user {$user->name} ({$user->email})."
        );

        return response()->json([
            'success' => true,
            'message' => "Akses langganan user {$user->name} berhasil dicabut.",
        ]);
    }
}
