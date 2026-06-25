<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Models\Setting;
use App\Models\User;
use App\Services\TripayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TripayController extends Controller
{
    public function __construct(protected TripayService $tripayService) {}

    /**
     * Create a Tripay payment transaction.
     */
    public function createPayment(Request $request): JsonResponse
    {
        if (filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN)) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran dinonaktifkan karena preorder sedang aktif.',
            ], 403);
        }

        $user = $request->user();
        $amount = (int) Setting::get('package_price', 100000);
        $merchantRef = 'SUB-' . $user->id . '-' . time();

        $params = [
            'method' => $request->input('method', 'QRIS'),
            'merchant_ref' => $merchantRef,
            'amount' => $amount,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
        ];

        $transaction = $this->tripayService->createTransaction($params);

        if ($transaction) {
            return response()->json([
                'success' => true,
                'data' => $transaction,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal membuat pembayaran ke Tripay.',
        ], 500);
    }

    /**
     * Handle Tripay Webhook Callback.
     */
    public function handleCallback(Request $request): JsonResponse
    {
        $callbackSignature = $request->header('X-Callback-Signature');
        $rawJson = $request->getContent();

        if (empty($callbackSignature)) {
            return response()->json([
                'success' => false,
                'message' => 'Signature tidak ditemukan.',
            ], 400);
        }

        $privateKey = $this->tripayService->getPrivateKey();
        $localSignature = hash_hmac('sha256', $rawJson, $privateKey);

        if ($callbackSignature !== $localSignature) {
            return response()->json([
                'success' => false,
                'message' => 'Signature tidak valid.',
            ], 400);
        }

        // Decode JSON payload
        $data = json_decode($rawJson, true);
        if (!$data || !is_array($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Format payload tidak valid.',
            ], 400);
        }

        // Grab required fields
        $status = $data['status'] ?? '';
        $merchantRef = $data['merchant_ref'] ?? '';
        $reference = $data['reference'] ?? '';
        $totalAmount = $data['total_amount'] ?? 0;

        Log::info("Callback Tripay diterima: Ref={$merchantRef}, Status={$status}, Amount={$totalAmount}");

        // Only handle paid status
        if ($status === 'PAID') {
            $parts = explode('-', $merchantRef);
            if (count($parts) >= 2 && $parts[0] === 'SUB') {
                $userId = $parts[1];
                $user = User::find($userId);

                if ($user) {
                    // Update user role to host and extend active subscription
                    $user->update([
                        'role' => 'host',
                        'subscription_expires_at' => now()->addDays(40),
                    ]);

                    ActivityLogHelper::log(
                        'payment',
                        'payment_webhook_success_tripay',
                        "Webhook Tripay mengonfirmasi pembayaran Rp " . number_format($totalAmount) . " untuk referensi {$merchantRef}. User {$user->name} role di-upgrade ke host.",
                        $user
                    );

                    return response()->json([
                        'success' => true,
                        'message' => 'Pembayaran berhasil diproses, posko aktif.',
                    ]);
                } else {
                    Log::error("User dengan ID {$userId} tidak ditemukan dari callback Tripay.");
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Callback diterima namun tidak memicu aksi.',
        ]);
    }
}
