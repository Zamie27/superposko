<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberActivityLogController extends Controller
{
    /**
     * Display a listing of member activity logs.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $hostId = $user->host_id ?? $user->id;

        // Fetch all members of the posko for the filter dropdown
        $members = User::where('host_id', $hostId)
            ->orWhere('id', $hostId)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'email', 'role']);

        $memberIds = $members->pluck('id');

        // Query logs
        $query = ActivityLog::with('user:id,name,email')
            ->whereIn('user_id', $memberIds);

        // Filter by Search (description, action, or user name/email)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%");
            });
        }

        // Filter by Member ID
        if ($request->filled('member_id')) {
            $query->where('user_id', $request->input('member_id'));
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        // Get unique categories for filters
        $categories = ActivityLog::whereIn('user_id', $memberIds)
            ->distinct()
            ->pluck('category');

        return Inertia::render('management/activity-logs/Index', [
            'logs' => $logs,
            'members' => $members,
            'categories' => $categories,
            'filters' => $request->only(['search', 'member_id', 'category']),
        ]);
    }
}
