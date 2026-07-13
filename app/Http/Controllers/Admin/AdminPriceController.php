<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
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
                'checkoutPaymentMethod' => Setting::get('checkout_payment_method', 'tripay'),
                'staticQrisPath' => Setting::get('static_qris_path', null),
                'staticQrisUrl' => Setting::get('static_qris_path') ? \Illuminate\Support\Facades\Storage::disk(env('FILESYSTEM_DISK', 'public'))->url(Setting::get('static_qris_path')) : null,
            ],
        ]);
    }

    /**
     * Update pricing settings.
     */
    public function update(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $validated = $request->validate([
            'packageName' => ['required', 'string', 'max:100'],
            'packagePrice' => ['required', 'integer', 'min:0'],
            'packageStrikePrice' => ['required', 'integer', 'min:0'],
            'packageDescription' => ['required', 'string'],
            'preorderPrice' => ['required', 'integer', 'min:0'],
            'preorderStrikePrice' => ['required', 'integer', 'min:0'],
            'preorderPromoActive' => ['required', 'boolean'],
            'checkoutPaymentMethod' => ['required', 'string', 'in:tripay,qris_static'],
        ]);

        Setting::set('package_name', $validated['packageName']);
        Setting::set('package_price', $validated['packagePrice']);
        Setting::set('package_strike_price', $validated['packageStrikePrice']);
        Setting::set('package_description', $validated['packageDescription']);
        Setting::set('preorder_price', $validated['preorderPrice']);
        Setting::set('preorder_strike_price', $validated['preorderStrikePrice']);
        Setting::set('preorder_promo_active', $validated['preorderPromoActive'] ? '1' : '0');
        Setting::set('checkout_payment_method', $validated['checkoutPaymentMethod']);

        ActivityLogHelper::log(
            'settings',
            'update_pricing',
            "Admin updated pricing configuration: Package {$validated['packageName']} = Rp ".number_format($validated['packagePrice']).', Preorder = Rp '.number_format($validated['preorderPrice']).' (Preorder promo: '.($validated['preorderPromoActive'] ? 'Active' : 'Inactive').')'
        );

        return Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Konfigurasi harga berhasil diperbarui.',
        ])->back();
    }

    /**
     * Upload static QRIS image.
     */
    public function updateQris(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $request->validate([
            'qris_image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        if ($request->hasFile('qris_image')) {
            $oldPath = Setting::get('static_qris_path');
            if ($oldPath && \Illuminate\Support\Facades\Storage::disk(env('FILESYSTEM_DISK', 'public'))->exists($oldPath)) {
                \Illuminate\Support\Facades\Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($oldPath);
            }

            $path = $request->file('qris_image')->store('qris', env('FILESYSTEM_DISK', 'public'));
            Setting::set('static_qris_path', $path);

            ActivityLogHelper::log(
                'settings',
                'update_qris',
                "Admin updated static QRIS payment image."
            );

            return Inertia::flash('toast', [
                'type' => 'success',
                'message' => 'QRIS pembayaran berhasil diunggah.',
            ])->back();
        }

        return back()->withErrors(['qris_image' => 'Gagal mengunggah berkas.']);
    }

    /**
     * Delete static QRIS image.
     */
    public function deleteQris(): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $oldPath = Setting::get('static_qris_path');
        if ($oldPath && \Illuminate\Support\Facades\Storage::disk(env('FILESYSTEM_DISK', 'public'))->exists($oldPath)) {
            \Illuminate\Support\Facades\Storage::disk(env('FILESYSTEM_DISK', 'public'))->delete($oldPath);
        }

        Setting::set('static_qris_path', null);

        ActivityLogHelper::log(
            'settings',
            'delete_qris',
            "Admin deleted static QRIS payment image."
        );

        return Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'QRIS pembayaran berhasil dihapus.',
        ])->back();
    }
}
