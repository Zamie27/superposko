<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Helpers\HostRoleHelper;
use App\Models\Finance;
use App\Models\FinanceTag;
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
                    'payment_method' => $fin->payment_method,
                    'destination_payment_method' => $fin->destination_payment_method,
                    'amount' => (float) $fin->amount,
                    'title' => $fin->title,
                    'description' => $fin->description,
                    'category' => $fin->category,
                    'date' => $fin->date->format('Y-m-d'),
                    'receipt_path' => $fin->receipt_path ? Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($fin->receipt_path) : null,
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

        $paymentMethods = ['Cash', 'SeaBank', 'DANA'];

        // Calculate Proker Balances per method
        $prokerBalances = [];
        foreach ($programKerjas as $proker) {
            $prokerBalances[$proker->id] = [];
            foreach ($paymentMethods as $method) {
                $prokerBalances[$proker->id][$method] = $this->getProkerMethodBalance($proker->id, $method);
            }
        }

        // Fetch custom tags for this host
        $customTags = FinanceTag::where('host_id', $hostId)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'type']);

        // Calculate aggregates
        // 1. Total Pemasukan Umum (Cash In, no proker) + returns from proker
        $totalIncome = Finance::where('host_id', $hostId)
            ->where('type', 'income')
            ->whereNull('program_kerja_id')
            ->sum('amount');

        $prokerReturns = Finance::where('host_id', $hostId)
            ->where('type', 'allocation')
            ->where('category', 'Proker ke Kas')
            ->sum('amount');

        $generalIncome = $totalIncome + $prokerReturns;

        // 2. Total Pengeluaran Umum (Cash Out, no proker) + allocations to proker
        $generalExpense = Finance::where('host_id', $hostId)
            ->where('type', 'expense')
            ->whereNull('program_kerja_id')
            ->sum('amount');

        $prokerAllocations = Finance::where('host_id', $hostId)
            ->where('type', 'allocation')
            ->where('category', 'Kas ke Proker')
            ->sum('amount');

        $totalExpense = $generalExpense + $prokerAllocations;
        $balance = $generalIncome - $totalExpense;

        $paymentMethods = ['Cash', 'SeaBank', 'DANA'];
        $balancesByMethod = [];
        foreach ($paymentMethods as $method) {
            $balancesByMethod[$method] = $this->getMethodBalance($hostId, $method);
        }

        return Inertia::render('finance/Index', [
            'finances' => $finances,
            'programKerjas' => $programKerjas,
            'customTags' => $customTags,
            'metrics' => [
                'total_income' => (float) $generalIncome,
                'total_expense' => (float) $totalExpense,
                'balance' => (float) $balance,
                'balances_by_method' => $balancesByMethod,
                'proker_balances' => $prokerBalances,
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
            'type' => ['required', 'string', Rule::in(['income', 'expense', 'allocation', 'transfer'])],
            'payment_method' => ['required', 'string', Rule::in(['Cash', 'SeaBank', 'DANA'])],
            'destination_payment_method' => ['nullable', 'string', Rule::in(['Cash', 'SeaBank', 'DANA'])],
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

            // normal income: cannot link to Proker
            if (! empty($validated['program_kerja_id'])) {
                return back()->withErrors(['program_kerja_id' => 'Transaksi Pemasukan tidak dapat dihubungkan ke Program Kerja. Silakan gunakan tipe Alokasi Dana.']);
            }
        }

        $receiptPath = null;
        if ($request->hasFile('receipt_file')) {
            $receiptPath = $request->file('receipt_file')->store('receipts', env('FILESYSTEM_DISK', 'public'));
        }

        $finance = Finance::create([
            'host_id' => $hostId,
            'program_kerja_id' => $validated['program_kerja_id'] ?? null,
            'category' => $validated['category'] ?? null,
            'created_by' => $user->id,
            'type' => $validated['type'],
            'payment_method' => $validated['payment_method'],
            'destination_payment_method' => $validated['destination_payment_method'] ?? null,
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
            'type' => ['required', 'string', Rule::in(['income', 'expense', 'allocation', 'transfer'])],
            'payment_method' => ['required', 'string', Rule::in(['Cash', 'SeaBank', 'DANA'])],
            'destination_payment_method' => [
                Rule::requiredIf(fn () => in_array($request->type, ['transfer', 'allocation'])),
                'nullable', 'string', Rule::in(['Cash', 'SeaBank', 'DANA']),
            ],
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

        // Validation check based on the new 3-type system (income, expense, allocation)
        if ($validated['type'] === 'allocation') {
            if (empty($validated['program_kerja_id'])) {
                return back()->withErrors(['program_kerja_id' => 'Harap pilih Program Kerja untuk transaksi Alokasi Dana.']);
            }
            if ($validated['category'] === 'Kas ke Proker') {
                $currentGeneralBalance = $this->getGeneralKasBalance($hostId);
                $adjustedGeneralBalance = $currentGeneralBalance;

                // Adjust for current transaction
                if ($finance->type === 'allocation' && $finance->category === 'Kas ke Proker') {
                    $adjustedGeneralBalance += $finance->amount;
                } elseif ($finance->type === 'allocation' && $finance->category === 'Proker ke Kas') {
                    $adjustedGeneralBalance -= $finance->amount;
                } elseif ($finance->type === 'expense' && $finance->program_kerja_id === null) {
                    $adjustedGeneralBalance += $finance->amount;
                } elseif ($finance->type === 'income' && $finance->program_kerja_id === null) {
                    $adjustedGeneralBalance -= $finance->amount;
                }

                if ($adjustedGeneralBalance <= 0) {
                    return back()->withErrors(['amount' => 'Saldo kas umum kosong atau minus. Tidak dapat melakukan alokasi dana ke Program Kerja saat ini (Saldo saat ini: Rp '.number_format($adjustedGeneralBalance, 0, ',', '.').').']);
                }
                if ($adjustedGeneralBalance < $validated['amount']) {
                    return back()->withErrors(['amount' => 'Saldo kas umum tidak mencukupi untuk memindahkan dana ke Program Kerja ini (Saldo saat ini: Rp '.number_format($adjustedGeneralBalance, 0, ',', '.').').']);
                }
                // Also validate per-method balance
                $currentMethodBalance = $this->getMethodBalance($hostId, $validated['payment_method']);
                $adjustedMethodBalance = $currentMethodBalance;
                // Adjust for current transaction's impact on this method
                if ($finance->payment_method === $validated['payment_method']) {
                    if ($finance->type === 'allocation' && $finance->category === 'Kas ke Proker') {
                        $adjustedMethodBalance += $finance->amount;
                    } elseif ($finance->type === 'income' && $finance->program_kerja_id === null) {
                        $adjustedMethodBalance -= $finance->amount;
                    } elseif ($finance->type === 'expense' && $finance->program_kerja_id === null) {
                        $adjustedMethodBalance += $finance->amount;
                    }
                }
                if ($adjustedMethodBalance < $validated['amount']) {
                    return back()->withErrors(['amount' => 'Saldo rekening '.$validated['payment_method'].' tidak mencukupi untuk alokasi ini (Saldo saat ini: Rp '.number_format($adjustedMethodBalance, 0, ',', '.').').']);
                }
            } elseif ($validated['category'] === 'Proker ke Kas') {
                $currentProkerBalance = $this->getProkerBalance($validated['program_kerja_id']);
                $adjustedProkerBalance = $currentProkerBalance;

                // Adjust for current transaction if it was linked to the SAME proker
                if ($finance->program_kerja_id == $validated['program_kerja_id']) {
                    if ($finance->type === 'allocation' && $finance->category === 'Kas ke Proker') {
                        $adjustedProkerBalance -= $finance->amount;
                    } elseif ($finance->type === 'allocation' && $finance->category === 'Proker ke Kas') {
                        $adjustedProkerBalance += $finance->amount;
                    } elseif ($finance->type === 'expense') {
                        $adjustedProkerBalance += $finance->amount;
                    }
                }

                if ($adjustedProkerBalance <= 0) {
                    return back()->withErrors(['amount' => 'Saldo dana Program Kerja kosong atau minus. Tidak ada dana proker yang dapat dikembalikan ke kas posko.']);
                }
                if ($adjustedProkerBalance < $validated['amount']) {
                    return back()->withErrors(['amount' => 'Dana yang ingin dipindahkan melebihi sisa saldo dana Program Kerja (Saldo proker saat ini: Rp '.number_format($adjustedProkerBalance, 0, ',', '.').').']);
                }
            } else {
                return back()->withErrors(['category' => 'Arah alokasi dana tidak valid.']);
            }
        } elseif ($validated['type'] === 'expense') {
            if (! empty($validated['program_kerja_id'])) {
                // Belanja Proker. Check Proker available balance.
                $currentProkerBalance = $this->getProkerBalance($validated['program_kerja_id']);
                $adjustedProkerBalance = $currentProkerBalance;

                if ($finance->program_kerja_id == $validated['program_kerja_id']) {
                    if ($finance->type === 'allocation' && $finance->category === 'Kas ke Proker') {
                        $adjustedProkerBalance -= $finance->amount;
                    } elseif ($finance->type === 'allocation' && $finance->category === 'Proker ke Kas') {
                        $adjustedProkerBalance += $finance->amount;
                    } elseif ($finance->type === 'expense') {
                        $adjustedProkerBalance += $finance->amount;
                    }
                }

                if ($adjustedProkerBalance <= 0) {
                    return back()->withErrors(['amount' => 'Saldo dana Program Kerja kosong atau minus. Silakan alokasikan dana dari kas posko terlebih dahulu.']);
                }
                if ($adjustedProkerBalance < $validated['amount']) {
                    return back()->withErrors(['amount' => 'Saldo dana Program Kerja tidak mencukupi untuk pengeluaran belanja ini (Saldo proker saat ini: Rp '.number_format($adjustedProkerBalance, 0, ',', '.').').']);
                }
            } else {
                // General transaction (not linked to Proker)
                $currentGeneralBalance = $this->getGeneralKasBalance($hostId);
                $adjustedGeneralBalance = $currentGeneralBalance;

                if ($finance->type === 'allocation' && $finance->category === 'Kas ke Proker') {
                    $adjustedGeneralBalance += $finance->amount;
                } elseif ($finance->type === 'allocation' && $finance->category === 'Proker ke Kas') {
                    $adjustedGeneralBalance -= $finance->amount;
                } elseif ($finance->type === 'expense' && $finance->program_kerja_id === null) {
                    $adjustedGeneralBalance += $finance->amount;
                } elseif ($finance->type === 'income' && $finance->program_kerja_id === null) {
                    $adjustedGeneralBalance -= $finance->amount;
                }

                if ($adjustedGeneralBalance <= 0) {
                    return back()->withErrors(['amount' => 'Saldo kas umum kosong atau minus. Tidak dapat melakukan pengeluaran kas saat ini (Saldo saat ini: Rp '.number_format($adjustedGeneralBalance, 0, ',', '.').').']);
                }
                if ($adjustedGeneralBalance < $validated['amount']) {
                    return back()->withErrors(['amount' => 'Saldo kas umum tidak mencukupi untuk transaksi ini (Saldo saat ini: Rp '.number_format($adjustedGeneralBalance, 0, ',', '.').').']);
                }
            }
        } elseif ($validated['type'] === 'transfer') {
            if (empty($validated['destination_payment_method'])) {
                return back()->withErrors(['destination_payment_method' => 'Harap pilih Tujuan Uang untuk transaksi Transfer.']);
            }
            if ($validated['payment_method'] === $validated['destination_payment_method']) {
                return back()->withErrors(['destination_payment_method' => 'Tujuan Uang tidak boleh sama dengan Sumber Uang.']);
            }

            $currentMethodBalance = $this->getMethodBalance($hostId, $validated['payment_method']);
            $adjustedMethodBalance = $currentMethodBalance;

            if ($finance->type === 'transfer' && $finance->payment_method === $validated['payment_method']) {
                $adjustedMethodBalance += $finance->amount;
            } elseif ($finance->type === 'transfer' && $finance->destination_payment_method === $validated['payment_method']) {
                $adjustedMethodBalance -= $finance->amount;
            } elseif ($finance->type === 'income' && $finance->payment_method === $validated['payment_method']) {
                $adjustedMethodBalance -= $finance->amount;
            } elseif ($finance->type === 'expense' && $finance->payment_method === $validated['payment_method']) {
                $adjustedMethodBalance += $finance->amount;
            }

            if ($adjustedMethodBalance <= 0) {
                return back()->withErrors(['amount' => 'Saldo sumber ('.$validated['payment_method'].') kosong atau minus. (Saldo saat ini: Rp '.number_format($adjustedMethodBalance, 0, ',', '.').').']);
            }
            if ($adjustedMethodBalance < $validated['amount']) {
                return back()->withErrors(['amount' => 'Saldo sumber tidak mencukupi untuk transfer ini (Saldo saat ini: Rp '.number_format($adjustedMethodBalance, 0, ',', '.').').']);
            }
        } else {
            // normal income: cannot link to Proker
            if (! empty($validated['program_kerja_id'])) {
                return back()->withErrors(['program_kerja_id' => 'Transaksi Pemasukan tidak dapat dihubungkan ke Program Kerja. Silakan gunakan tipe Alokasi Dana.']);
            }
        }

        $receiptPath = $finance->receipt_path;
        if ($request->hasFile('receipt_file')) {
            if ($receiptPath) {
                Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($receiptPath);
            }
            $receiptPath = $request->file('receipt_file')->store('receipts', env('FILESYSTEM_DISK', 'public'));
        }

        $finance->update([
            'program_kerja_id' => $validated['program_kerja_id'] ?? null,
            'category' => $validated['category'] ?? null,
            'type' => $validated['type'],
            'payment_method' => $validated['payment_method'],
            'destination_payment_method' => $validated['destination_payment_method'] ?? null,
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
            Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($finance->receipt_path);
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

    /**
     * Store a new custom finance tag.
     */
    public function storeTag(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola keuangan.');
        }

        $hostId = $user->host_id ?? $user->id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', Rule::in(['income', 'expense'])],
        ]);

        // Check for duplicate (including default tags)
        $defaultIncomeTags = ['Iuran Anggota', 'Sponsor', 'Donasi / Sumbangan', 'Dana Kampus'];
        $defaultExpenseTags = ['Konsumsi', 'Transportasi', 'Perlengkapan & Bahan', 'Humas & Publikasi'];
        $defaults = $validated['type'] === 'income' ? $defaultIncomeTags : $defaultExpenseTags;

        if (in_array($validated['name'], $defaults, true)) {
            return back()->withErrors(['name' => 'Tag ini sudah tersedia sebagai tag bawaan.']);
        }

        $existing = FinanceTag::where('host_id', $hostId)
            ->where('name', $validated['name'])
            ->where('type', $validated['type'])
            ->exists();

        if ($existing) {
            return back()->withErrors(['name' => 'Tag dengan nama ini sudah ada.']);
        }

        FinanceTag::create([
            'host_id' => $hostId,
            'name' => $validated['name'],
            'type' => $validated['type'],
        ]);

        return back()->with('success', 'Tag berhasil ditambahkan.');
    }

    /**
     * Delete a custom finance tag.
     */
    public function destroyTag(Request $request, FinanceTag $financeTag): RedirectResponse
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        if ($financeTag->host_id !== $hostId || ! HostRoleHelper::canWriteFinance($user)) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus tag ini.');
        }

        $financeTag->delete();

        return back()->with('success', 'Tag berhasil dihapus.');
    }

    private function getGeneralKasBalance($hostId): float
    {
        // Income (umum only)
        $totalIncome = Finance::where('host_id', $hostId)
            ->where('type', 'income')
            ->sum('amount');

        // Expense (umum only, no proker link)
        $generalExpense = Finance::where('host_id', $hostId)
            ->where('type', 'expense')
            ->whereNull('program_kerja_id')
            ->sum('amount');

        // Allocation: Kas ke Proker (reduces general kas)
        $kasToProker = Finance::where('host_id', $hostId)
            ->where('type', 'allocation')
            ->where('category', 'Kas ke Proker')
            ->sum('amount');

        // Allocation: Proker ke Kas (increases general kas)
        $prokerToKas = Finance::where('host_id', $hostId)
            ->where('type', 'allocation')
            ->where('category', 'Proker ke Kas')
            ->sum('amount');

        return (float) (($totalIncome + $prokerToKas) - ($generalExpense + $kasToProker));
    }

    private function getProkerBalance($prokerId): float
    {
        // Allocation: Kas ke Proker (adds to proker balance)
        $kasToProker = Finance::where('program_kerja_id', $prokerId)
            ->where('type', 'allocation')
            ->where('category', 'Kas ke Proker')
            ->sum('amount');

        // Allocation: Proker ke Kas (removes from proker balance)
        $prokerToKas = Finance::where('program_kerja_id', $prokerId)
            ->where('type', 'allocation')
            ->where('category', 'Proker ke Kas')
            ->sum('amount');

        // Expense: Belanja Proker (removes from proker balance)
        $spent = Finance::where('program_kerja_id', $prokerId)
            ->where('type', 'expense')
            ->sum('amount');

        return (float) ($kasToProker - $prokerToKas - $spent);
    }

    private function getProkerMethodBalance($prokerId, $method): float
    {
        // Allocation: Kas ke Proker (adds to proker method balance when destination is this method)
        $kasToProker = Finance::where('program_kerja_id', $prokerId)
            ->where('type', 'allocation')
            ->where('category', 'Kas ke Proker')
            ->where(function($q) use ($method) {
                $q->where('destination_payment_method', $method)
                  ->orWhere(function($q2) use ($method) {
                      $q2->whereNull('destination_payment_method')->where('payment_method', $method);
                  });
            })
            ->sum('amount');

        // Allocation: Proker ke Kas (removes from proker method balance when source is this method)
        $prokerToKas = Finance::where('program_kerja_id', $prokerId)
            ->where('type', 'allocation')
            ->where('category', 'Proker ke Kas')
            ->where('payment_method', $method)
            ->sum('amount');

        // Expense: Belanja Proker (removes from proker method balance when source is this method)
        $spent = Finance::where('program_kerja_id', $prokerId)
            ->where('type', 'expense')
            ->where('payment_method', $method)
            ->sum('amount');

        return (float) ($kasToProker - $prokerToKas - $spent);
    }

    private function getMethodBalance($hostId, $method): float
    {
        $mIncome = Finance::where('host_id', $hostId)->where('payment_method', $method)->where('type', 'income')->whereNull('program_kerja_id')->sum('amount');
        
        $mReturns = Finance::where('host_id', $hostId)->where('type', 'allocation')->where('category', 'Proker ke Kas')
            ->where(function($q) use ($method) {
                $q->where('destination_payment_method', $method)
                  ->orWhere(function($q2) use ($method) {
                      $q2->whereNull('destination_payment_method')->where('payment_method', $method);
                  });
            })->sum('amount');
            
        $mTransferIn = Finance::where('host_id', $hostId)->where('destination_payment_method', $method)->where('type', 'transfer')->sum('amount');

        $mExpense = Finance::where('host_id', $hostId)->where('payment_method', $method)->where('type', 'expense')->whereNull('program_kerja_id')->sum('amount');
        $mAllocations = Finance::where('host_id', $hostId)->where('payment_method', $method)->where('type', 'allocation')->where('category', 'Kas ke Proker')->sum('amount');
        $mTransferOut = Finance::where('host_id', $hostId)->where('payment_method', $method)->where('type', 'transfer')->sum('amount');

        return (float) (($mIncome + $mReturns + $mTransferIn) - ($mExpense + $mAllocations + $mTransferOut));
    }
}
