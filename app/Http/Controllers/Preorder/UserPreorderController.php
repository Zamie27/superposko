<?php

namespace App\Http\Controllers\Preorder;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\Preorder;
// phpcs:ignore
use App\Models\Setting;
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

        // Check if there is a cached unpaid Tripay preorder transaction
        $activeTripayUrl = null;
        $activeTripayRef = null;
        
        $cachedRef = \Illuminate\Support\Facades\Cache::get('user_preorder_tripay_ref_' . $user->id);
        $cachedUrl = \Illuminate\Support\Facades\Cache::get('user_preorder_tripay_url_' . $user->id);
        
        $tripayService = app(\App\Services\TripayService::class);
        
        if ($cachedRef && $cachedUrl) {
            $detail = $tripayService->getTransactionDetail($cachedRef);
            if ($detail && isset($detail['status']) && strtoupper($detail['status']) === 'UNPAID') {
                $activeTripayUrl = $cachedUrl;
                $activeTripayRef = $cachedRef;
            } else {
                \Illuminate\Support\Facades\Cache::forget('user_preorder_tripay_ref_' . $user->id);
                \Illuminate\Support\Facades\Cache::forget('user_preorder_tripay_url_' . $user->id);
            }
        }

        // Fetch active payment channels dynamically from Tripay and filter out DANA
        $channels = collect($tripayService->getPaymentChannels() ?? [])
            ->reject(fn($channel) => ($channel['code'] ?? '') === 'DANA')
            ->values()
            ->toArray();

        return Inertia::render('preorder/Index', [
            'preorderPrice' => (int) Setting::get('preorder_price', 50000),
            'preorderStrikePrice' => (int) Setting::get('preorder_strike_price', 100000),
            'staticQrisUrl' => Setting::get('static_qris_path') ? asset('storage/' . Setting::get('static_qris_path')) : '/images/qris.jpg',
            'existingPreorder' => $existingPreorder ? [
                'status' => $existingPreorder->status,
                'created_at' => $existingPreorder->created_at?->toIso8601String(),
            ] : null,
            'tripayChannels' => $channels,
            'activeTripayUrl' => $activeTripayUrl,
            'activeTripayRef' => $activeTripayRef,
            'checkoutPaymentMethod' => Setting::get('checkout_payment_method', 'tripay'),
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

            ActivityLogHelper::log(
                'preorder',
                'submit_preorder',
                "User submitted preorder request with name {$validated['name']} and email {$validated['email']}."
            );

            return redirect()->back()->with('success', 'Form Preorder berhasil dikirim! Silakan tunggu konfirmasi Admin.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah bukti pembayaran.');
    }
}
