<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Models\Setting;
use App\Models\User;
use App\Services\TripayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TripayController extends Controller
{
    public function __construct(protected TripayService $tripayService) {}

    /**
     * Create a Tripay payment transaction.
     */
    public function createPayment(Request $request): JsonResponse
    {
        $type = $request->input('type', 'subscription'); // 'subscription' or 'preorder'

        if ($type === 'subscription' && filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN)) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran dinonaktifkan karena preorder sedang aktif.',
            ], 403);
        }

        if ($type === 'preorder' && !filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN)) {
            return response()->json([
                'success' => false,
                'message' => 'Preorder dinonaktifkan karena SaaS utama sedang aktif.',
            ], 403);
        }

        $user = $request->user();

        if ($type === 'preorder') {
            $validated = $request->validate([
                'name' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'whatsapp' => ['nullable', 'string', 'max:20'],
                'method' => ['nullable', 'string'],
            ]);
            $customerName = !empty($validated['name']) ? $validated['name'] : $user->name;
            $customerEmail = !empty($validated['email']) ? $validated['email'] : $user->email;
            $customerWhatsapp = !empty($validated['whatsapp']) ? $validated['whatsapp'] : ($user->phone ?? '0000000000');

            $amount = (int) Setting::get('preorder_price', 50000);
            $merchantRef = 'PRE-' . $user->id . '-' . time();
        } else {
            $amount = (int) Setting::get('package_price', 100000);
            $merchantRef = 'SUB-' . $user->id . '-' . time();
        }

        // Get active payment channels from Tripay
        $channels = $this->tripayService->getPaymentChannels();

        if (empty($channels)) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil channel pembayaran aktif dari Tripay. Pastikan API key dan status merchant di Tripay sudah benar.',
            ], 500);
        }

        // Extract active channel codes and filter out DANA
        $activeCodes = array_map(function ($c) {
            return $c['code'] ?? '';
        }, $channels);
        $activeCodes = array_values(array_filter($activeCodes, fn($code) => $code && $code !== 'DANA'));

        // Determine method: check request, default to QRIS, or fallback to the first active channel
        $method = $request->input('method');
        if (empty($method)) {
            if (in_array('QRIS', $activeCodes)) {
                $method = 'QRIS';
            } else {
                $method = $activeCodes[0] ?? 'QRIS';
            }
        }

        $params = [
            'method' => $method,
            'merchant_ref' => $merchantRef,
            'amount' => $amount,
            'customer_name' => $type === 'preorder' ? $customerName : $user->name,
            'customer_email' => $type === 'preorder' ? $customerEmail : $user->email,
        ];

        $transaction = $this->tripayService->createTransaction($params);

        if ($transaction) {
            if ($type === 'preorder') {
                // Cache the preorder reference and url
                \Illuminate\Support\Facades\Cache::put('user_preorder_tripay_ref_' . $user->id, $transaction['reference'], now()->addHours(24));
                \Illuminate\Support\Facades\Cache::put('user_preorder_tripay_url_' . $user->id, $transaction['checkout_url'], now()->addHours(24));

                // Save preorder to database with status 'pending'
                \App\Models\Preorder::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $customerName,
                        'email' => $customerEmail,
                        'whatsapp' => $customerWhatsapp,
                        'payment_proof' => 'tripay',
                        'status' => 'pending',
                    ]
                );
            } else {
                // Cache the subscription reference and url
                \Illuminate\Support\Facades\Cache::put('user_tripay_ref_' . $user->id, $transaction['reference'], now()->addHours(24));
                \Illuminate\Support\Facades\Cache::put('user_tripay_url_' . $user->id, $transaction['checkout_url'], now()->addHours(24));
            }

            return response()->json([
                'success' => true,
                'data' => $transaction,
            ]);
        }

        $activeChannelsStr = implode(', ', $activeCodes);
        return response()->json([
            'success' => false,
            'message' => "Gagal membuat pembayaran ke Tripay menggunakan {$method}. Channel aktif Anda di Tripay: [{$activeChannelsStr}].",
        ], 500);
    }

    /**
     * Cancel/clear current active cached payment.
     */
    public function cancelCurrentPayment(Request $request): JsonResponse
    {
        $user = $request->user();
        $type = $request->input('type', 'subscription');

        if ($type === 'preorder') {
            \Illuminate\Support\Facades\Cache::forget('user_preorder_tripay_ref_' . $user->id);
            \Illuminate\Support\Facades\Cache::forget('user_preorder_tripay_url_' . $user->id);

            // Delete pending preorder if it was paid via tripay to allow choosing again
            $preorder = \App\Models\Preorder::where('user_id', $user->id)->first();
            if ($preorder && $preorder->status === 'pending' && $preorder->payment_proof === 'tripay') {
                $preorder->delete();
            }
        } else {
            \Illuminate\Support\Facades\Cache::forget('user_tripay_ref_' . $user->id);
            \Illuminate\Support\Facades\Cache::forget('user_tripay_url_' . $user->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaksi sebelumnya berhasil dibatalkan.',
        ]);
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
            if (count($parts) >= 2) {
                $prefix = $parts[0];
                $userId = $parts[1];
                $user = User::find($userId);

                if ($user) {
                    if ($prefix === 'SUB') {
                        // Update user role to ketua and extend active subscription
                        $user->update([
                            'role' => 'ketua',
                            'subscription_expires_at' => now()->addDays(40),
                        ]);

                        ActivityLogHelper::log(
                            'payment',
                            'payment_webhook_success_tripay',
                            "Webhook Tripay mengonfirmasi pembayaran Rp " . number_format($totalAmount) . " untuk referensi {$merchantRef}. User {$user->name} role di-upgrade ke ketua.",
                            $user
                        );

                        return response()->json([
                            'success' => true,
                            'message' => 'Pembayaran berhasil diproses, posko aktif.',
                        ]);
                    } elseif ($prefix === 'PRE') {
                        // Find preorder record
                        $preorder = \App\Models\Preorder::where('user_id', $user->id)->first();

                        if ($preorder) {
                            $preorder->update(['status' => 'approved']);
                        } else {
                            \App\Models\Preorder::create([
                                'user_id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'whatsapp' => '0000000000',
                                'payment_proof' => 'tripay',
                                'status' => 'approved',
                            ]);
                        }

                        // Promote user to ketua as well on approved preorder!
                        $user->update([
                            'role' => 'ketua',
                            'subscription_expires_at' => now()->addDays(40),
                        ]);

                        ActivityLogHelper::log(
                            'preorder',
                            'preorder_webhook_success_tripay',
                            "Webhook Tripay mengonfirmasi preorder Rp " . number_format($totalAmount) . " untuk referensi {$merchantRef}. Preorder disetujui, user {$user->name} role di-upgrade ke ketua.",
                            $user
                        );

                        return response()->json([
                            'success' => true,
                            'message' => 'Preorder berhasil diproses, posko aktif.',
                        ]);
                    }
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

    /**
     * Handle user redirect back from Tripay payment.
     */
    public function handleReturn(Request $request): \Inertia\Response|\Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        // Check if there is cached preorder transaction
        $preorderRef = \Illuminate\Support\Facades\Cache::get('user_preorder_tripay_ref_' . $user->id);
        if ($preorderRef) {
            $detail = $this->tripayService->getTransactionDetail($preorderRef);
            if ($detail && isset($detail['status'])) {
                $statusStr = strtoupper($detail['status']);
                if ($statusStr === 'PAID') {
                    // Preorder is paid! Approve it and update role to host immediately
                    $preorder = \App\Models\Preorder::where('user_id', $user->id)->first();
                    if ($preorder) {
                        $preorder->update(['status' => 'approved']);
                    } else {
                        \App\Models\Preorder::create([
                            'user_id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'whatsapp' => '0000000000',
                            'payment_proof' => 'tripay',
                            'status' => 'approved',
                        ]);
                    }

                    $user->update([
                        'role' => 'ketua',
                        'subscription_expires_at' => now()->addDays(40),
                    ]);

                    \Illuminate\Support\Facades\Cache::forget('user_preorder_tripay_ref_' . $user->id);
                    \Illuminate\Support\Facades\Cache::forget('user_preorder_tripay_url_' . $user->id);

                    return Inertia::render('payment/Success', [
                        'status' => 'success',
                        'reference' => $detail['reference'] ?? $preorderRef,
                        'amount' => (int) ($detail['amount'] ?? Setting::get('preorder_price', 50000)),
                        'paymentMethod' => $detail['payment_name'] ?? 'Tripay',
                    ]);
                } elseif ($statusStr === 'UNPAID') {
                    return Inertia::render('payment/Success', [
                        'status' => 'pending',
                        'reference' => $detail['reference'] ?? $preorderRef,
                        'amount' => (int) ($detail['amount'] ?? Setting::get('preorder_price', 50000)),
                        'paymentMethod' => $detail['payment_name'] ?? 'Tripay',
                    ]);
                } else {
                    \Illuminate\Support\Facades\Cache::forget('user_preorder_tripay_ref_' . $user->id);
                    \Illuminate\Support\Facades\Cache::forget('user_preorder_tripay_url_' . $user->id);

                    return Inertia::render('payment/Success', [
                        'status' => 'failed',
                        'reference' => $detail['reference'] ?? $preorderRef,
                        'amount' => (int) ($detail['amount'] ?? Setting::get('preorder_price', 50000)),
                        'paymentMethod' => $detail['payment_name'] ?? 'Tripay',
                        'errorMessage' => 'Pembayaran dibatalkan atau kedaluwarsa.',
                    ]);
                }
            }
        }

        // Check if there is cached subscription transaction
        $subRef = \Illuminate\Support\Facades\Cache::get('user_tripay_ref_' . $user->id);
        if ($subRef) {
            $detail = $this->tripayService->getTransactionDetail($subRef);
            if ($detail && isset($detail['status'])) {
                $statusStr = strtoupper($detail['status']);
                if ($statusStr === 'PAID') {
                    // Subscription is paid! Update role to ketua immediately
                    $user->update([
                        'role' => 'ketua',
                        'subscription_expires_at' => now()->addDays(40),
                    ]);

                    \Illuminate\Support\Facades\Cache::forget('user_tripay_ref_' . $user->id);
                    \Illuminate\Support\Facades\Cache::forget('user_tripay_url_' . $user->id);

                    return Inertia::render('payment/Success', [
                        'status' => 'success',
                        'reference' => $detail['reference'] ?? $subRef,
                        'amount' => (int) ($detail['amount'] ?? Setting::get('package_price', 100000)),
                        'paymentMethod' => $detail['payment_name'] ?? 'Tripay',
                    ]);
                } elseif ($statusStr === 'UNPAID') {
                    return Inertia::render('payment/Success', [
                        'status' => 'pending',
                        'reference' => $detail['reference'] ?? $subRef,
                        'amount' => (int) ($detail['amount'] ?? Setting::get('package_price', 100000)),
                        'paymentMethod' => $detail['payment_name'] ?? 'Tripay',
                    ]);
                } else {
                    \Illuminate\Support\Facades\Cache::forget('user_tripay_ref_' . $user->id);
                    \Illuminate\Support\Facades\Cache::forget('user_tripay_url_' . $user->id);

                    return Inertia::render('payment/Success', [
                        'status' => 'failed',
                        'reference' => $detail['reference'] ?? $subRef,
                        'amount' => (int) ($detail['amount'] ?? Setting::get('package_price', 100000)),
                        'paymentMethod' => $detail['payment_name'] ?? 'Tripay',
                        'errorMessage' => 'Pembayaran dibatalkan atau kedaluwarsa.',
                    ]);
                }
            }
        }

        // If user returns and is already owner (e.g. webhook processed first)
        if (is_null($user->host_id) && $user->subscription_expires_at?->isFuture()) {
            return Inertia::render('payment/Success', [
                'status' => 'success',
                'reference' => 'PAID',
                'amount' => filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN)
                    ? (int) Setting::get('preorder_price', 50000)
                    : (int) Setting::get('package_price', 100000),
                'paymentMethod' => 'Tripay',
            ]);
        }

        // Otherwise redirect back to preorder/payment page with info
        if (filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN)) {
            return redirect()->route('preorder.index')->with('info', 'Status transaksi Anda sedang diproses. Silakan hubungi admin jika saldo terpotong namun status belum berubah.');
        }

        return redirect()->route('payment.index')->with('info', 'Status transaksi Anda sedang diproses. Silakan hubungi admin jika saldo terpotong namun status belum berubah.');
    }

    /**
     * Check current active transaction status.
     */
    public function checkStatus(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1. If user is already owner with active subscription, they are paid
        if (is_null($user->host_id) && $user->subscription_expires_at?->isFuture()) {
            return response()->json([
                'success' => true,
                'status' => 'PAID',
            ]);
        }

        // 2. Check cached preorder reference
        $preorderRef = \Illuminate\Support\Facades\Cache::get('user_preorder_tripay_ref_' . $user->id);
        if ($preorderRef) {
            $detail = $this->tripayService->getTransactionDetail($preorderRef);
            if ($detail && isset($detail['status'])) {
                $status = strtoupper($detail['status']);
                if ($status === 'PAID') {
                    // Update database immediately
                    $preorder = \App\Models\Preorder::where('user_id', $user->id)->first();
                    if ($preorder) {
                        $preorder->update(['status' => 'approved']);
                    }
                    $user->update([
                        'role' => 'ketua',
                        'subscription_expires_at' => now()->addDays(40),
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'status' => $status,
                ]);
            }
        }

        // 3. Check cached subscription reference
        $subRef = \Illuminate\Support\Facades\Cache::get('user_tripay_ref_' . $user->id);
        if ($subRef) {
            $detail = $this->tripayService->getTransactionDetail($subRef);
            if ($detail && isset($detail['status'])) {
                $status = strtoupper($detail['status']);
                if ($status === 'PAID') {
                    $user->update([
                        'role' => 'ketua',
                        'subscription_expires_at' => now()->addDays(40),
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'status' => $status,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'status' => 'UNPAID',
        ]);
    }
}
