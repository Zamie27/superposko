<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PoskoSettingsController extends Controller
{
    /**
     * Get target host user model.
     */
    protected function getHostUser()
    {
        $user = auth()->user();
        if ($user->host_id) {
            return \App\Models\User::find($user->host_id);
        }

        return $user;
    }

    /**
     * Show posko settings form.
     */
    public function edit(Request $request): Response
    {
        $host = $this->getHostUser();

        $logoUrl = \App\Helpers\StorageHelper::getUrl($host->posko_logo_url);

        // Parse numeric group number if prefixed with "Kelompok "
        $cleanGroupNum = $host->group_number;
        if ($cleanGroupNum && preg_match('/^Kelompok\s+(\d+)$/i', trim($cleanGroupNum), $matches)) {
            $cleanGroupNum = $matches[1];
        }

        return Inertia::render('settings/Posko', [
            'posko' => [
                'group_number' => $cleanGroupNum,
                'full_group_name' => $host->group_number,
                'kkn_address' => $host->kkn_address,
                'posko_village' => $host->posko_village,
                'posko_district' => $host->posko_district,
                'posko_regency' => $host->posko_regency,
                'posko_province' => $host->posko_province,
                'posko_postal_code' => $host->posko_postal_code,
                'posko_logo_url' => $logoUrl,
            ],
            'canEdit' => in_array(auth()->user()->role, ['host', 'admin', 'ketua', 'wakil', 'sekretaris']),
        ]);
    }

    /**
     * Update posko details.
     */
    public function update(Request $request): RedirectResponse
    {
        $host = $this->getHostUser();

        $validated = $request->validate([
            'group_number' => ['required', 'string', 'max:255'],
            'kkn_address' => ['nullable', 'string', 'max:500'],
            'posko_village' => ['nullable', 'string', 'max:255'],
            'posko_district' => ['nullable', 'string', 'max:255'],
            'posko_regency' => ['nullable', 'string', 'max:255'],
            'posko_province' => ['nullable', 'string', 'max:255'],
            'posko_postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        $groupNum = trim($validated['group_number']);
        if (is_numeric($groupNum)) {
            $groupNum = "Kelompok {$groupNum}";
        }

        $host->update([
            'group_number' => $groupNum,
            'kkn_address' => $validated['kkn_address'],
            'posko_village' => $validated['posko_village'],
            'posko_district' => $validated['posko_district'],
            'posko_regency' => $validated['posko_regency'],
            'posko_province' => $validated['posko_province'],
            'posko_postal_code' => $validated['posko_postal_code'],
        ]);

        return back()->with('success', 'Data Posko KKN berhasil diperbarui.');
    }

    /**
     * Upload posko logo to MinIO storage under client/kelompok_{group_number}/logo/.
     */
    public function uploadLogo(Request $request): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'file', 'mimes:png,jpg,jpeg,svg,webp,gif', 'max:10240'],
        ], [
            'logo.uploaded' => 'Berkas logo gagal diunggah. Ukuran berkas melebihi batas maksimum PHP server (upload_max_filesize). Silakan gunakan gambar yang lebih kecil atau di bawah 2MB.',
            'logo.max' => 'Ukuran berkas logo maksimal 10MB.',
            'logo.mimes' => 'Format berkas logo harus berupa PNG, JPG, JPEG, SVG, atau WEBP.',
            'logo.required' => 'Silakan pilih berkas logo terlebih dahulu.',
        ]);

        $host = $this->getHostUser();
        $file = $request->file('logo');

        $disk = env('FILESYSTEM_DISK', 's3');
        $groupSlug = Str::slug($host->group_number ?: "kelompok-{$host->id}", '_');
        $path = $file->store("client/{$groupSlug}/logo", $disk);

        if ($host->posko_logo_url && Storage::disk($disk)->exists($host->posko_logo_url)) {
            try {
                Storage::disk($disk)->delete($host->posko_logo_url);
            } catch (\Throwable $e) {
                // Ignore delete error
            }
        }

        $host->update([
            'posko_logo_url' => $path,
        ]);

        return back()->with('success', 'Logo Posko berhasil diperbarui.');
    }
}
