<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MemberController extends Controller
{
    /**
     * Display a listing of the members.
     */
    public function index(Request $request)
    {
        // Ensure only host can access member management directly (or host and roles with access)
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        $members = User::where('host_id', $hostId)->get();

        return Inertia::render('management/members/Index', [
            'members' => $members,
            'isHost' => is_null($user->host_id)
        ]);
    }

    /**
     * Store a newly created member in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (!is_null($user->host_id)) {
            abort(403, 'Hanya Host yang dapat menambah anggota.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(['bendahara', 'sekretaris', 'anggota'])],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'host_id' => $user->id,
            'university' => $user->university,
            'group_number' => $user->group_number,
            'kkn_address' => $user->kkn_address,
        ]);

        return back()->with('success', 'Anggota berhasil ditambahkan.');
    }

    /**
     * Update the specified member in storage.
     */
    public function update(Request $request, User $member)
    {
        $user = $request->user();
        if (!is_null($user->host_id) || $member->host_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengubah anggota ini.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($member->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'string', Rule::in(['bendahara', 'sekretaris', 'anggota'])],
        ]);

        $member->name = $validated['name'];
        $member->email = $validated['email'];
        $member->role = $validated['role'];
        
        if (!empty($validated['password'])) {
            $member->password = Hash::make($validated['password']);
        }

        $member->save();

        return back()->with('success', 'Anggota berhasil diperbarui.');
    }

    /**
     * Remove the specified member from storage.
     */
    public function destroy(Request $request, User $member)
    {
        $user = $request->user();
        if (!is_null($user->host_id) || $member->host_id !== $user->id) {
            abort(403, 'Anda tidak berhak menghapus anggota ini.');
        }

        $member->delete();

        return back()->with('success', 'Anggota berhasil dihapus.');
    }
}
