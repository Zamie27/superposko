<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('university', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $hosts = User::whereNull('host_id')->where('role', '!=', 'admin')->select('id', 'name', 'email')->get();

        return Inertia::render('admin/Users', [
            'users' => $users,
            'hosts' => $hosts,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Direct reset password of a user.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        /** @var User $user */
        $user = User::findOrFail($request->input('user_id'));
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "Password untuk user {$user->name} berhasil diubah secara manual.",
        ]);
    }

    /**
     * Send password reset link to user email.
     */
    public function sendResetEmail(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        /** @var User $user */
        $user = User::findOrFail($request->input('user_id'));

        $status = Password::broker()->sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => "Link reset password telah dikirim ke email {$user->email}.",
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirimkan link reset password.',
        ], 400);
    }

    /**
     * Update user role and host_id.
     */
    public function updateRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(['admin', 'user', 'trial', 'ketua', 'wakil', 'sekretaris', 'bendahara', 'logistik', 'pdd', 'humas', 'acara', 'perlengkapan', 'anggota', 'dpl'])],
            'host_id' => ['nullable', 'exists:users,id'],
        ]);

        // Ensure host_id is null if role is not member
        if ($validated['role'] !== 'member') {
            $validated['host_id'] = null;
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Role user {$user->name} berhasil diubah menjadi {$validated['role']}.",
        ]);
    }

    /**
     * Update user trial settings.
     */
    public function updateTrial(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'trial_days' => ['required', 'integer', 'min:1'],
        ]);

        $user->update([
            'role' => 'trial',
            'trial_ends_at' => now()->addDays($validated['trial_days']),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Trial untuk user {$user->name} berhasil diaktifkan/diperbarui selama {$validated['trial_days']} hari.",
        ]);
    }

    /**
     * Ban the specified user.
     */
    public function ban(User $user): JsonResponse
    {
        $user->update([
            'banned_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "User {$user->name} berhasil di-ban.",
        ]);
    }

    /**
     * Unban the specified user.
     */
    public function unban(User $user): JsonResponse
    {
        $user->update([
            'banned_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Ban untuk user {$user->name} berhasil dibatalkan.",
        ]);
    }
}
