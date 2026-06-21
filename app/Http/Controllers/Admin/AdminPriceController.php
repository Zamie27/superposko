<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminPriceController extends Controller
{
    /**
     * Display pricing settings page.
     */
    public function index(): Response
    {
        return Inertia::render('admin/Prices', [
            'pricing' => [
                'packageName' => Setting::get('package_name', 'Paket Posko'),
                'packagePrice' => (int) Setting::get('package_price', 100000),
                'packageStrikePrice' => (int) Setting::get('package_strike_price', 150000),
                'packageDescription' => Setting::get('package_description', 'Dapatkan akses penuh ke seluruh modul platform untuk satu posko KKN tanpa batasan kuota user/anggota kelompok.'),
                'preorderPrice' => (int) Setting::get('preorder_price', 50000),
                'preorderStrikePrice' => (int) Setting::get('preorder_strike_price', 100000),
                'preorderPromoActive' => filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN),
            ],
        ]);
    }

    /**
     * Update pricing settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'packageName' => ['required', 'string', 'max:100'],
            'packagePrice' => ['required', 'integer', 'min:0'],
            'packageStrikePrice' => ['required', 'integer', 'min:0'],
            'packageDescription' => ['required', 'string'],
            'preorderPrice' => ['required', 'integer', 'min:0'],
            'preorderStrikePrice' => ['required', 'integer', 'min:0'],
            'preorderPromoActive' => ['required', 'boolean'],
        ]);

        Setting::set('package_name', $validated['packageName']);
        Setting::set('package_price', $validated['packagePrice']);
        Setting::set('package_strike_price', $validated['packageStrikePrice']);
        Setting::set('package_description', $validated['packageDescription']);
        Setting::set('preorder_price', $validated['preorderPrice']);
        Setting::set('preorder_strike_price', $validated['preorderStrikePrice']);
        Setting::set('preorder_promo_active', $validated['preorderPromoActive'] ? '1' : '0');

        return Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Konfigurasi harga berhasil diperbarui.',
        ])->back();
    }
}
