<?php

namespace App\Http\Controllers\Preorder;

use App\Http\Controllers\Controller;
use App\Models\Preorder;
use App\Models\Setting;
// phpcs:ignore
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserPreorderController extends Controller
{
    /**
     * Show the user preorder page.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        if (! filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN)) {
            return redirect()->route('dashboard');
        }

        $user = $request->user();

        // Check if user already submitted a preorder
        $existingPreorder = Preorder::where('user_id', $user->id)->first();

        return Inertia::render('preorder/Index', [
            'preorderPrice' => (int) Setting::get('preorder_price', 50000),
            'preorderStrikePrice' => (int) Setting::get('preorder_strike_price', 100000),
            'existingPreorder' => $existingPreorder ? [
                'status' => $existingPreorder->status,
                'created_at' => $existingPreorder->created_at?->toIso8601String(),
            ] : null,
        ]);
    }

    /**
     * Store preorder request.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Check if already has active preorder (pending or approved)
        if (Preorder::where('user_id', $user->id)->whereIn('status', ['pending', 'approved'])->exists()) {
            return redirect()->back()->with('error', 'Anda sudah mengajukan preorder sebelumnya.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'payment_proof' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('preorders', 'public');

            Preorder::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'whatsapp' => $validated['whatsapp'],
                    'payment_proof' => $path,
                    'status' => 'pending',
                ]
            );

            return redirect()->back()->with('success', 'Form Preorder berhasil dikirim! Silakan tunggu konfirmasi Admin.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah bukti pembayaran.');
    }
}
