<?php

namespace App\Http\Controllers;

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

        $logistics = Logistic::where('host_id', $hostId)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('management/logistic/Index', [
            'logistics' => $logistics,
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
        ]);

        $hostId = $user->host_id ?? $user->id;

        $logistic = Logistic::create([
            'host_id' => $hostId,
            'name' => $validated['name'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        ActivityLogHelper::log(
            'member',
            'create_logistic',
            "User added logistic item '{$logistic->name}' (Qty: {$logistic->quantity} {$logistic->unit})."
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
        ]);

        $logistic->update([
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
