<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function __construct(protected MidtransService $midtransService) {}

    /**
     * Show the test payment page.
     */
    public function showTestPage(): Response
    {
        return Inertia::render('payment/TestPayment', [
            'midtransClientKey' => config('services.midtrans.client_key') ?? '',
            'isProduction' => filter_var(config('services.midtrans.is_production', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    /**
     * Show the subscription checkout page for normal users.
     */
    public function showCheckoutPage(): Response|\Illuminate\Http\RedirectResponse
    {
        if (filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN)) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('payment/Checkout', [
            'midtransClientKey' => config('services.midtrans.client_key') ?? '',
            'isProduction' => filter_var(config('services.midtrans.is_production', false), FILTER_VALIDATE_BOOLEAN),
            'packagePrice' => (int) Setting::get('package_price', 100000),
            'packageStrikePrice' => (int) Setting::get('package_strike_price', 150000),
        ]);
    }

    /**
     * Create snap token from Midtrans.
     */
    public function createSnapToken(Request $request): JsonResponse
    {
        if (filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN)) {
            return response()->json([
                'success' => false,
                'message' => 'Halaman Beli Langganan dinonaktifkan karena preorder sedang aktif.',
            ], 403);
        }

        $user = $request->user();

        $prefix = $user->role === 'admin' ? 'TEST-' : 'SUB-';
        $orderId = $prefix.time().'-'.rand(1000, 9999);
        $amount = (int) Setting::get('package_price', 100000);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'credit_card' => [
                'secure' => true,
            ],
        ];

        $result = $this->midtransService->createSnapTransaction($params);

        if ($result && isset($result['token'])) {
            return response()->json([
                'success' => true,
                'token' => $result['token'],
                'redirect_url' => $result['redirect_url'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal membuat transaksi ke Midtrans. Pastikan Server Key Anda benar.',
        ], 500);
    }

    /**
     * Verify payment status and activate subscription.
     */
    public function handlePaymentSuccess(Request $request): JsonResponse
    {
        if (filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN)) {
            return response()->json([
                'success' => false,
                'message' => 'Halaman Beli Langganan dinonaktifkan karena preorder sedang aktif.',
            ], 403);
        }

        $request->validate([
            'order_id' => ['required', 'string'],
        ]);

        $orderId = $request->input('order_id');
        $statusData = $this->midtransService->getTransactionStatus($orderId);

        if (! $statusData) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi status pembayaran ke Midtrans.',
            ], 400);
        }

        $transactionStatus = $statusData['transaction_status'] ?? '';

        if (in_array($transactionStatus, ['settlement', 'capture'])) {
            $user = $request->user();
            $user->update([
                'role' => 'host',
                'subscription_expires_at' => now()->addDays(40),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi! Akun Anda telah diaktifkan menjadi Host.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Status pembayaran tidak valid: '.$transactionStatus,
        ], 400);
    }
}

