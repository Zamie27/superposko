<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AdminSubscriptionRequestController extends Controller
{
    /**
     * Display a listing of the QRIS subscription payment requests.
     */
    public function index(): Response
    {
        $requests = SubscriptionRequest::with('user:id,name,email')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'email' => $item->email,
                    'whatsapp' => $item->whatsapp,
                    'payment_proof' => Storage::url($item->payment_proof),
                    'status' => $item->status,
                    'rejection_reason' => $item->rejection_reason,
                    'created_at' => $item->created_at?->toIso8601String(),
                    'user_id' => $item->user_id,
                ];
            });

        return Inertia::render('admin/SubscriptionRequests', [
            'subscriptionRequests' => $requests,
        ]);
    }

    /**
     * Approve QRIS subscription request and activate user as ketua.
     */
    public function approve(SubscriptionRequest $subscriptionRequest): JsonResponse
    {
        $user = $subscriptionRequest->user;
        if ($user) {
            $user->update([
                'role' => 'ketua',
                'subscription_expires_at' => now()->addDays(40),
            ]);
        }

        $subscriptionRequest->update([
            'status' => 'approved',
            'rejection_reason' => null,
        ]);

        ActivityLogHelper::log(
            'payment',
            'approve_qris_payment',
            "Admin approved QRIS subscription request #{$subscriptionRequest->id} from {$subscriptionRequest->name} ({$subscriptionRequest->email}). User role upgraded to ketua."
        );

        return response()->json([
            'success' => true,
            'message' => "Pembayaran dari {$subscriptionRequest->name} disetujui. Akun telah aktif sebagai Ketua.",
        ]);
    }

    /**
     * Reject QRIS subscription request.
     */
    public function reject(SubscriptionRequest $subscriptionRequest, Request $request): JsonResponse
    {
        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $subscriptionRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        ActivityLogHelper::log(
            'payment',
            'reject_qris_payment',
            "Admin rejected QRIS subscription request #{$subscriptionRequest->id} from {$subscriptionRequest->name} ({$subscriptionRequest->email})."
        );

        return response()->json([
            'success' => true,
            'message' => "Pengajuan dari {$subscriptionRequest->name} ditolak.",
        ]);
    }
}
