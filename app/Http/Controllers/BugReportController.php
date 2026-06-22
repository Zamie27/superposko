<?php

namespace App\Http\Controllers;

use App\Models\BugReport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BugReportController extends Controller
{
    /**
     * Store a newly created bug report.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'reporter_name' => ['required', 'string', 'max:255'],
            'contact_info' => ['nullable', 'string', 'max:255'],
            'screenshots' => ['nullable', 'array', 'max:5'],
            'screenshots.*' => ['image', 'max:5120'], // Each max 5MB
        ]);

        $screenshotPaths = [];
        if ($request->hasFile('screenshots')) {
            foreach ($request->file('screenshots') as $file) {
                $screenshotPaths[] = $file->store('bug-reports', 'public');
            }
        }

        BugReport::create([
            'user_id' => $request->user()?->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'reporter_name' => $validated['reporter_name'],
            'contact_info' => $validated['contact_info'] ?? null,
            'screenshots' => $screenshotPaths ?: null,
            'status' => 'pending',
        ]);

        return Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Laporan bug berhasil dikirim! Terima kasih atas kontribusinya.',
        ])->back();
    }
}
