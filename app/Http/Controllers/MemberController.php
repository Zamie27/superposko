<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Helpers\HostRoleHelper;
use App\Helpers\RoleConfig;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $pendingDpls = \App\Models\DplMonitoring::with('dpl:id,name,email')
            ->where('host_id', $hostId)
            ->where('status', 'pending')
            ->get();

        $activeDpls = \App\Models\DplMonitoring::with('dpl:id,name,email')
            ->where('host_id', $hostId)
            ->where('status', 'approved')
            ->get();

        return Inertia::render('management/members/Index', [
            'members' => $members,
            'pendingDpls' => $pendingDpls,
            'activeDpls' => $activeDpls,
            'isHost' => HostRoleHelper::canManageMembers($user),
            'availableRoles' => RoleConfig::getAvailableRoles($hostId),
            'currentUserRole' => $user->role,
        ]);
    }

    /**
     * Store a newly created member in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! HostRoleHelper::canManageMembers($user)) {
            abort(403, 'Hanya Ketua, Wakil, dan Sekretaris yang dapat menambah anggota.');
        }

        $role = $request->input('role');
        $email = $request->input('email');
        $hostId = $user->host_id ?? $user->id;

        if ($role === 'dpl') {
            $existingUser = User::where('email', $email)->first();
            if ($existingUser) {
                if ($existingUser->role === 'dpl') {
                    \App\Models\DplMonitoring::updateOrCreate([
                        'dpl_id' => $existingUser->id,
                        'host_id' => $hostId,
                    ], [
                        'status' => 'approved',
                    ]);

                    ActivityLogHelper::log(
                        'member',
                        'add_dpl_exist',
                        "User added existing DPL {$existingUser->name} ({$email}) to monitor this posko."
                    );

                    return back()->with('success', 'DPL dengan email tersebut sudah terdaftar di sistem. Kelompok Anda otomatis terhubung dan dapat dipantau oleh DPL ini.');
                } else {
                    return back()->withErrors(['email' => 'Email sudah digunakan oleh pengguna lain dengan peran non-DPL.']);
                }
            }
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(RoleConfig::getRoleKeys())],
        ], [
            'email.unique' => 'Email Sudah Digunakan',
            'email.email' => 'Email Tidak Sesuai',
            'password.min' => 'Password Tidak Memenuhi Syarat',
        ]);

        $host = User::findOrFail($hostId);

        // Enforce role capacity (skip for 'anggota' and 'dpl' which are unlimited)
        $capacity = RoleConfig::getRoleCapacity($validated['role']);
        if ($capacity > 0) {
            $currentCount = User::where(function ($q) use ($hostId) {
                $q->where('host_id', $hostId)->orWhere('id', $hostId);
            })->where('role', $validated['role'])->count();

            if ($currentCount >= $capacity) {
                return back()->withErrors([
                    'role' => 'Slot untuk peran ' . RoleConfig::getRoleLabel($validated['role']) . ' sudah penuh (' . $capacity . ' maks).',
                ]);
            }
        }

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'host_id' => $validated['role'] === 'dpl' ? null : $hostId,
            'university' => $host->university,
            'group_number' => $host->group_number,
            'kkn_address' => $host->kkn_address,
        ]);

        if ($validated['role'] === 'dpl') {
            \App\Models\DplMonitoring::create([
                'dpl_id' => $newUser->id,
                'host_id' => $hostId,
                'status' => 'approved',
            ]);
        }

        ActivityLogHelper::log(
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
            'role' => ['required', 'string', Rule::in(RoleConfig::getRoleKeys())],
        ]);

        // Enforce role capacity if role is changing
        if ($validated['role'] !== $member->role) {
            $capacity = RoleConfig::getRoleCapacity($validated['role']);
            if ($capacity > 0) {
                $currentCount = User::where(function ($q) use ($hostId) {
                    $q->where('host_id', $hostId)->orWhere('id', $hostId);
                })->where('role', $validated['role'])->count();

                if ($currentCount >= $capacity) {
                    return back()->withErrors([
                        'role' => 'Slot untuk peran ' . RoleConfig::getRoleLabel($validated['role']) . ' sudah penuh (' . $capacity . ' maks).',
                    ]);
                }
            }
        }

        $member->name = $validated['name'];
        $member->email = $validated['email'];
        $member->role = $validated['role'];

        if (! empty($validated['password'])) {
            $member->password = Hash::make($validated['password']);
        }

        $member->save();

        ActivityLogHelper::log(
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

        ActivityLogHelper::log(
            'member',
            'delete_member',
            "User deleted member {$member->name} ({$member->email})."
        );

        return back()->with('success', 'Anggota berhasil dihapus.');
    }

    /**
     * Transfer Ketua role and posko ownership to another member.
     * This also transfers billing/subscription ownership.
     */
    public function transferKetua(Request $request): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        // Only the current Owner can transfer ownership
        if (! HostRoleHelper::isOwner($user)) {
            abort(403, 'Hanya Owner posko yang dapat mentransfer jabatan Ketua.');
        }

        $validated = $request->validate([
            'target_member_id' => ['required', 'integer', 'exists:users,id'],
            'new_self_role' => ['required', 'string', Rule::in(RoleConfig::getRoleKeys())],
        ]);

        $targetMember = User::findOrFail($validated['target_member_id']);

        // Target must be a member of this posko
        if ($targetMember->host_id !== $hostId) {
            abort(403, 'Anggota target bukan anggota posko ini.');
        }

        // Validate new_self_role capacity (exclude both users from count since both are changing)
        $newSelfRole = $validated['new_self_role'];
        if ($newSelfRole !== 'ketua') {
            $capacity = RoleConfig::getRoleCapacity($newSelfRole);
            if ($capacity > 0) {
                $currentCount = User::where(function ($q) use ($hostId) {
                    $q->where('host_id', $hostId)->orWhere('id', $hostId);
                })
                    ->where('role', $newSelfRole)
                    ->where('id', '!=', $user->id)
                    ->where('id', '!=', $targetMember->id)
                    ->count();

                if ($currentCount >= $capacity) {
                    return back()->withErrors([
                        'new_self_role' => 'Slot untuk peran ' . RoleConfig::getRoleLabel($newSelfRole) . ' sudah penuh.',
                    ]);
                }
            }
        }

        DB::transaction(function () use ($user, $targetMember, $newSelfRole) {
            $oldOwnerId = $user->id;
            $newOwnerId = $targetMember->id;

            // Transfer subscription data from old owner to new owner
            $targetMember->subscription_expires_at = $user->subscription_expires_at;
            $targetMember->trial_ends_at = $user->trial_ends_at;

            // Set new owner: remove host_id, set role to ketua
            $targetMember->host_id = null;
            $targetMember->role = 'ketua';
            $targetMember->save();

            // Update all other members to point to the new owner
            User::where('host_id', $oldOwnerId)
                ->where('id', '!=', $newOwnerId)
                ->update(['host_id' => $newOwnerId]);

            // Old owner becomes a member of the new owner
            $user->host_id = $newOwnerId;
            $user->role = $newSelfRole;
            $user->subscription_expires_at = null;
            $user->trial_ends_at = null;
            $user->save();
        });

        ActivityLogHelper::log(
            'member',
            'transfer_ketua',
            "User transferred Ketua ownership to {$targetMember->name} ({$targetMember->email}) and changed own role to {$newSelfRole}."
        );

        return redirect('/management/members')->with('success', 'Jabatan Ketua dan kepemilikan posko berhasil ditransfer.');
    }

    /**
     * Store multiple members in batch.
     */
    public function storeBatch(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! HostRoleHelper::canManageMembers($user)) {
            abort(403, 'Hanya Ketua, Wakil, dan Sekretaris yang dapat menambah anggota.');
        }

        $validated = $request->validate([
            'members' => ['required', 'array', 'min:1'],
            'members.*.name' => ['required', 'string', 'max:255'],
            'members.*.email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'members.*.role' => ['required', 'string', Rule::in(RoleConfig::getRoleKeys())],
        ], [
            'members.*.email.unique' => 'Email :input sudah digunakan oleh pengguna lain.',
            'members.*.email.email' => 'Format email :input tidak valid.',
            'members.*.name.required' => 'Nama wajib diisi.',
            'members.*.email.required' => 'Email wajib diisi.',
        ]);

        $hostId = $user->host_id ?? $user->id;
        $host = User::findOrFail($hostId);

        $rawMembers = $request->input('members', []);
        $newMembersToValidate = [];
        $existingDplsToConnect = [];

        foreach ($rawMembers as $index => $m) {
            if (isset($m['role'], $m['email']) && $m['role'] === 'dpl') {
                $existingUser = User::where('email', $m['email'])->first();
                if ($existingUser) {
                    if ($existingUser->role === 'dpl') {
                        $existingDplsToConnect[] = [
                            'dpl_id' => $existingUser->id,
                            'name' => $existingUser->name,
                            'email' => $existingUser->email,
                        ];
                        continue;
                    } else {
                        return back()->withErrors([
                            "members.{$index}.email" => "Email {$m['email']} sudah digunakan oleh pengguna non-DPL."
                        ]);
                    }
                }
            }
            $newMembersToValidate[] = $m;
        }

        // Validate the remaining new members
        if (count($newMembersToValidate) > 0) {
            $validator = \Illuminate\Support\Facades\Validator::make([
                'members' => $newMembersToValidate
            ], [
                'members' => ['required', 'array', 'min:1'],
                'members.*.name' => ['required', 'string', 'max:255'],
                'members.*.email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'members.*.role' => ['required', 'string', Rule::in(RoleConfig::getRoleKeys())],
            ], [
                'members.*.email.unique' => 'Email :input sudah digunakan oleh pengguna lain.',
                'members.*.email.email' => 'Format email :input tidak valid.',
                'members.*.name.required' => 'Nama wajib diisi.',
                'members.*.email.required' => 'Email wajib diisi.',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator);
            }

            $validatedNewMembers = $validator->validated()['members'];
        } else {
            $validatedNewMembers = [];
        }

        // Check capacity for roles that have limit
        $batchRoles = [];
        foreach ($validatedNewMembers as $m) {
            $batchRoles[$m['role']] = ($batchRoles[$m['role']] ?? 0) + 1;
        }

        foreach ($batchRoles as $role => $count) {
            $capacity = RoleConfig::getRoleCapacity($role);
            if ($capacity > 0) {
                $currentCount = User::where(function ($q) use ($hostId) {
                    $q->where('host_id', $hostId)->orWhere('id', $hostId);
                })->where('role', $role)->count();

                if (($currentCount + $count) > $capacity) {
                    return back()->withErrors([
                        'batch_error' => 'Slot untuk peran ' . RoleConfig::getRoleLabel($role) . ' tidak mencukupi. Tersedia: ' . ($capacity - $currentCount) . ' slot lagi.',
                    ]);
                }
            }
        }

        DB::transaction(function () use ($validatedNewMembers, $existingDplsToConnect, $hostId, $host) {
            // 1. Process existing DPLs
            foreach ($existingDplsToConnect as $dpl) {
                \App\Models\DplMonitoring::updateOrCreate([
                    'dpl_id' => $dpl['dpl_id'],
                    'host_id' => $hostId,
                ], [
                    'status' => 'approved',
                ]);

                ActivityLogHelper::log(
                    'member',
                    'add_dpl_exist_batch',
                    "User added existing DPL {$dpl['name']} ({$dpl['email']}) to monitor this posko in batch."
                );
            }

            // 2. Process new members
            foreach ($validatedNewMembers as $m) {
                $password = \Illuminate\Support\Str::random(10);

                $newUser = User::create([
                    'name' => $m['name'],
                    'email' => $m['email'],
                    'password' => Hash::make($password),
                    'role' => $m['role'],
                    'host_id' => $m['role'] === 'dpl' ? null : $hostId,
                    'university' => $host->university,
                    'group_number' => $host->group_number,
                    'kkn_address' => $host->kkn_address,
                ]);

                if ($m['role'] === 'dpl') {
                    \App\Models\DplMonitoring::create([
                        'dpl_id' => $newUser->id,
                        'host_id' => $hostId,
                        'status' => 'approved',
                    ]);
                }

                try {
                    \Illuminate\Support\Facades\Mail::to($m['email'])
                        ->send(new \App\Mail\MemberCreatedMail($m['name'], $m['email'], $password));
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error("Gagal mengirim email kredensial ke {$m['email']}: " . $e->getMessage());
                }

                ActivityLogHelper::log(
                    'member',
                    'add_member_batch',
                    "User added member {$m['name']} ({$m['email']}) with role {$m['role']} in batch mode."
                );
            }
        });

        return back()->with('success', 'Batch anggota/DPL berhasil ditambahkan.');
    }

    /**
     * Approve DPL monitoring request.
     */
    public function approveDpl(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        if (! HostRoleHelper::canManageMembers($user)) {
            abort(403);
        }

        $hostId = $user->host_id ?? $user->id;
        $monitoring = \App\Models\DplMonitoring::where('id', $id)
            ->where('host_id', $hostId)
            ->firstOrFail();

        $monitoring->update(['status' => 'approved']);

        ActivityLogHelper::log(
            'member',
            'approve_dpl',
            "User approved DPL monitoring request ID {$id}."
        );

        return back()->with('success', 'Permintaan pemantauan DPL berhasil disetujui.');
    }

    /**
     * Reject DPL monitoring request.
     */
    public function rejectDpl(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();
        if (! HostRoleHelper::canManageMembers($user)) {
            abort(403);
        }

        $hostId = $user->host_id ?? $user->id;
        $monitoring = \App\Models\DplMonitoring::where('id', $id)
            ->where('host_id', $hostId)
            ->firstOrFail();

        $monitoring->delete();

        ActivityLogHelper::log(
            'member',
            'reject_dpl',
            "User rejected DPL monitoring request ID {$id}."
        );

        return back()->with('success', 'Permintaan pemantauan DPL berhasil ditolak.');
    }
}
