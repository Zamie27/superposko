<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    /**
     * Show the form for creating a new report.
     */
    public function create(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('reports/Create', [
            'defaultEmail' => $user ? $user->email : '',
            'defaultType' => $request->input('type', 'complaint'),
            'defaultTitle' => $request->input('title', ''),
            'defaultDesc' => $request->input('desc', ''),
        ]);
    }

    /**
     * Store a newly created report in storage.
     */
    public function store(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'type' => ['required', 'string', 'in:security,bug,complaint'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'screenshot' => ['nullable', 'image', 'max:5120'], // Max 5MB
        ]);

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('reports', env('FILESYSTEM_DISK', 'public'));
        }

        Report::create([
            'user_id' => $request->user()?->id,
            'email' => $validated['email'],
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'screenshot' => $screenshotPath,
            'status' => 'pending',
        ]);

        return Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Laporan Anda berhasil dikirim dan akan segera diproses oleh Admin.',
        ])->back();
    }
}
