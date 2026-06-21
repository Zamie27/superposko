<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminSettingController extends Controller
{
    /**
     * Display settings page.
     */
    public function index(): Response
    {
        return Inertia::render('admin/Settings', [
            'settings' => [
                'footerAbout' => Setting::get('footer_about', 'SuperPosko adalah platform kolaborasi kelompok KKN (Kuliah Kerja Nyata) berbasis web untuk menunjang keterbukaan informasi, kebersamaan, dan kerapian administrasi posko.'),
                'footerEmail' => Setting::get('footer_email', 'kuukok.id@gmail.com'),
                'footerPhone' => Setting::get('footer_phone', '+62 851-7173-9232'),
                'footerCopyright' => Setting::get('footer_copyright', 'Kuukok.id'),
            ],
        ]);
    }

    /**
     * Update settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'footerAbout' => ['required', 'string'],
            'footerEmail' => ['required', 'email'],
            'footerPhone' => ['required', 'string'],
            'footerCopyright' => ['required', 'string'],
        ]);

        Setting::set('footer_about', $validated['footerAbout']);
        Setting::set('footer_email', $validated['footerEmail']);
        Setting::set('footer_phone', $validated['footerPhone']);
        Setting::set('footer_copyright', $validated['footerCopyright']);

        return Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Konfigurasi website berhasil disimpan.',
        ])->back();
    }
}
