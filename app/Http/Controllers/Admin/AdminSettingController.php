<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Helpers\ActivityLogHelper;
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
                'midtransMerchantId' => Setting::get('midtrans_merchant_id', config('services.midtrans.merchant_id', '')),
                'midtransClientKey' => Setting::get('midtrans_client_key', config('services.midtrans.client_key', '')),
                'midtransServerKey' => Setting::get('midtrans_server_key', config('services.midtrans.server_key', '')),
                'midtransIsProduction' => filter_var(Setting::get('midtrans_is_production', config('services.midtrans.is_production', false)), FILTER_VALIDATE_BOOLEAN),
                'immichUrl' => Setting::get('immich_url', config('services.immich.url', '')),
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
            'midtransMerchantId' => ['nullable', 'string', 'max:255'],
            'midtransClientKey' => ['nullable', 'string', 'max:255'],
            'midtransServerKey' => ['nullable', 'string', 'max:255'],
            'midtransIsProduction' => ['required', 'boolean'],
            'immichUrl' => ['nullable', 'url', 'max:255'],
        ]);

        $phone = preg_replace('/[^0-9]/', '', $validated['footerPhone']);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        Setting::set('footer_about', $validated['footerAbout']);
        Setting::set('footer_email', $validated['footerEmail']);
        Setting::set('footer_phone', $phone);
        Setting::set('footer_copyright', $validated['footerCopyright']);
        Setting::set('midtrans_merchant_id', $validated['midtransMerchantId'] ?? '');
        Setting::set('midtrans_client_key', $validated['midtransClientKey'] ?? '');
        Setting::set('midtrans_server_key', $validated['midtransServerKey'] ?? '');
        Setting::set('midtrans_is_production', $validated['midtransIsProduction'] ? '1' : '0');
        Setting::set('immich_url', $validated['immichUrl'] ?? '');

        ActivityLogHelper::log(
            'settings',
            'update_settings',
            'Admin updated website general and API integration settings.'
        );

        return Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Konfigurasi website berhasil disimpan.',
        ])->back();
    }
}
