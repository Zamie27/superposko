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
        // 1. Total Pemasukan Umum (Cash In, no proker)
        $totalIncome = Finance::where('host_id', $hostId)
            ->where('type', 'income')
            ->whereNull('program_kerja_id')
            ->sum('amount');

        // 2. Total Pengeluaran Umum (Cash Out, no proker) + Alokasi ke Proker (Cash In with proker)
        $generalExpense = Finance::where('host_id', $hostId)
            ->where('type', 'expense')
            ->whereNull('program_kerja_id')
            ->sum('amount');

        $prokerAllocation = Finance::where('host_id', $hostId)
            ->where('type', 'income')
            ->whereNotNull('program_kerja_id')
            ->sum('amount');

        $totalExpense = $generalExpense + $prokerAllocation;
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

        // Validation check based on destination (proker vs general) and type (income vs expense)
        if (! empty($validated['program_kerja_id'])) {
            // Transaction linked to a Proker
            if ($validated['type'] === 'income') {
                // Transfer from General Kas to Proker (behaves as General Kas expense)
                $currentGeneralBalance = $this->getGeneralKasBalance($hostId);
                
                if ($currentGeneralBalance <= 0) {
                    return back()->withErrors(['amount' => 'Saldo kas umum kosong atau minus. Tidak dapat melakukan alokasi dana ke Program Kerja saat ini (Saldo saat ini: Rp ' . number_format($currentGeneralBalance, 0, ',', '.') . ').']);
                }
                if ($currentGeneralBalance < $validated['amount']) {
                    return back()->withErrors(['amount' => 'Saldo kas umum tidak mencukupi untuk melakukan alokasi dana ke Program Kerja ini (Saldo saat ini: Rp ' . number_format($currentGeneralBalance, 0, ',', '.') . ').']);
                }
            } else {
                // Actual spending of Proker. Must check Proker available balance.
                $currentProkerBalance = $this->getProkerBalance($validated['program_kerja_id']);
                
                if ($currentProkerBalance <= 0) {
                    return back()->withErrors(['amount' => 'Saldo dana Program Kerja kosong atau minus. Silakan alokasikan dana dari kas posko terlebih dahulu.']);
                }
                if ($currentProkerBalance < $validated['amount']) {
                    return back()->withErrors(['amount' => 'Saldo dana Program Kerja tidak mencukupi untuk pengeluaran ini (Saldo proker saat ini: Rp ' . number_format($currentProkerBalance, 0, ',', '.') . ').']);
                }
            }
        } else {
            // General transaction (not linked to Proker)
            if ($validated['type'] === 'expense') {
                $currentGeneralBalance = $this->getGeneralKasBalance($hostId);
                
                if ($currentGeneralBalance <= 0) {
                    return back()->withErrors(['amount' => 'Saldo kas umum kosong atau minus. Tidak dapat melakukan pengeluaran kas saat ini (Saldo saat ini: Rp ' . number_format($currentGeneralBalance, 0, ',', '.') . ').']);
                }
                if ($currentGeneralBalance < $validated['amount']) {
                    return back()->withErrors(['amount' => 'Saldo kas umum tidak mencukupi untuk transaksi ini (Saldo saat ini: Rp ' . number_format($currentGeneralBalance, 0, ',', '.') . ').']);
                }
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

        // Validation check based on destination (proker vs general) and type (income vs expense)
        if (! empty($validated['program_kerja_id'])) {
            // Transaction linked to a Proker
            if ($validated['type'] === 'income') {
                // Transfer from General Kas to Proker (behaves as General Kas expense)
                $currentGeneralBalance = $this->getGeneralKasBalance($hostId);
                $adjustedGeneralBalance = $currentGeneralBalance;
                
                if ($finance->program_kerja_id && $finance->type === 'income') {
                    $adjustedGeneralBalance += $finance->amount;
                } elseif (! $finance->program_kerja_id && $finance->type === 'expense') {
                    $adjustedGeneralBalance += $finance->amount;
                }
                
                if ($adjustedGeneralBalance <= 0) {
                    return back()->withErrors(['amount' => 'Saldo kas umum kosong atau minus. Tidak dapat melakukan alokasi dana ke Program Kerja saat ini (Saldo saat ini: Rp ' . number_format($adjustedGeneralBalance, 0, ',', '.') . ').']);
                }
                if ($adjustedGeneralBalance < $validated['amount']) {
                    return back()->withErrors(['amount' => 'Saldo kas umum tidak mencukupi untuk melakukan alokasi dana ke Program Kerja ini (Saldo saat ini: Rp ' . number_format($adjustedGeneralBalance, 0, ',', '.') . ').']);
                }
            } else {
                // Actual spending of Proker. Must check Proker available balance.
                $currentProkerBalance = $this->getProkerBalance($validated['program_kerja_id']);
                $adjustedProkerBalance = $currentProkerBalance;
                
                if ($finance->program_kerja_id == $validated['program_kerja_id']) {
                    if ($finance->type === 'expense') {
                        $adjustedProkerBalance += $finance->amount;
                    } else {
                        $adjustedProkerBalance -= $finance->amount;
                    }
                }
                
                if ($adjustedProkerBalance <= 0) {
                    return back()->withErrors(['amount' => 'Saldo dana Program Kerja kosong atau minus. Silakan alokasikan dana dari kas posko terlebih dahulu.']);
                }
                if ($adjustedProkerBalance < $validated['amount']) {
                    return back()->withErrors(['amount' => 'Saldo dana Program Kerja tidak mencukupi untuk pengeluaran ini (Saldo proker saat ini: Rp ' . number_format($adjustedProkerBalance, 0, ',', '.') . ').']);
                }
            }
        } else {
            // General transaction (not linked to Proker)
            if ($validated['type'] === 'expense') {
                $currentGeneralBalance = $this->getGeneralKasBalance($hostId);
                $adjustedGeneralBalance = $currentGeneralBalance;
                
                if ($finance->program_kerja_id && $finance->type === 'income') {
                    $adjustedGeneralBalance += $finance->amount;
                } elseif (! $finance->program_kerja_id && $finance->type === 'expense') {
                    $adjustedGeneralBalance += $finance->amount;
                }
                
                if ($adjustedGeneralBalance <= 0) {
                    return back()->withErrors(['amount' => 'Saldo kas umum kosong atau minus. Tidak dapat melakukan pengeluaran kas saat ini (Saldo saat ini: Rp ' . number_format($adjustedGeneralBalance, 0, ',', '.') . ').']);
                }
                if ($adjustedGeneralBalance < $validated['amount']) {
                    return back()->withErrors(['amount' => 'Saldo kas umum tidak mencukupi untuk transaksi ini (Saldo saat ini: Rp ' . number_format($adjustedGeneralBalance, 0, ',', '.') . ').']);
                }
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

    private function getGeneralKasBalance($hostId): float
    {
        $totalIncome = Finance::where('host_id', $hostId)
            ->where('type', 'income')
            ->whereNull('program_kerja_id')
            ->sum('amount');

        $generalExpense = Finance::where('host_id', $hostId)
            ->where('type', 'expense')
            ->whereNull('program_kerja_id')
            ->sum('amount');
            
        $prokerAllocation = Finance::where('host_id', $hostId)
            ->where('type', 'income')
            ->whereNotNull('program_kerja_id')
            ->sum('amount');

        return (float) ($totalIncome - ($generalExpense + $prokerAllocation));
    }

    private function getProkerBalance($prokerId): float
    {
        $allocated = Finance::where('program_kerja_id', $prokerId)
            ->where('type', 'income')
            ->sum('amount');

        $spent = Finance::where('program_kerja_id', $prokerId)
            ->where('type', 'expense')
            ->sum('amount');

        return (float) ($allocated - $spent);
    }
}
