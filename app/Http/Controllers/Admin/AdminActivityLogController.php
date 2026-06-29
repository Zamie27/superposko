<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminActivityLogController extends Controller
{
    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $perPage = $request->input('per_page', 30);

        $query = ActivityLog::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('user_name', 'like', "%{$search}%")
                        ->orWhere('user_email', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('action', 'like', "%{$search}%");
                });
            })
            ->when($category, function ($query, $category) {
                $query->where('category', $category);
            })
            ->orderBy('id', 'desc');

        if ($perPage === 'all') {
            $logs = $query->paginate(999999)->withQueryString();
        } else {
            $logs = $query->paginate(max(10, (int) $perPage))->withQueryString();
        }

        return Inertia::render('admin/ActivityLogs', [
            'logs' => $logs,
            'filters' => $request->only(['search', 'category', 'per_page']),
        ]);
    }
}
