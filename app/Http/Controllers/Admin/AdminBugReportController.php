<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BugReport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminBugReportController extends Controller
{
    /**
     * Display bug reports listing.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $bugReports = BugReport::query()
            ->with('user:id,name,email')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('reporter_name', 'like', "%{$search}%")
                        ->orWhere('contact_info', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/BugReports', [
            'bugReports' => $bugReports,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Resolve a bug report.
     */
    public function resolve(BugReport $bugReport): RedirectResponse
    {
        $bugReport->update(['status' => 'resolved']);

        return Inertia::flash('toast', [
            'type' => 'success',
            'message' => "Laporan bug \"{$bugReport->title}\" telah ditandai selesai.",
        ])->back();
    }
}
