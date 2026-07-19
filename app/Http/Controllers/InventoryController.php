<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Helpers\HostRoleHelper;
use App\Models\Finance;
use App\Models\Inventory;
use App\Models\User;
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

        $inventories = Inventory::with(['owner:id,name,email', 'finance:id,payment_method'])
            ->where('host_id', $hostId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $item->image_url = $item->image_path ? Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($item->image_path) : null;
                return $item;
            });

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
     * If source is 'purchase', automatically create a Finance expense record.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak untuk mengelola inventaris.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit' => ['required', 'string', 'max:255'],
            'condition' => ['required', 'string', Rule::in(['good', 'damaged', 'lost'])],
            'notes' => ['nullable', 'string', 'max:255'],
            'source' => ['required', 'string', Rule::in(['member', 'purchase'])],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:5120'], // Max 5MB
            'payment_method' => ['nullable', 'required_if:source,purchase', 'string', Rule::in(['Cash', 'SeaBank', 'DANA'])],
        ]);

        $hostId = $user->host_id ?? $user->id;

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('inventories', env('FILESYSTEM_DISK', 'public'));
        }

        $ownerId = $validated['owner_id'] ?? null;
        if (empty($ownerId) || $ownerId === 'null') {
            $ownerId = null;
        }

        $source = $validated['source'] ?? 'member';
        $purchasePrice = $validated['purchase_price'] ?? null;
        $financeId = null;

        // If purchased from kas, auto-create Finance expense record
        if ($source === 'purchase' && $purchasePrice > 0) {
            $paymentMethod = $validated['payment_method'] ?? 'Cash';
            $amount = $purchasePrice * $validated['quantity'];
            
            $currentBalance = Finance::getGeneralMethodBalance($hostId, $paymentMethod);
            if ($amount > $currentBalance) {
                return back()->withErrors(['purchase_price' => "Saldo {$paymentMethod} tidak mencukupi (Saldo: Rp " . number_format($currentBalance, 0, ',', '.') . ")."]);
            }

            $finance = Finance::create([
                'host_id' => $hostId,
                'program_kerja_id' => null,
                'created_by' => $user->id,
                'type' => 'expense',
                'amount' => $purchasePrice * $validated['quantity'],
                'payment_method' => $validated['payment_method'] ?? 'Cash',
                'title' => 'Pembelian Inventaris: '.$validated['name'],
                'description' => 'Pembelian otomatis dari penambahan inventaris.',
                'date' => now()->toDateString(),
            ]);
            $financeId = $finance->id;
        }

        $inventory = Inventory::create([
            'host_id' => $hostId,
            'owner_id' => $source === 'member' ? $ownerId : null,
            'source' => $source,
            'purchase_price' => $source === 'purchase' ? $purchasePrice : null,
            'finance_id' => $financeId,
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'condition' => $validated['condition'],
            'notes' => $validated['notes'] ?? null,
            'image_path' => $imagePath,
        ]);

        ActivityLogHelper::log(
            'member',
            'create_inventory',
            "User added inventory item '{$inventory->name}' (Qty: {$inventory->quantity}, Sumber: {$source})."
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

        if ($inventory->host_id !== $hostId || ! HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak untuk mengelola inventaris.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit' => ['required', 'string', 'max:255'],
            'condition' => ['required', 'string', Rule::in(['good', 'damaged', 'lost'])],
            'notes' => ['nullable', 'string', 'max:255'],
            'source' => ['required', 'string', Rule::in(['member', 'purchase'])],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:5120'], // Max 5MB
            'payment_method' => ['nullable', 'required_if:source,purchase', 'string', Rule::in(['Cash', 'SeaBank', 'DANA'])],
        ]);

        $imagePath = $inventory->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('inventories', env('FILESYSTEM_DISK', 'public'));
        }

        $ownerId = $validated['owner_id'] ?? null;
        if (empty($ownerId) || $ownerId === 'null') {
            $ownerId = null;
        }

        $source = $validated['source'] ?? 'member';
        $purchasePrice = $validated['purchase_price'] ?? null;
        $financeId = $inventory->finance_id;

        // Handle finance record updates
        if ($source === 'purchase' && $purchasePrice > 0) {
            $paymentMethod = $validated['payment_method'] ?? 'Cash';
            $amount = $purchasePrice * $validated['quantity'];
            
            $currentBalance = Finance::getGeneralMethodBalance($hostId, $paymentMethod);
            
            if ($financeId) {
                $financeRec = Finance::find($financeId);
                if ($financeRec && $financeRec->payment_method === $paymentMethod) {
                    $currentBalance += $financeRec->amount;
                }
            }
            
            if ($amount > $currentBalance) {
                return back()->withErrors(['purchase_price' => "Saldo {$paymentMethod} tidak mencukupi (Saldo: Rp " . number_format($currentBalance, 0, ',', '.') . ")."]);
            }

            if ($financeId) {
                // Update existing finance record
                Finance::where('id', $financeId)->update([
                    'title' => 'Pembelian Inventaris: '.$validated['name'],
                    'amount' => $purchasePrice * $validated['quantity'],
                    'payment_method' => $validated['payment_method'] ?? 'Cash',
                ]);
            } else {
                // Create new finance record (source changed from member to purchase)
                $finance = Finance::create([
                    'host_id' => $hostId,
                    'program_kerja_id' => null,
                    'created_by' => $user->id,
                    'type' => 'expense',
                    'amount' => $purchasePrice * $validated['quantity'],
                    'payment_method' => $validated['payment_method'] ?? 'Cash',
                    'title' => 'Pembelian Inventaris: '.$validated['name'],
                    'description' => 'Pembelian otomatis dari penambahan inventaris.',
                    'date' => now()->toDateString(),
                ]);
                $financeId = $finance->id;
            }
        } elseif ($source === 'member' && $financeId) {
            // Source changed from purchase to member – delete linked finance record
            Finance::find($financeId)?->delete();
            $financeId = null;
        }

        $inventory->update([
            'owner_id' => $source === 'member' ? $ownerId : null,
            'source' => $source,
            'purchase_price' => $source === 'purchase' ? $purchasePrice : null,
            'finance_id' => $financeId,
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
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
     * Also deletes the linked Finance record if purchased from kas.
     */
    public function destroy(Request $request, Inventory $inventory): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($inventory->host_id !== $hostId || ! HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak untuk mengelola inventaris.');
        }

        if ($inventory->image_path) {
            Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($inventory->image_path);
        }

        // If linked to a finance record (purchased from kas), delete it too
        if ($inventory->finance_id) {
            Finance::find($inventory->finance_id)?->delete();
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
