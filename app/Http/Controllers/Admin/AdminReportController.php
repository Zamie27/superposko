<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminReportController extends Controller
{
    /**
     * Display a listing of the reports.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $type = $request->input('type');

        $reports = Report::with('user:id,name,email')
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('admin/reports/Index', [
            'reports' => $reports,
            'filters' => $request->only(['search', 'status', 'type']),
        ]);
    }

    /**
     * Mark the report as resolved.
     */
    public function resolve(Report $report): RedirectResponse
    {
        $report->update(['status' => 'resolved']);

        return Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Status laporan berhasil diubah menjadi Selesai (Resolved).',
        ])->back();
    }
}
