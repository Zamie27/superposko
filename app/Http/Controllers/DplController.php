<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DplController extends Controller
{
    /**
     * Switch DPL active posko view.
     */
    public function switchPosko(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!in_array($user->role, ['dpl', 'admin'])) {
            abort(403, 'Aksi ini hanya diperbolehkan untuk DPL atau Admin.');
        }

        $validated = $request->validate([
            'host_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        if (is_null($validated['host_id'])) {
            $request->session()->forget('dpl_active_host_id');
            
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Berhasil keluar dari pemantauan posko.');
            }
            
            return redirect()->route('dashboard')->with('success', 'Berhasil keluar dari pemantauan posko.');
        }

        $host = User::findOrFail($validated['host_id']);
        if (! is_null($host->host_id)) {
            abort(400, 'User target bukan merupakan pemilik posko.');
        }

        if ($user->role === 'dpl') {
            $isApproved = \App\Models\DplMonitoring::where('dpl_id', $user->id)
                ->where('host_id', $validated['host_id'])
                ->where('status', 'approved')
                ->exists();

            if (! $isApproved) {
                abort(403, 'Anda belum disetujui untuk memantau posko ini.');
            }
        }

        $request->session()->put('dpl_active_host_id', $validated['host_id']);

        return back()->with('success', "Berhasil beralih ke posko kelompok: {$host->name}.");
    }

    /**
     * Request to monitor a posko group.
     */
    public function requestMonitoring(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->role !== 'dpl') {
            abort(403, 'Aksi ini hanya diperbolehkan untuk DPL.');
        }

        $validated = $request->validate([
            'host_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $host = User::findOrFail($validated['host_id']);
        if (! is_null($host->host_id)) {
            return back()->withErrors(['host_id' => 'Target user bukan pemilik posko (Ketua).']);
        }

        $exists = \App\Models\DplMonitoring::where('dpl_id', $user->id)
            ->where('host_id', $validated['host_id'])
            ->first();

        if ($exists) {
            if ($exists->status === 'approved') {
                return back()->withErrors(['host_id' => 'Anda sudah memantau kelompok ini.']);
            }
            return back()->withErrors(['host_id' => 'Permintaan pemantauan kelompok ini sedang pending menunggu konfirmasi.']);
        }

        \App\Models\DplMonitoring::create([
            'dpl_id' => $user->id,
            'host_id' => $validated['host_id'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Permintaan akses pemantauan berhasil dikirim ke kelompok posko.');
    }
}
