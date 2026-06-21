<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\User;
use App\Helpers\HostRoleHelper;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventories.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        $inventories = Inventory::with('owner:id,name,email')
            ->where('host_id', $hostId)
            ->orderBy('created_at', 'desc')
            ->get();

        $members = User::where('host_id', $hostId)
            ->orWhere('id', $hostId)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email']);

        return Inertia::render('management/inventory/Index', [
            'inventories' => $inventories,
            'members' => $members,
            'canWrite' => HostRoleHelper::canWriteFinance($user),
        ]);
    }

    /**
     * Store a newly created inventory item.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak untuk mengelola inventaris.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'condition' => ['required', 'string', Rule::in(['good', 'damaged', 'lost'])],
            'notes' => ['nullable', 'string', 'max:255'],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'image' => ['nullable', 'image', 'max:5120'], // Max 5MB
        ]);

        $hostId = $user->host_id ?? $user->id;

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('inventories', 'public');
        }

        $ownerId = $validated['owner_id'] ?? null;
        if (empty($ownerId) || $ownerId === 'null') {
            $ownerId = null;
        }

        $inventory = Inventory::create([
            'host_id' => $hostId,
            'owner_id' => $ownerId,
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'condition' => $validated['condition'],
            'notes' => $validated['notes'] ?? null,
            'image_path' => $imagePath,
        ]);

        ActivityLogHelper::log(
            'member',
            'create_inventory',
            "User added inventory item '{$inventory->name}' (Qty: {$inventory->quantity})."
        );

        return back()->with('success', 'Barang inventaris berhasil ditambahkan.');
    }

    /**
     * Update the specified inventory item.
     */
    public function update(Request $request, Inventory $inventory): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($inventory->host_id !== $hostId || !HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak untuk mengelola inventaris.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'condition' => ['required', 'string', Rule::in(['good', 'damaged', 'lost'])],
            'notes' => ['nullable', 'string', 'max:255'],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'image' => ['nullable', 'image', 'max:5120'], // Max 5MB
        ]);

        $imagePath = $inventory->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('inventories', 'public');
        }

        $ownerId = $validated['owner_id'] ?? null;
        if (empty($ownerId) || $ownerId === 'null') {
            $ownerId = null;
        }

        $inventory->update([
            'owner_id' => $ownerId,
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'condition' => $validated['condition'],
            'notes' => $validated['notes'] ?? null,
            'image_path' => $imagePath,
        ]);

        ActivityLogHelper::log(
            'member',
            'update_inventory',
            "User updated inventory item '{$inventory->name}'."
        );

        return back()->with('success', 'Barang inventaris berhasil diperbarui.');
    }

    /**
     * Remove the specified inventory item.
     */
    public function destroy(Request $request, Inventory $inventory): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($inventory->host_id !== $hostId || !HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak untuk mengelola inventaris.');
        }

        if ($inventory->image_path) {
            Storage::disk('public')->delete($inventory->image_path);
        }

        $name = $inventory->name;
        $inventory->delete();

        ActivityLogHelper::log(
            'member',
            'delete_inventory',
            "User deleted inventory item '{$name}'."
        );

        return back()->with('success', 'Barang inventaris berhasil dihapus.');
    }
}
