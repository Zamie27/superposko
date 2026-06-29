<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class AdminTrialController extends Controller
{
    /**
     * Display trial listing.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $trials = User::query()
            ->whereIn('role', ['user', 'trial'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('university', 'like', "%{$search}%");
                });
            })
            ->orderByRaw("CASE WHEN role = 'trial' THEN 0 ELSE 1 END")
            ->orderBy('trial_ends_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/Trials', [
            'trials' => $trials,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Update trial settings for a user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'trial_days' => ['required', 'integer', 'min:1'],
        ]);

        $user->update([
            'role' => 'trial',
            'trial_ends_at' => Carbon::now()->addDays($validated['trial_days']),
        ]);

        ActivityLogHelper::log(
            'trial',
            'update_trial',
            "Admin updated trial duration for user {$user->name} ({$user->email}) to {$validated['trial_days']} days."
        );

        return response()->json([
            'success' => true,
            'message' => "Trial untuk user {$user->name} berhasil diperbarui/diaktifkan selama {$validated['trial_days']} hari.",
        ]);
    }

    /**
     * Revoke trial access.
     */
    public function revoke(User $user): JsonResponse
    {
        $user->update([
            'role' => 'user',
            'trial_ends_at' => null,
        ]);

        ActivityLogHelper::log(
            'trial',
            'revoke_trial',
            "Admin revoked trial access for user {$user->name} ({$user->email})."
        );

        return response()->json([
            'success' => true,
            'message' => "Akses trial untuk user {$user->name} berhasil dicabut.",
        ]);
    }
}
