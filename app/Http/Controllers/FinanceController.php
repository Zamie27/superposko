<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Helpers\HostRoleHelper;
use App\Models\Finance;
use App\Models\ProgramKerja;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class FinanceController extends Controller
{
    /**
     * Display the financial ledger dashboard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        // Fetch all financial transactions with related creator and program kerja
        $finances = Finance::with(['creator:id,name,email', 'programKerja:id,name'])
            ->where('host_id', $hostId)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Finance $fin) {
                return [
                    'id' => $fin->id,
                    'type' => $fin->type,
                    'amount' => (float) $fin->amount,
                    'title' => $fin->title,
                    'description' => $fin->description,
                    'category' => $fin->category,
                    'date' => $fin->date->format('Y-m-d'),
                    'receipt_path' => $fin->receipt_path ? Storage::url($fin->receipt_path) : null,
                    'program_kerja_id' => $fin->program_kerja_id,
                    'program_kerja' => $fin->programKerja ? [
                        'id' => $fin->programKerja->id,
                        'name' => $fin->programKerja->name,
                    ] : null,
                    'creator' => [
                        'id' => $fin->creator->id,
                        'name' => $fin->creator->name,
                    ],
                ];
            });

        // Fetch program kerjas for the dropdown selector
        $programKerjas = ProgramKerja::where('host_id', $hostId)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'budget']);

        // Calculate aggregates
        $totalIncome = Finance::where('host_id', $hostId)
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = Finance::where('host_id', $hostId)
            ->where('type', 'expense')
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        return Inertia::render('finance/Index', [
            'finances' => $finances,
            'programKerjas' => $programKerjas,
            'metrics' => [
                'total_income' => (float) $totalIncome,
                'total_expense' => (float) $totalExpense,
                'balance' => (float) $balance,
            ],
            'canWrite' => HostRoleHelper::canWriteFinance($user),
        ]);
    }

    /**
     * Store a new financial record.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola keuangan.');
        }

        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(['income', 'expense'])],
            'amount' => ['required', 'numeric', 'min:0'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category' => ['nullable', 'string', 'max:100'],
            'date' => ['required', 'date'],
            'program_kerja_id' => ['nullable', 'integer', 'exists:program_kerjas,id'],
            'receipt_file' => ['nullable', 'image', 'max:5120'], // Max 5MB
        ]);

        $hostId = $user->host_id ?? $user->id;

        // Ensure program kerja belongs to the same host if linked
        if (! empty($validated['program_kerja_id'])) {
            $proker = ProgramKerja::where('id', $validated['program_kerja_id'])
                ->where('host_id', $hostId)
                ->first();
            if (! $proker) {
                abort(400, 'Program Kerja tidak valid untuk posko Anda.');
            }
        }

        $receiptPath = null;
        if ($request->hasFile('receipt_file')) {
            $receiptPath = $request->file('receipt_file')->store('receipts', 'public');
        }

        $finance = Finance::create([
            'host_id' => $hostId,
            'program_kerja_id' => $validated['program_kerja_id'] ?? null,
            'category' => $validated['category'] ?? null,
            'created_by' => $user->id,
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'date' => $validated['date'],
            'receipt_path' => $receiptPath,
        ]);

        ActivityLogHelper::log(
            'member',
            'create_finance',
            "User recorded financial transaction: '{$finance->title}' (Rp ".number_format($finance->amount, 0, ',', '.').').'
        );

        return back()->with('success', 'Transaksi berhasil dicatat.');
    }

    /**
     * Update an existing financial record.
     */
    public function update(Request $request, Finance $finance): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($finance->host_id !== $hostId || ! HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola transaksi ini.');
        }

        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(['income', 'expense'])],
            'amount' => ['required', 'numeric', 'min:0'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category' => ['nullable', 'string', 'max:100'],
            'date' => ['required', 'date'],
            'program_kerja_id' => ['nullable', 'integer', 'exists:program_kerjas,id'],
            'receipt_file' => ['nullable', 'image', 'max:5120'], // Max 5MB
        ]);

        // Ensure program kerja belongs to the same host if linked
        if (! empty($validated['program_kerja_id'])) {
            $proker = ProgramKerja::where('id', $validated['program_kerja_id'])
                ->where('host_id', $hostId)
                ->first();
            if (! $proker) {
                abort(400, 'Program Kerja tidak valid untuk posko Anda.');
            }
        }

        $receiptPath = $finance->receipt_path;
        if ($request->hasFile('receipt_file')) {
            if ($receiptPath) {
                Storage::disk('public')->delete($receiptPath);
            }
            $receiptPath = $request->file('receipt_file')->store('receipts', 'public');
        }

        $finance->update([
            'program_kerja_id' => $validated['program_kerja_id'] ?? null,
            'category' => $validated['category'] ?? null,
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'date' => $validated['date'],
            'receipt_path' => $receiptPath,
        ]);

        ActivityLogHelper::log(
            'member',
            'update_finance',
            "User updated financial transaction: '{$finance->title}'."
        );

        return back()->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Delete a financial record.
     */
    public function destroy(Request $request, Finance $finance): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($finance->host_id !== $hostId || ! HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola transaksi ini.');
        }

        if ($finance->receipt_path) {
            Storage::disk('public')->delete($finance->receipt_path);
        }

        $title = $finance->title;
        $finance->delete();

        ActivityLogHelper::log(
            'member',
            'delete_finance',
            "User deleted financial transaction '{$title}'."
        );

        return back()->with('success', 'Transaksi berhasil dihapus.');
    }
}
