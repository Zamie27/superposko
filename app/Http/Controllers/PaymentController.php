<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SubscriptionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    /**
     * Show the subscription checkout page for normal users.
     */
    public function showCheckoutPage(Request $request): Response|RedirectResponse
    {
        if (filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN)) {
            return redirect()->route('dashboard');
        }

        $user = $request->user();
        $existingRequest = SubscriptionRequest::where('user_id', $user->id)->first();

        // Check if user has an active unpaid Tripay transaction in Cache
        $activeTripayUrl = null;
        $activeTripayRef = null;
        
        $cachedRef = \Illuminate\Support\Facades\Cache::get('user_tripay_ref_' . $user->id);
        $cachedUrl = \Illuminate\Support\Facades\Cache::get('user_tripay_url_' . $user->id);
        
        $tripayService = app(\App\Services\TripayService::class);

        if ($cachedRef && $cachedUrl) {
            $detail = $tripayService->getTransactionDetail($cachedRef);
            if ($detail && isset($detail['status']) && strtoupper($detail['status']) === 'UNPAID') {
                $activeTripayUrl = $cachedUrl;
                $activeTripayRef = $cachedRef;
            } else {
                \Illuminate\Support\Facades\Cache::forget('user_tripay_ref_' . $user->id);
                \Illuminate\Support\Facades\Cache::forget('user_tripay_url_' . $user->id);
            }
        }

        // Fetch active payment channels dynamically from Tripay and filter out DANA
        $channels = collect($tripayService->getPaymentChannels() ?? [])
            ->reject(fn($channel) => ($channel['code'] ?? '') === 'DANA')
            ->values()
            ->toArray();

        return Inertia::render('payment/Checkout', [
            'packagePrice' => (int) Setting::get('package_price', 100000),
            'packageStrikePrice' => (int) Setting::get('package_strike_price', 150000),
            'checkoutPaymentMethod' => Setting::get('checkout_payment_method', 'tripay'),
            'staticQrisUrl' => Setting::get('static_qris_path') ? asset('storage/' . Setting::get('static_qris_path')) : '/images/qris.jpg',
            'existingRequest' => $existingRequest ? [
                'status' => $existingRequest->status,
                'rejection_reason' => $existingRequest->rejection_reason,
                'created_at' => $existingRequest->created_at?->toIso8601String(),
            ] : null,
            'tripayChannels' => $channels,
            'activeTripayUrl' => $activeTripayUrl,
            'activeTripayRef' => $activeTripayRef,
        ]);
    }

    /**
     * Show the test payment page for admin.
     */
    public function showTestPage(): Response
    {
        $mockupChannels = [
            [
                'code' => 'QRIS',
                'name' => 'QRIS',
                'icon_url' => 'https://tripay.co.id/images/payment-channel/qris.png',
                'group' => 'E-Wallet',
            ],
            [
                'code' => 'MYBVALINK',
                'name' => 'Other Bank Virtual Account',
                'icon_url' => 'https://tripay.co.id/images/payment-channel/alto.png',
                'group' => 'Virtual Account',
            ],
        ];

        return Inertia::render('payment/TestPayment', [
            'preorderPromoActive' => filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN),
            'checkoutPaymentMethod' => Setting::get('checkout_payment_method', 'tripay'),
            'preorderPrice' => (int) Setting::get('preorder_price', 50000),
            'preorderStrikePrice' => (int) Setting::get('preorder_strike_price', 100000),
            'packagePrice' => (int) Setting::get('package_price', 100000),
            'packageStrikePrice' => (int) Setting::get('package_strike_price', 150000),
            'staticQrisUrl' => Setting::get('static_qris_path') ? asset('storage/' . Setting::get('static_qris_path')) : '/images/qris.jpg',
            'tripayChannels' => $mockupChannels,
        ]);
    }
}
