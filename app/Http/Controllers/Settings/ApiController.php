<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Inertia\Response;

class ApiController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('settings/Api', [
            'immichUrl' => config('services.immich.url'),
            'immichApiKey' => config('services.immich.api_key'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'immich_url' => ['nullable', 'url', 'max:255'],
            'immich_api_key' => ['nullable', 'string', 'max:255'],
        ]);

        $this->setEnvValue('IMMICH_URL', $request->input('immich_url') ?? '');
        $this->setEnvValue('IMMICH_API_KEY', $request->input('immich_api_key') ?? '');

        // Clear config cache
        Artisan::call('config:clear');

        return back()->with('success', 'Konfigurasi API berhasil diperbarui.');
    }

    private function setEnvValue(string $key, string $value): void
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $value = '"'.trim($value).'"';
            $oldContent = file_get_contents($path);

            if ($oldContent === false) {
                return;
            }

            // Check if key exists
            if (preg_match("/^{$key}=/m", $oldContent)) {
                $newContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $oldContent);
            } else {
                $newContent = $oldContent."\n{$key}={$value}\n";
            }

            if ($newContent !== null) {
                file_put_contents($path, $newContent);
            }
        }
    }
}
