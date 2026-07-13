<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BugReport;
use App\Models\Preorder;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard index page.
     */
    public function index(Request $request): Response
    {
        $request->session()->forget('dpl_active_host_id');

        $totalUsers = User::count();
        $totalHosts = User::whereNull('host_id')->where('role', '!=', 'admin')->count();
        $activeSubscriptions = User::whereNull('host_id')
            ->where('role', '!=', 'admin')
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

        // Bug report leaderboard: Top reporters by total submissions
        $topBugReporters = BugReport::query()
            ->selectRaw('reporter_name, contact_info, COUNT(*) as total_reports')
            ->groupBy('reporter_name', 'contact_info')
            ->orderByDesc('total_reports')
            ->limit(10)
            ->get();

        // Bug report leaderboard: Top reporters by accepted/resolved bugs
        $topAcceptedReporters = BugReport::query()
            ->selectRaw('reporter_name, contact_info, COUNT(*) as accepted_reports')
            ->where('status', 'resolved')
            ->groupBy('reporter_name', 'contact_info')
            ->orderByDesc('accepted_reports')
            ->limit(10)
            ->get();

        $totalBugReports = BugReport::count();
        $pendingBugReports = BugReport::where('status', 'pending')->count();
        $resolvedBugReports = BugReport::where('status', 'resolved')->count();

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'totalUsers' => $totalUsers,
                'totalHosts' => $totalHosts,
                'activeSubscriptions' => $activeSubscriptions,
                'totalPreorders' => $totalPreorders,
                'pendingPreorders' => $pendingPreorders,
                'approvedPreorders' => $approvedPreorders,
                'totalTrials' => $totalTrials,
                'totalBugReports' => $totalBugReports,
                'pendingBugReports' => $pendingBugReports,
                'resolvedBugReports' => $resolvedBugReports,
            ],
            'topBugReporters' => $topBugReporters,
            'topAcceptedReporters' => $topAcceptedReporters,
        ]);
    }
}
