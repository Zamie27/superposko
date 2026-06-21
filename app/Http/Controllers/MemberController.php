<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\HostRoleHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    /**
     * Display a listing of the members.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        $members = User::where('host_id', $hostId)->get();

        return Inertia::render('management/members/Index', [
            'members' => $members,
            'isHost' => HostRoleHelper::canManageMembers($user),
        ]);
    }

    /**
     * Store a newly created member in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! HostRoleHelper::canManageMembers($user)) {
            abort(403, 'Hanya Host dan Sekretaris yang dapat menambah anggota.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(['bendahara', 'sekretaris', 'anggota', 'pdd'])],
        ]);

        $hostId = $user->host_id ?? $user->id;
        $host = User::findOrFail($hostId);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'host_id' => $hostId,
            'university' => $host->university,
            'group_number' => $host->group_number,
            'kkn_address' => $host->kkn_address,
        ]);

        \App\Helpers\ActivityLogHelper::log(
            'member',
            'add_member',
            "User added member {$validated['name']} ({$validated['email']}) with role {$validated['role']}."
        );

        return back()->with('success', 'Anggota berhasil ditambahkan.');
    }

    /**
     * Update the specified member in storage.
     */
    public function update(Request $request, User $member): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if (! HostRoleHelper::canManageMembers($user) || $member->host_id !== $hostId) {
            abort(403, 'Anda tidak berhak mengubah anggota ini.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($member->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(['bendahara', 'sekretaris', 'anggota', 'pdd'])],
        ]);

        $member->name = $validated['name'];
        $member->email = $validated['email'];
        $member->role = $validated['role'];

        if (! empty($validated['password'])) {
            $member->password = Hash::make($validated['password']);
        }

        $member->save();

        \App\Helpers\ActivityLogHelper::log(
            'member',
            'update_member',
            "User updated member {$member->name} ({$member->email}) details to role {$member->role}."
        );

        return back()->with('success', 'Anggota berhasil diperbarui.');
    }

    /**
     * Remove the specified member from storage.
     */
    public function destroy(Request $request, User $member): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if (! HostRoleHelper::canManageMembers($user) || $member->host_id !== $hostId) {
            abort(403, 'Anda tidak berhak menghapus anggota ini.');
        }

        $member->delete();

        \App\Helpers\ActivityLogHelper::log(
            'member',
            'delete_member',
            "User deleted member {$member->name} ({$member->email})."
        );

        return back()->with('success', 'Anggota berhasil dihapus.');
    }
}
