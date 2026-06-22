<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use App\Models\User;
use App\Models\Logistic;
use App\Helpers\HostRoleHelper;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class LogisticController extends Controller
{
    /**
     * Display a listing of logistics.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        $logistics = Logistic::with('owner:id,name,email')
            ->where('host_id', $hostId)
            ->orderBy('created_at', 'desc')
            ->get();

        $members = User::where('host_id', $hostId)
            ->orWhere('id', $hostId)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email']);

        return Inertia::render('management/logistic/Index', [
            'logistics' => $logistics,
            'members' => $members,
            'canWrite' => HostRoleHelper::canWriteFinance($user),
        ]);
    }

    /**
     * Store a newly created logistic item.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak untuk mengelola logistik.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['sufficient', 'low', 'out'])],
            'notes' => ['nullable', 'string', 'max:255'],
            'source' => ['required', 'string', Rule::in(['member', 'purchase'])],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $hostId = $user->host_id ?? $user->id;

        $ownerId = $validated['owner_id'] ?? null;
        if (empty($ownerId) || $ownerId === 'null') {
            $ownerId = null;
        }

        $source = $validated['source'] ?? 'member';
        $purchasePrice = $validated['purchase_price'] ?? null;
        $financeId = null;

        // If purchased from kas, auto-create Finance expense record
        if ($source === 'purchase' && $purchasePrice > 0) {
            $finance = Finance::create([
                'host_id' => $hostId,
                'program_kerja_id' => null,
                'created_by' => $user->id,
                'type' => 'expense',
                'amount' => $purchasePrice * $validated['quantity'],
                'title' => 'Pembelian Logistik: ' . $validated['name'],
                'description' => 'Pembelian otomatis dari penambahan logistik.',
                'date' => now()->toDateString(),
            ]);
            $financeId = $finance->id;
        }

        $logistic = Logistic::create([
            'host_id' => $hostId,
            'owner_id' => $source === 'member' ? $ownerId : null,
            'source' => $source,
            'purchase_price' => $source === 'purchase' ? $purchasePrice : null,
            'finance_id' => $financeId,
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        ActivityLogHelper::log(
            'member',
            'create_logistic',
            "User added logistic item '{$logistic->name}' (Qty: {$logistic->quantity} {$logistic->unit}, Sumber: {$source})."
        );

        return back()->with('success', 'Bahan logistik berhasil ditambahkan.');
    }

    /**
     * Update the specified logistic item.
     */
    public function update(Request $request, Logistic $logistic): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($logistic->host_id !== $hostId || !HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak untuk mengelola logistik.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['sufficient', 'low', 'out'])],
            'notes' => ['nullable', 'string', 'max:255'],
            'source' => ['required', 'string', Rule::in(['member', 'purchase'])],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $ownerId = $validated['owner_id'] ?? null;
        if (empty($ownerId) || $ownerId === 'null') {
            $ownerId = null;
        }

        $source = $validated['source'] ?? 'member';
        $purchasePrice = $validated['purchase_price'] ?? null;
        $financeId = $logistic->finance_id;

        // Handle finance record updates
        if ($source === 'purchase' && $purchasePrice > 0) {
            if ($financeId) {
                // Update existing finance record
                Finance::where('id', $financeId)->update([
                    'title' => 'Pembelian Logistik: ' . $validated['name'],
                    'amount' => $purchasePrice * $validated['quantity'],
                ]);
            } else {
                // Create new finance record (source changed from member to purchase)
                $finance = Finance::create([
                    'host_id' => $hostId,
                    'program_kerja_id' => null,
                    'created_by' => $user->id,
                    'type' => 'expense',
                    'amount' => $purchasePrice * $validated['quantity'],
                    'title' => 'Pembelian Logistik: ' . $validated['name'],
                    'description' => 'Pembelian otomatis dari penambahan logistik.',
                    'date' => now()->toDateString(),
                ]);
                $financeId = $finance->id;
            }
        } elseif ($source === 'member' && $financeId) {
            // Source changed from purchase to member – delete linked finance record
            Finance::find($financeId)?->delete();
            $financeId = null;
        }

        $logistic->update([
            'owner_id' => $source === 'member' ? $ownerId : null,
            'source' => $source,
            'purchase_price' => $source === 'purchase' ? $purchasePrice : null,
            'finance_id' => $financeId,
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        ActivityLogHelper::log(
            'member',
            'update_logistic',
            "User updated logistic item '{$logistic->name}'."
        );

        return back()->with('success', 'Bahan logistik berhasil diperbarui.');
    }

    /**
     * Remove the specified logistic item.
     */
    public function destroy(Request $request, Logistic $logistic): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($logistic->host_id !== $hostId || !HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak untuk mengelola logistik.');
        }

        // If linked to a finance record (purchased from kas), delete it too
        if ($logistic->finance_id) {
            Finance::find($logistic->finance_id)?->delete();
        }

        $name = $logistic->name;
        $logistic->delete();

        ActivityLogHelper::log(
            'member',
            'delete_logistic',
            "User deleted logistic item '{$name}'."
        );

        return back()->with('success', 'Bahan logistik berhasil dihapus.');
    }

    /**
     * Catat barang keluar / pengambilan logistik.
     */
    public function barangKeluar(Request $request): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        // Any member of the posko can record barang keluar.
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:logistics,id'],
            'items.*.amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $keluarDetails = [];

        foreach ($validated['items'] as $item) {
            $logistic = Logistic::findOrFail($item['id']);

            // Posko isolation check
            if ($logistic->host_id !== $hostId) {
                abort(403, 'Anda tidak memiliki akses ke logistik ini.');
            }

            $amount = (float) $item['amount'];

            if ($logistic->quantity < $amount) {
                return back()->withErrors([
                    'items' => "Stok untuk {$logistic->name} tidak mencukupi (Tersedia: {$logistic->quantity} {$logistic->unit}, Diminta: {$amount} {$logistic->unit})."
                ]);
            }

            $newQuantity = max(0.0, $logistic->quantity - $amount);
            $status = 'sufficient';
            if ($newQuantity <= 0) {
                $status = 'out';
            } elseif ($newQuantity <= 3.0) {
                $status = 'low';
            }

            $logistic->update([
                'quantity' => $newQuantity,
                'status' => $status,
            ]);

            $keluarDetails[] = "{$amount} {$logistic->unit} dari {$logistic->name}";
        }

        $summary = implode(', ', $keluarDetails);
        ActivityLogHelper::log(
            'member',
            'barang_keluar_logistic',
            "User mencatat barang keluar: {$summary}."
        );

        return back()->with('success', 'Barang keluar berhasil dicatat.');
    }
}
