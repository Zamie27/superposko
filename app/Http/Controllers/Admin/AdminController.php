<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Preorder;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard index page.
     */
    public function index(): Response
    {
        $totalUsers = User::count();
        $totalHosts = User::where('role', 'host')->count();
        $activeSubscriptions = User::where('role', 'host')
            ->where(function ($query) {
                $query->whereNull('subscription_expires_at')
                    ->orWhere('subscription_expires_at', '>', now());
            })->count();

        $totalPreorders = Preorder::count();
        $pendingPreorders = Preorder::where('status', 'pending')->count();
        $approvedPreorders = Preorder::where('status', 'approved')->count();

        $totalTrials = User::where('role', 'trial')
            ->where(function ($query) {
                $query->whereNull('trial_ends_at')
                    ->orWhere('trial_ends_at', '>', now());
            })->count();

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'totalUsers' => $totalUsers,
                'totalHosts' => $totalHosts,
                'activeSubscriptions' => $activeSubscriptions,
                'totalPreorders' => $totalPreorders,
                'pendingPreorders' => $pendingPreorders,
                'approvedPreorders' => $approvedPreorders,
                'totalTrials' => $totalTrials,
            ],
        ]);
    }
}
