<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Helpers\HostRoleHelper;
use App\Models\Finance;
use App\Models\Logistic;
use App\Models\User;
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

        $logistics = Logistic::with(['owner:id,name,email', 'finance:id,payment_method'])
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
            'canWrite' => HostRoleHelper::canWriteLogistic($user),
        ]);
    }

    /**
     * Store a newly created logistic item or restock an existing logistic item.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! HostRoleHelper::canWriteLogistic($user)) {
            abort(403, 'Anda tidak memiliki hak untuk mengelola logistik.');
        }

        $validated = $request->validate([
            'logistic_id' => ['nullable', 'integer', 'exists:logistics,id'],
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['sufficient', 'low', 'out'])],
            'notes' => ['nullable', 'string', 'max:255'],
            'source' => ['required', 'string', Rule::in(['member', 'purchase'])],
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'required_if:source,purchase', 'string', Rule::in(['Cash', 'SeaBank', 'DANA'])],
            'date' => ['required', 'date'],
        ]);

        $hostId = $user->host_id ?? $user->id;

        $existingLogistic = null;
        if (! empty($validated['logistic_id'])) {
            $existingLogistic = Logistic::where('host_id', $hostId)->where('id', $validated['logistic_id'])->first();
        } else {
            $existingLogistic = Logistic::where('host_id', $hostId)
                ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($validated['name']))])
                ->first();
        }

        $ownerId = $validated['owner_id'] ?? null;
        if (empty($ownerId) || $ownerId === 'null') {
            $ownerId = null;
        }

        $source = $validated['source'] ?? 'member';
        $purchasePrice = $validated['purchase_price'] ?? null;
        $addedQuantity = (float) $validated['quantity'];

        // If restocking an existing item
        if ($existingLogistic) {
            $financeId = $existingLogistic->finance_id;

            if ($source === 'purchase' && $purchasePrice > 0) {
                $paymentMethod = $validated['payment_method'] ?? 'Cash';
                $totalExpense = $purchasePrice * $addedQuantity;

                $currentBalance = Finance::getGeneralMethodBalance($hostId, $paymentMethod);
                if ($totalExpense > $currentBalance) {
                    return back()->withErrors(['purchase_price' => "Saldo {$paymentMethod} tidak mencukupi (Saldo: Rp ".number_format($currentBalance, 0, ',', '.').').']);
                }

                $finance = Finance::create([
                    'host_id' => $hostId,
                    'program_kerja_id' => null,
                    'created_by' => $user->id,
                    'type' => 'expense',
                    'amount' => $totalExpense,
                    'payment_method' => $paymentMethod,
                    'title' => 'Pembelian Logistik: '.$existingLogistic->name,
                    'description' => 'Pembelian otomatis dari penambahan logistik.',
                    'date' => $validated['date'],
                ]);
                $financeId = $finance->id;
            }

            $newQuantity = $existingLogistic->quantity + $addedQuantity;

            // Recalculate status based on new quantity if needed
            $status = $validated['status'];
            if ($newQuantity > 3 && $status === 'out') {
                $status = 'sufficient';
            } elseif ($newQuantity > 0 && $newQuantity <= 3 && $status === 'out') {
                $status = 'low';
            }

            $existingLogistic->update([
                'quantity' => $newQuantity,
                'status' => $status,
                'notes' => ! empty($validated['notes']) ? $validated['notes'] : $existingLogistic->notes,
                'date' => $validated['date'],
                'finance_id' => $financeId,
            ]);

            ActivityLogHelper::log(
                'member',
                'restock_logistic',
                "User restocked logistic item '{$existingLogistic->name}' (+{$addedQuantity} {$existingLogistic->unit}, Total: {$newQuantity} {$existingLogistic->unit})."
            );

            return back()->with('success', 'Stok bahan logistik berhasil ditambahkan.');
        }

        // Creating a brand new logistic item
        $financeId = null;

        if ($source === 'purchase' && $purchasePrice > 0) {
            $paymentMethod = $validated['payment_method'] ?? 'Cash';
            $amount = $purchasePrice * $addedQuantity;

            $currentBalance = Finance::getGeneralMethodBalance($hostId, $paymentMethod);
            if ($amount > $currentBalance) {
                return back()->withErrors(['purchase_price' => "Saldo {$paymentMethod} tidak mencukupi (Saldo: Rp ".number_format($currentBalance, 0, ',', '.').').']);
            }

            $finance = Finance::create([
                'host_id' => $hostId,
                'program_kerja_id' => null,
                'created_by' => $user->id,
                'type' => 'expense',
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'title' => 'Pembelian Logistik: '.$validated['name'],
                'description' => 'Pembelian otomatis dari penambahan logistik.',
                'date' => $validated['date'],
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
            'quantity' => $addedQuantity,
            'unit' => $validated['unit'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'date' => $validated['date'],
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

        if ($logistic->host_id !== $hostId || ! HostRoleHelper::canWriteLogistic($user)) {
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
            'payment_method' => ['nullable', 'required_if:source,purchase', 'string', Rule::in(['Cash', 'SeaBank', 'DANA'])],
            'date' => ['required', 'date'],
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
                return back()->withErrors(['purchase_price' => "Saldo {$paymentMethod} tidak mencukupi (Saldo: Rp ".number_format($currentBalance, 0, ',', '.').').']);
            }

            if ($financeId) {
                // Update existing finance record
                Finance::where('id', $financeId)->update([
                    'title' => 'Pembelian Logistik: '.$validated['name'],
                    'amount' => $purchasePrice * $validated['quantity'],
                    'payment_method' => $validated['payment_method'] ?? 'Cash',
                    'date' => $validated['date'],
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
                    'title' => 'Pembelian Logistik: '.$validated['name'],
                    'description' => 'Pembelian otomatis dari penambahan logistik.',
                    'date' => $validated['date'],
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
            'date' => $validated['date'],
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

        if ($logistic->host_id !== $hostId || ! HostRoleHelper::canWriteLogistic($user)) {
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
            $logistic = Logistic::where('id', $item['id'])->firstOrFail();

            // Posko isolation check
            if ($logistic->host_id !== $hostId) {
                abort(403, 'Anda tidak memiliki akses ke logistik ini.');
            }

            $amount = (float) $item['amount'];

            if ($logistic->quantity < $amount) {
                return back()->withErrors([
                    'items' => "Stok untuk {$logistic->name} tidak mencukupi (Tersedia: {$logistic->quantity} {$logistic->unit}, Diminta: {$amount} {$logistic->unit}).",
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
