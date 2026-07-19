<?php

namespace App\Http\Controllers;

use App\Models\CashDue;
use App\Models\Finance;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class CashDueController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        $hostId = $user->host_id ?? $user->id;

        // Get host settings
        $host = User::find($hostId);
        $startDate = $host && $host->cash_dues_start_date ? $host->cash_dues_start_date->format('Y-m-d') : null;

        // Is the user allowed to edit?
        $canEdit = in_array($user->role, ['ketua', 'wakil', 'sekretaris', 'bendahara']);

        // Get all members for this host
        $members = User::where('host_id', $hostId)->orWhere('id', $hostId)->orderBy('name', 'asc')->get();

        // Get all cash dues
        $cashDues = CashDue::with(['finance', 'creator'])
            ->where('host_id', $hostId)
            ->get();

        return Inertia::render('cash-due/Index', [
            'members' => $members,
            'cashDues' => $cashDues,
            'canEdit' => $canEdit,
            'startDate' => $startDate,
        ]);
    }

    public function updateSettings(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $user = auth()->user();
        if (!in_array($user->role, ['ketua', 'wakil', 'sekretaris', 'bendahara'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'cash_dues_start_date' => ['nullable', 'date'],
        ]);

        $hostId = $user->host_id ?? $user->id;
        $host = User::find($hostId);
        if ($host) {
            $host->update([
                'cash_dues_start_date' => $request->cash_dues_start_date,
            ]);
        }

        return back()->with('success', 'Pengaturan tanggal mulai kas berhasil disimpan.');
    }

    public function store(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $user = auth()->user();
        if (!in_array($user->role, ['ketua', 'wakil', 'sekretaris', 'bendahara'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'week_number' => ['required', 'integer', 'min:1', 'max:52'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'in:Cash,SeaBank,DANA'],
            'date' => ['nullable', 'date'],
        ]);

        // Ensure amount is float/int regardless of frontend string formats
        $amount = (float) $request->amount;

        $hostId = $user->host_id ?? $user->id;
        $member = User::find($request->user_id);

        // Check if already paid
        $exists = CashDue::where('host_id', $hostId)
            ->where('user_id', $request->user_id)
            ->where('week_number', $request->week_number)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Iuran minggu ini sudah dibayar oleh anggota tersebut.');
        }

        DB::beginTransaction();

        try {
            // 1. Create Finance record (Income)
            $finance = Finance::create([
                'host_id' => $hostId,
                'type' => 'income',
                'amount' => $amount,
                'title' => 'Uang Kas Minggu ' . $request->week_number . ' - ' . $member->name,
                'category' => 'Uang Kas',
                'payment_method' => $request->payment_method,
                'date' => $request->date ?? now(),
                'created_by' => $user->id,
            ]);

            // 2. Create CashDue record
            CashDue::create([
                'user_id' => $request->user_id,
                'host_id' => $hostId,
                'week_number' => $request->week_number,
                'amount' => $amount,
                'finance_id' => $finance->id,
                'created_by' => $user->id,
            ]);

            DB::commit();

            return back()->with('success', 'Pembayaran kas berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mencatat pembayaran: ' . $e->getMessage());
        }
    }

    public function destroy(CashDue $cashDue): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $user = auth()->user();
        if (!in_array($user->role, ['ketua', 'wakil', 'sekretaris', 'bendahara'])) {
            abort(403, 'Unauthorized action.');
        }

        $hostId = $user->host_id ?? $user->id;
        if ($cashDue->host_id !== $hostId) {
            abort(403, 'Unauthorized action.');
        }

        DB::beginTransaction();

        try {
            $financeId = $cashDue->finance_id;
            
            // Delete cash due first to avoid constraint issues if any
            $cashDue->delete();

            // Delete associated finance record
            if ($financeId) {
                Finance::where('id', $financeId)->delete();
            }

            DB::commit();

            return back()->with('success', 'Catatan iuran kas berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus catatan: ' . $e->getMessage());
        }
    }
}
