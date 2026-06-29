<?php

namespace App\Http\Controllers\Payment;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SubscriptionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionRequestController extends Controller
{
    /**
     * Store a new QRIS subscription payment request.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Only allow QRIS payment method
        if (Setting::get('checkout_payment_method', 'tripay') !== 'qris_static') {
            return redirect()->back()->with('error', 'Metode pembayaran QRIS tidak aktif.');
        }

        // Check if already has active request (pending or approved)
        if (SubscriptionRequest::where('user_id', $user->id)->whereIn('status', ['pending', 'approved'])->exists()) {
            return redirect()->back()->with('error', 'Anda sudah mengajukan bukti pembayaran sebelumnya. Harap tunggu konfirmasi Admin.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'payment_proof' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('subscription-proofs', 'public');

            SubscriptionRequest::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'whatsapp' => $validated['whatsapp'],
                    'payment_proof' => $path,
                    'status' => 'pending',
                    'rejection_reason' => null,
                ]
            );

            ActivityLogHelper::log(
                'payment',
                'submit_qris_payment',
                "User submitted QRIS subscription payment proof for name {$validated['name']} ({$validated['email']})."
            );

            return redirect()->back()->with('success', 'Bukti pembayaran berhasil dikirim! Silakan tunggu konfirmasi Admin.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah bukti pembayaran.');
    }
}
