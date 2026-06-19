<?php

namespace App\Http\Controllers;

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
     * Create snap token from Midtrans.
     */
    public function createSnapToken(Request $request): JsonResponse
    {
        $user = $request->user();

        $orderId = 'TEST-'.time().'-'.rand(1000, 9999);
        $amount = 50000; // Rp 50.000

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
}
