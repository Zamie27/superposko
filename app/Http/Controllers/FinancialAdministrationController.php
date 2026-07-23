<?php

namespace App\Http\Controllers;

use App\Helpers\HostRoleHelper;
use App\Models\Finance;
use App\Models\Inventory;
use App\Models\Logistic;
use App\Models\ProgramKerja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class FinancialAdministrationController extends Controller
{
    /**
     * Helper to get base host ID and permissions.
     */
    private function getHostContext(Request $request): array
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        $host = User::where('id', $hostId)->first(['id', 'name', 'email']);

        $ketua = User::where('host_id', $hostId)
            ->where('role', 'ketua')
            ->first(['id', 'name']);

        if (!$ketua && $user->role === 'host') {
            $ketua = $user;
        }

        $bendahara = User::where('host_id', $hostId)
            ->where('role', 'bendahara')
            ->first(['id', 'name']);

        $dpl = User::where('host_id', $hostId)
            ->where('role', 'dpl')
            ->first(['id', 'name']);

        return [
            'user' => $user,
            'hostId' => $hostId,
            'host' => $host,
            'ketua' => $ketua,
            'bendahara' => $bendahara,
            'dpl' => $dpl,
            'canWrite' => HostRoleHelper::canWriteFinance($user),
        ];
    }

    /**
     * 1. Buku Kas Umum (BKU)
     */
    public function bukuKasUmum(Request $request): Response
    {
        $context = $this->getHostContext($request);
        $hostId = $context['hostId'];

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = Finance::with(['creator:id,name', 'programKerja:id,name'])
            ->where('host_id', $hostId)
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc');

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        $rawFinances = $query->get();

        $runningBalance = 0;
        $totalDebit = 0;
        $totalKredit = 0;

        $bkuItems = $rawFinances->map(function ($fin, $index) use (&$runningBalance, &$totalDebit, &$totalKredit) {
            $debit = 0;
            $kredit = 0;

            if ($fin->type === 'income') {
                $debit = (float) $fin->amount;
                $runningBalance += $debit;
                $totalDebit += $debit;
            } elseif ($fin->type === 'expense') {
                $kredit = (float) $fin->amount;
                $runningBalance -= $kredit;
                $totalKredit += $kredit;
            } elseif ($fin->type === 'allocation') {
                if ($fin->category === 'Proker ke Kas') {
                    $debit = (float) $fin->amount;
                    $runningBalance += $debit;
                    $totalDebit += $debit;
                } else {
                    $kredit = (float) $fin->amount;
                    $runningBalance -= $kredit;
                    $totalKredit += $kredit;
                }
            }

            return [
                'no' => $index + 1,
                'id' => $fin->id,
                'date' => $fin->date->format('Y-m-d'),
                'title' => $fin->title,
                'description' => $fin->description,
                'category' => $fin->category,
                'type' => $fin->type,
                'payment_method' => $fin->payment_method,
                'destination_payment_method' => $fin->destination_payment_method,
                'debit' => $debit,
                'kredit' => $kredit,
                'running_balance' => $runningBalance,
                'program_kerja' => $fin->programKerja ? $fin->programKerja->name : null,
                'creator' => $fin->creator ? $fin->creator->name : null,
                'receipt_path' => $fin->receipt_path ? Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($fin->receipt_path) : null,
            ];
        });

        return Inertia::render('financial-administration/BukuKasUmum', [
            'bkuItems' => $bkuItems,
            'summary' => [
                'total_debit' => $totalDebit,
                'total_kredit' => $totalKredit,
                'saldo_akhir' => $runningBalance,
            ],
            'filters' => [
                'start_date' => $startDate ?? '',
                'end_date' => $endDate ?? '',
            ],
            'poskoInfo' => [
                'name' => $context['host']?->name ?? 'Posko KKN',
                'ketua' => $context['ketua']?->name ?? 'Ketua Posko',
                'bendahara' => $context['bendahara']?->name ?? 'Bendahara Posko',
            ],
            'canWrite' => $context['canWrite'],
        ]);
    }

    /**
     * 2. Buku Penerimaan Dana
     */
    public function bukuPenerimaan(Request $request): Response
    {
        $context = $this->getHostContext($request);
        $hostId = $context['hostId'];

        $incomeQuery = Finance::with(['creator:id,name'])
            ->where('host_id', $hostId)
            ->where(function ($q) {
                $q->where('type', 'income')
                  ->orWhere(function ($q2) {
                      $q2->where('type', 'allocation')->where('category', 'Proker ke Kas');
                  });
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        $incomes = $incomeQuery->get()->map(function ($fin) {
            return [
                'id' => $fin->id,
                'date' => $fin->date->format('Y-m-d'),
                'title' => $fin->title,
                'description' => $fin->description,
                'category' => $fin->category ?? 'Pemasukan Umum',
                'payment_method' => $fin->payment_method,
                'amount' => (float) $fin->amount,
                'creator' => $fin->creator ? $fin->creator->name : null,
                'receipt_path' => $fin->receipt_path ? Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($fin->receipt_path) : null,
            ];
        });

        // Group by category summary
        $categorySummary = [];
        $methodSummary = ['Cash' => 0, 'SeaBank' => 0, 'DANA' => 0];

        foreach ($incomes as $inc) {
            $cat = $inc['category'];
            if (!isset($categorySummary[$cat])) {
                $categorySummary[$cat] = 0;
            }
            $categorySummary[$cat] += $inc['amount'];

            if (isset($methodSummary[$inc['payment_method']])) {
                $methodSummary[$inc['payment_method']] += $inc['amount'];
            }
        }

        return Inertia::render('financial-administration/BukuPenerimaanDana', [
            'incomes' => $incomes,
            'totalIncome' => (float) $incomes->sum('amount'),
            'categorySummary' => $categorySummary,
            'methodSummary' => $methodSummary,
            'poskoInfo' => [
                'name' => $context['host']?->name ?? 'Posko KKN',
            ],
            'canWrite' => $context['canWrite'],
        ]);
    }

    /**
     * 3. Buku Pengeluaran Dana
     */
    public function bukuPengeluaran(Request $request): Response
    {
        $context = $this->getHostContext($request);
        $hostId = $context['hostId'];

        $expenseQuery = Finance::with(['creator:id,name', 'programKerja:id,name'])
            ->where('host_id', $hostId)
            ->where(function ($q) {
                $q->where('type', 'expense')
                  ->orWhere(function ($q2) {
                      $q2->where('type', 'allocation')->where('category', 'Kas ke Proker');
                  });
            })
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        $expenses = $expenseQuery->get()->map(function ($fin) {
            return [
                'id' => $fin->id,
                'date' => $fin->date->format('Y-m-d'),
                'title' => $fin->title,
                'description' => $fin->description,
                'category' => $fin->category ?? 'Pengeluaran Umum',
                'payment_method' => $fin->payment_method,
                'amount' => (float) $fin->amount,
                'program_kerja' => $fin->programKerja ? $fin->programKerja->name : null,
                'creator' => $fin->creator ? $fin->creator->name : null,
                'receipt_path' => $fin->receipt_path ? Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($fin->receipt_path) : null,
            ];
        });

        // Category breakdown
        $categorySummary = [];
        $prokerSummary = [];
        $methodSummary = ['Cash' => 0, 'SeaBank' => 0, 'DANA' => 0];

        foreach ($expenses as $exp) {
            $cat = $exp['category'];
            if (!isset($categorySummary[$cat])) {
                $categorySummary[$cat] = 0;
            }
            $categorySummary[$cat] += $exp['amount'];

            $proker = $exp['program_kerja'] ?? 'Pengeluaran Umum / Operasional';
            if (!isset($prokerSummary[$proker])) {
                $prokerSummary[$proker] = 0;
            }
            $prokerSummary[$proker] += $exp['amount'];

            if (isset($methodSummary[$exp['payment_method']])) {
                $methodSummary[$exp['payment_method']] += $exp['amount'];
            }
        }

        return Inertia::render('financial-administration/BukuPengeluaranDana', [
            'expenses' => $expenses,
            'totalExpense' => (float) $expenses->sum('amount'),
            'categorySummary' => $categorySummary,
            'prokerSummary' => $prokerSummary,
            'methodSummary' => $methodSummary,
            'poskoInfo' => [
                'name' => $context['host']?->name ?? 'Posko KKN',
            ],
            'canWrite' => $context['canWrite'],
        ]);
    }

    /**
     * 4. Bukti Pembayaran
     */
    public function buktiPembayaran(Request $request): Response
    {
        $context = $this->getHostContext($request);
        $hostId = $context['hostId'];

        $finances = Finance::with(['creator:id,name'])
            ->where('host_id', $hostId)
            ->orderBy('date', 'desc')
            ->get();

        $items = $finances->map(function ($fin) {
            return [
                'id' => $fin->id,
                'title' => $fin->title,
                'type' => $fin->type,
                'amount' => (float) $fin->amount,
                'date' => $fin->date->format('Y-m-d'),
                'payment_method' => $fin->payment_method,
                'category' => $fin->category,
                'creator' => $fin->creator ? $fin->creator->name : null,
                'receipt_path' => $fin->receipt_path ? Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($fin->receipt_path) : null,
                'has_receipt' => !empty($fin->receipt_path),
            ];
        });

        $totalCount = $items->count();
        $hasReceiptCount = $items->where('has_receipt', true)->count();
        $missingReceiptCount = $totalCount - $hasReceiptCount;

        return Inertia::render('financial-administration/BuktiPembayaran', [
            'items' => $items,
            'metrics' => [
                'total_count' => $totalCount,
                'has_receipt_count' => $hasReceiptCount,
                'missing_receipt_count' => $missingReceiptCount,
            ],
            'canWrite' => $context['canWrite'],
        ]);
    }

    /**
     * 5. Kwitansi Digital
     */
    public function kwitansi(Request $request): Response
    {
        $context = $this->getHostContext($request);
        $hostId = $context['hostId'];

        $finances = Finance::where('host_id', $hostId)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($fin) {
                return [
                    'id' => $fin->id,
                    'title' => $fin->title,
                    'type' => $fin->type,
                    'amount' => (float) $fin->amount,
                    'date' => $fin->date->format('Y-m-d'),
                    'payment_method' => $fin->payment_method,
                    'category' => $fin->category,
                    'description' => $fin->description,
                ];
            });

        return Inertia::render('financial-administration/Kwitansi', [
            'finances' => $finances,
            'poskoInfo' => [
                'name' => $context['host']?->name ?? 'Posko KKN',
                'ketua' => $context['ketua']?->name ?? 'Ketua Posko',
                'bendahara' => $context['bendahara']?->name ?? 'Bendahara Posko',
            ],
            'canWrite' => $context['canWrite'],
        ]);
    }

    /**
     * 6. Nota Belanja
     */
    public function notaBelanja(Request $request): Response
    {
        $context = $this->getHostContext($request);
        $hostId = $context['hostId'];

        // Purchased logistics
        $logistics = Logistic::where('host_id', $hostId)
            ->where('source', 'purchase')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'type' => 'logistic',
                    'name' => $log->name,
                    'quantity' => (float) $log->quantity,
                    'unit' => $log->unit,
                    'unit_price' => (float) ($log->purchase_price ?? 0),
                    'total_price' => (float) (($log->purchase_price ?? 0) * $log->quantity),
                    'date' => $log->date ? $log->date->format('Y-m-d') : $log->created_at->format('Y-m-d'),
                    'notes' => $log->notes,
                    'receipt_path' => null,
                ];
            });

        // Purchased inventories
        $inventories = Inventory::where('host_id', $hostId)
            ->where('source', 'purchase')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'type' => 'inventory',
                    'name' => $inv->name,
                    'quantity' => (float) $inv->quantity,
                    'unit' => $inv->unit,
                    'unit_price' => (float) ($inv->purchase_price ?? 0),
                    'total_price' => (float) (($inv->purchase_price ?? 0) * $inv->quantity),
                    'date' => $inv->created_at->format('Y-m-d'),
                    'notes' => $inv->notes,
                    'receipt_path' => $inv->image_path ? Storage::disk('public')->url($inv->image_path) : null,
                ];
            });

        // General expense finances with receipts
        $expenses = Finance::where('host_id', $hostId)
            ->where('type', 'expense')
            ->whereNotNull('receipt_path')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($fin) {
                return [
                    'id' => $fin->id,
                    'type' => 'finance_expense',
                    'name' => $fin->title,
                    'quantity' => 1,
                    'unit' => 'paket',
                    'unit_price' => (float) $fin->amount,
                    'total_price' => (float) $fin->amount,
                    'date' => $fin->date->format('Y-m-d'),
                    'notes' => $fin->description,
                    'receipt_path' => Storage::disk(env('FILESYSTEM_DISK', 'public'))->url($fin->receipt_path),
                ];
            });

        $allNotes = $logistics->concat($inventories)->concat($expenses)->sortByDesc('date')->values();

        return Inertia::render('financial-administration/NotaBelanja', [
            'notaItems' => $allNotes,
            'totalShopping' => (float) $allNotes->sum('total_price'),
            'canWrite' => $context['canWrite'],
        ]);
    }

    /**
     * 7. LPJ Keuangan
     */
    public function lpjKeuangan(Request $request): Response
    {
        $context = $this->getHostContext($request);
        $hostId = $context['hostId'];

        $programKerjas = ProgramKerja::where('host_id', $hostId)
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($proker) use ($hostId) {
                $totalAllocated = Finance::where('program_kerja_id', $proker->id)
                    ->where('type', 'allocation')
                    ->where('category', 'Kas ke Proker')
                    ->sum('amount');

                $totalReturned = Finance::where('program_kerja_id', $proker->id)
                    ->where('type', 'allocation')
                    ->where('category', 'Proker ke Kas')
                    ->sum('amount');

                $totalSpent = Finance::where('program_kerja_id', $proker->id)
                    ->where('type', 'expense')
                    ->sum('amount');

                $netAllocated = $totalAllocated - $totalReturned;
                $prokerBalance = $netAllocated - $totalSpent;

                return [
                    'id' => $proker->id,
                    'name' => $proker->name,
                    'planned_budget' => (float) ($proker->budget ?? 0),
                    'total_allocated' => (float) $netAllocated,
                    'total_spent' => (float) $totalSpent,
                    'balance' => (float) $prokerBalance,
                ];
            });

        $totalIncome = Finance::where('host_id', $hostId)
            ->where('type', 'income')
            ->sum('amount');

        $totalGeneralExpense = Finance::where('host_id', $hostId)
            ->where('type', 'expense')
            ->whereNull('program_kerja_id')
            ->sum('amount');

        $totalProkerExpense = Finance::where('host_id', $hostId)
            ->where('type', 'expense')
            ->whereNotNull('program_kerja_id')
            ->sum('amount');

        $totalExpense = $totalGeneralExpense + $totalProkerExpense;
        $finalBalance = $totalIncome - $totalExpense;

        return Inertia::render('financial-administration/LpjKeuangan', [
            'summary' => [
                'total_income' => (float) $totalIncome,
                'total_general_expense' => (float) $totalGeneralExpense,
                'total_proker_expense' => (float) $totalProkerExpense,
                'total_expense' => (float) $totalExpense,
                'final_balance' => (float) $finalBalance,
            ],
            'prokerBreakdown' => $programKerjas,
            'poskoInfo' => [
                'name' => $context['host']?->name ?? 'Posko KKN',
                'ketua' => $context['ketua']?->name ?? 'Ketua Posko',
                'bendahara' => $context['bendahara']?->name ?? 'Bendahara Posko',
                'dpl' => $context['dpl']?->name ?? 'Dosen Pembimbing Lapangan (DPL)',
            ],
            'canWrite' => $context['canWrite'],
        ]);
    }
}
