<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Illuminate\Support\Str;

class DocumentationController extends Controller
{
    protected string $url;
    protected string $apiKey;

    public function __construct()
    {
        $this->url = rtrim(config('services.immich.url', ''), '/');
        $this->apiKey = config('services.immich.api_key', '');
    }

    public function index()
    {
        // Jika API key belum diset, return kosong
        if (empty($this->apiKey)) {
            return Inertia::render('documentation/Index', [
                'assets' => [],
                'error' => 'API Key Immich belum dikonfigurasi di .env',
            ]);
        }

        try {
            // Get all assets using search metadata
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'x-api-key' => $this->apiKey,
            ])->post("{$this->url}/api/search/metadata", [
                'withExif' => false,
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $assets = $responseData['assets']['items'] ?? $responseData['items'] ?? $responseData ?? [];
                
                // Map the assets to include local proxy URLs
                $mappedAssets = collect($assets)->map(function ($asset) {
                    return [
                        'id' => $asset['id'],
                        'type' => $asset['type'], // IMAGE or VIDEO
                        'thumbnail_url' => route('documentation.thumbnail', ['id' => $asset['id']]),
                        'file_url' => route('documentation.file', ['id' => $asset['id']]),
                        'createdAt' => $asset['fileCreatedAt'] ?? $asset['createdAt'] ?? null,
                    ];
                })->toArray();

                return Inertia::render('documentation/Index', [
                    'assets' => $mappedAssets,
                    'error' => session('error'),
                    'success' => session('success'),
                ]);
            }

            return Inertia::render('documentation/Index', [
                'assets' => [],
                'error' => session('error', 'Gagal mengambil data dari Immich: ' . $response->status()),
                'success' => session('success'),
            ]);
        } catch (\Exception $e) {
            return Inertia::render('documentation/Index', [
                'assets' => [],
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }

    public function thumbnail($id)
    {
        if (empty($this->apiKey)) abort(404);

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
        ])->get("{$this->url}/api/assets/{$id}/thumbnail");

        if ($response->successful()) {
            return response($response->body(), 200, [
                'Content-Type' => $response->header('Content-Type', 'image/webp'),
                'Cache-Control' => 'public, max-age=86400', // Cache for 1 day
            ]);
        }

        abort(404);
    }

    public function file(Request $request, $id)
    {
        if (empty($this->apiKey)) abort(404);

        $isDownload = $request->query('download') === 'true';

        // For large files/videos, we stream the response
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
        ])->send('GET', "{$this->url}/api/assets/{$id}/original", [
            'stream' => true,
        ]);

        if ($response->successful()) {
            $headers = [
                'Content-Type' => $response->header('Content-Type'),
                'Content-Length' => $response->header('Content-Length'),
                'Accept-Ranges' => 'bytes',
            ];

            if ($isDownload) {
                $headers['Content-Disposition'] = 'attachment; filename="immich_asset_'.$id.'"';
            } else {
                $headers['Content-Disposition'] = 'inline';
            }

            return response()->stream(function () use ($response) {
                while (!$response->toPsrResponse()->getBody()->eof()) {
                    echo $response->toPsrResponse()->getBody()->read(1024 * 8);
                    flush();
                }
            }, 200, $headers);
        }

        abort(404);
    }

    public function store(Request $request)
    {
        if (empty($this->apiKey)) {
            return back()->with('error', 'API Key Immich belum dikonfigurasi.');
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,mp4,mov,avi', 'max:512000'], // 500MB max
        ]);

        $file = $request->file('file');
        
        // Immich requires some specific metadata
        $deviceId = 'SuperPosko-Web';
        $deviceAssetId = Str::uuid()->toString(); // unique ID per upload
        $now = now()->toIso8601String();

        try {
            $response = Http::timeout(300)->withHeaders([
                'x-api-key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->attach(
                'assetData', fopen($file->getPathname(), 'r'), $file->getClientOriginalName()
            )->post("{$this->url}/api/assets", [
                'deviceId' => $deviceId,
                'deviceAssetId' => $deviceAssetId,
                'fileCreatedAt' => $now,
                'fileModifiedAt' => $now,
                'isFavorite' => 'false',
            ]);

            if ($response->successful()) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'File berhasil diunggah ke Dokumentasi.']);
                }
                return back()->with('success', 'File berhasil diunggah ke Dokumentasi.');
            }

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Gagal mengunggah ke Immich: ' . $response->body()], 400);
            }
            return back()->with('error', 'Gagal mengunggah ke Immich: ' . $response->body());
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan saat mengunggah: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Terjadi kesalahan saat mengunggah: ' . $e->getMessage());
        }
    }
}
