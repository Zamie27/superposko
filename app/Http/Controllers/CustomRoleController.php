<?php

namespace App\Http\Controllers;

use App\Helpers\HostRoleHelper;
use App\Models\CustomRole;
use Illuminate\Http\Request;

class CustomRoleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (! HostRoleHelper::canManageMembers($user)) {
            abort(403);
        }
        $hostId = $user->host_id ?? $user->id;

        return response()->json(CustomRole::where('host_id', $hostId)->get());
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (! HostRoleHelper::canManageMembers($user)) {
            abort(403);
        }
        $hostId = $user->host_id ?? $user->id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
        ]);

        $customRole = CustomRole::create([
            'host_id' => $hostId,
            'name' => $validated['name'],
            'permissions' => $validated['permissions'] ?? [],
        ]);

        return back()->with('success', 'Role kustom berhasil ditambahkan.');
    }

    public function update(Request $request, CustomRole $customRole)
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;
        
        if (! HostRoleHelper::canManageMembers($user) || $customRole->host_id !== $hostId) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
        ]);

        $customRole->update([
            'name' => $validated['name'],
            'permissions' => $validated['permissions'] ?? [],
        ]);

        return back()->with('success', 'Role kustom berhasil diperbarui.');
    }

    public function destroy(Request $request, CustomRole $customRole)
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;
        
        if (! HostRoleHelper::canManageMembers($user) || $customRole->host_id !== $hostId) {
            abort(403);
        }

        $customRole->delete();

        return back()->with('success', 'Role kustom berhasil dihapus.');
    }
}
