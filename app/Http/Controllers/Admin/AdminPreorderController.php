<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Preorder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AdminPreorderController extends Controller
{
    /**
     * Display a listing of the preorder requests.
     */
    public function index(): Response
    {
        $preorders = Preorder::with('user:id,name,email')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($preorder) {
                return [
                    'id' => $preorder->id,
                    'name' => $preorder->name,
                    'email' => $preorder->email,
                    'whatsapp' => $preorder->whatsapp,
                    'payment_proof' => Storage::url($preorder->payment_proof),
                    'status' => $preorder->status,
                    'created_at' => $preorder->created_at?->toIso8601String(),
                    'user_id' => $preorder->user_id,
                ];
            });

        return Inertia::render('admin/Preorders', [
            'preorders' => $preorders,
        ]);
    }

    /**
     * Approve preorder and promote user to host.
     */
    public function approve(Preorder $preorder): JsonResponse
    {
        $user = $preorder->user;
        if ($user) {
            $user->update([
                'role' => 'host',
                'subscription_expires_at' => now()->addDays(40),
            ]);
        }

        $preorder->update(['status' => 'approved']);

        \App\Helpers\ActivityLogHelper::log(
            'preorder',
            'approve_preorder',
            "Admin approved preorder request #{$preorder->id} from user {$preorder->name} ({$preorder->email}). User role upgraded to host."
        );

        return response()->json([
            'success' => true,
            'message' => "Preorder dari {$preorder->name} disetujui. Akun telah aktif sebagai Host.",
        ]);
    }

    /**
     * Reject preorder request.
     */
    public function reject(Preorder $preorder): JsonResponse
    {
        $preorder->update(['status' => 'rejected']);

        \App\Helpers\ActivityLogHelper::log(
            'preorder',
            'reject_preorder',
            "Admin rejected preorder request #{$preorder->id} from user {$preorder->name} ({$preorder->email})."
        );

        return response()->json([
            'success' => true,
            'message' => "Preorder dari {$preorder->name} ditolak.",
        ]);
    }
}
