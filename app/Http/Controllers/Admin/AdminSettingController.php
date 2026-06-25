<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
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
                'socialInstagram' => Setting::get('social_instagram', 'superposko'),
                'midtransMerchantId' => Setting::get('midtrans_merchant_id', config('services.midtrans.merchant_id', '')),
                'midtransClientKey' => Setting::get('midtrans_client_key', config('services.midtrans.client_key', '')),
                'midtransServerKey' => Setting::get('midtrans_server_key', config('services.midtrans.server_key', '')),
                'midtransIsProduction' => filter_var(Setting::get('midtrans_is_production', config('services.midtrans.is_production', false)), FILTER_VALIDATE_BOOLEAN),
                'immichUrl' => Setting::get('immich_url', config('services.immich.url', '')),
                
                // Event Settings
                'eventTitle' => Setting::get('event_title', 'SuperPosko Beta Bug Hunting & Feedback Event'),
                'eventPrize' => Setting::get('event_prize', 'Rp 100.000'),
                'eventStartDate' => Setting::get('event_start_date', '2026-06-23'),
                'eventEndDate' => Setting::get('event_end_date', '2026-06-28'),
                'eventYoutubeEmbedUrl' => Setting::get('event_youtube_embed_url', 'https://www.youtube.com/embed/dQw4w9WgXcQ'),
                'eventDescription' => Setting::get('event_description', 'Event testing ini diadakan untuk menguji kesiapan, kesesuaian fitur, kestabilan kinerja, dan keamanan aplikasi SuperPosko sebelum peluncuran resmi. Mari berkontribusi untuk menciptakan aplikasi KKN terbaik!'),
                'eventRules' => Setting::get('event_rules', "1. Peserta wajib mendaftar akun di SuperPosko.\n2. Peserta menguji fungsionalitas fitur (E-Bendahara, Piket, Logbook, dll.) dan melaporkan setiap bug yang ditemukan melalui bubble Lapor Bug.\n3. Bug yang valid dan belum pernah dilaporkan sebelumnya akan mendapatkan poin.\n4. Pemenang ditentukan berdasarkan jumlah temuan bug valid terbanyak.\n5. Keputusan juri bersifat mutlak."),
            ],
        ]);
    }

    /**
     * Update settings.
     */
    public function update(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $validated = $request->validate([
            'footerAbout' => ['required', 'string'],
            'footerEmail' => ['required', 'email'],
            'footerPhone' => ['required', 'string'],
            'footerCopyright' => ['required', 'string'],
            'socialInstagram' => ['nullable', 'string', 'max:255'],
            'midtransMerchantId' => ['nullable', 'string', 'max:255'],
            'midtransClientKey' => ['nullable', 'string', 'max:255'],
            'midtransServerKey' => ['nullable', 'string', 'max:255'],
            'midtransIsProduction' => ['required', 'boolean'],
            'immichUrl' => ['nullable', 'url', 'max:255'],
            
            // Event Validation
            'eventTitle' => ['required', 'string', 'max:255'],
            'eventPrize' => ['required', 'string', 'max:255'],
            'eventStartDate' => ['required', 'date'],
            'eventEndDate' => ['required', 'date', 'after_or_equal:eventStartDate'],
            'eventYoutubeEmbedUrl' => ['nullable', 'string', 'max:500'],
            'eventDescription' => ['required', 'string'],
            'eventRules' => ['required', 'string'],
        ]);

        $phone = preg_replace('/[^0-9]/', '', $validated['footerPhone']);
        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        } elseif (! str_starts_with($phone, '62')) {
            $phone = '62'.$phone;
        }

        Setting::set('footer_about', $validated['footerAbout']);
        Setting::set('footer_email', $validated['footerEmail']);
        Setting::set('footer_phone', $phone);
        Setting::set('footer_copyright', $validated['footerCopyright']);
        Setting::set('social_instagram', $validated['socialInstagram'] ?? '');
        Setting::set('midtrans_merchant_id', $validated['midtransMerchantId'] ?? '');
        Setting::set('midtrans_client_key', $validated['midtransClientKey'] ?? '');
        Setting::set('midtrans_server_key', $validated['midtransServerKey'] ?? '');
        Setting::set('midtrans_is_production', $validated['midtransIsProduction'] ? '1' : '0');
        Setting::set('immich_url', $validated['immichUrl'] ?? '');
        
        // Save Event settings
        Setting::set('event_title', $validated['eventTitle']);
        Setting::set('event_prize', $validated['eventPrize']);
        Setting::set('event_start_date', $validated['eventStartDate']);
        Setting::set('event_end_date', $validated['eventEndDate']);
        Setting::set('event_youtube_embed_url', $validated['eventYoutubeEmbedUrl'] ?? '');
        Setting::set('event_description', $validated['eventDescription']);
        Setting::set('event_rules', $validated['eventRules']);

        ActivityLogHelper::log(
            'settings',
            'update_settings',
            'Admin updated website general, API integrations, and Event configurations.'
        );

        return Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Konfigurasi website berhasil disimpan.',
        ])->back();
    }
}
