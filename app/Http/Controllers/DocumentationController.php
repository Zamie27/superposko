<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DocumentationController extends Controller
{
    protected string $url;

    protected string $apiKey;

    public function __construct()
    {
        $this->url = rtrim(config('services.immich.url', ''), '/');
        $this->apiKey = config('services.immich.api_key', '');
    }

    public function index(): Response
    {
        $immichUrl = $this->url;
        $immichEmail = config('services.immich.email', '');
        $immichPassword = config('services.immich.password', '');

        // Jika API key belum diset, return kosong
        if (empty($this->apiKey)) {
            return Inertia::render('documentation/Index', [
                'assets' => [],
                'immichUrl' => $immichUrl,
                'immichEmail' => $immichEmail,
                'immichPassword' => $immichPassword,
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
                /** @var array<int, array<string, mixed>> $assets */
                $assets = $responseData['assets']['items'] ?? $responseData['items'] ?? $responseData ?? [];

                // Map the assets to include local proxy URLs
                $mappedAssets = collect($assets)->map(function ($asset) {
                    return [
                        'id' => $asset['id'],
                        'type' => $asset['type'], // IMAGE or VIDEO
                        'thumbnail_url' => route('host.documentation.thumbnail', ['id' => $asset['id']], false),
                        'file_url' => route('host.documentation.file', ['id' => $asset['id']], false),
                        'createdAt' => $asset['fileCreatedAt'] ?? $asset['createdAt'] ?? null,
                    ];
                })->toArray();

                return Inertia::render('documentation/Index', [
                    'assets' => $mappedAssets,
                    'immichUrl' => $immichUrl,
                    'immichEmail' => $immichEmail,
                    'immichPassword' => $immichPassword,
                    'error' => session('error'),
                    'success' => session('success'),
                ]);
            }

            return Inertia::render('documentation/Index', [
                'assets' => [],
                'immichUrl' => $immichUrl,
                'immichEmail' => $immichEmail,
                'immichPassword' => $immichPassword,
                'error' => session('error', 'Gagal mengambil data dari Immich: '.$response->status()),
                'success' => session('success'),
            ]);
        } catch (\Exception $e) {
            return Inertia::render('documentation/Index', [
                'assets' => [],
                'immichUrl' => $immichUrl,
                'immichEmail' => $immichEmail,
                'immichPassword' => $immichPassword,
                'error' => 'Terjadi kesalahan: '.$e->getMessage(),
            ]);
        }
    }

    public function thumbnail(string $id): \Symfony\Component\HttpFoundation\Response
    {
        if (empty($this->apiKey)) {
            abort(404);
        }

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
        ])->get("{$this->url}/api/assets/{$id}/thumbnail");

        if ($response->successful()) {
            return response($response->body(), 200, [
                'Content-Type' => $response->header('Content-Type'),
                'Cache-Control' => 'public, max-age=86400', // Cache for 1 day
            ]);
        }

        abort(404);
    }

    public function file(Request $request, string $id): \Symfony\Component\HttpFoundation\Response
    {
        if (empty($this->apiKey)) {
            abort(404);
        }

        $isDownload = $request->query('download') === 'true';

        // For large files/videos, we stream the response
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
        ])->send('GET', "{$this->url}/api/assets/{$id}/original", [
            'stream' => true,
        ]);

        if ($response->successful()) {
            $headers = [
                'Content-Type' => (string) $response->header('Content-Type'),
                'Content-Length' => (string) $response->header('Content-Length'),
                'Accept-Ranges' => 'bytes',
            ];

            if ($isDownload) {
                $headers['Content-Disposition'] = 'attachment; filename="immich_asset_'.$id.'"';
            } else {
                $headers['Content-Disposition'] = 'inline';
            }

            return response()->stream(function () use ($response) {
                while (! $response->toPsrResponse()->getBody()->eof()) {
                    echo $response->toPsrResponse()->getBody()->read(1024 * 8);
                    flush();
                }
            }, 200, $headers);
        }

        abort(404);
    }

    public function store(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        if (empty($this->apiKey)) {
            return back()->with('error', 'API Key Immich belum dikonfigurasi.');
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,mp4,mov,avi', 'max:512000'], // 500MB max
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('file');

        // Immich requires some specific metadata
        $deviceId = 'SuperPosko-Web';
        $deviceAssetId = Str::uuid()->toString(); // unique ID per upload
        $now = now()->toIso8601String();

        try {
            $stream = fopen($file->getPathname(), 'r');
            if ($stream === false) {
                throw new \RuntimeException('Gagal membuka file.');
            }

            $response = Http::timeout(300)->withHeaders([
                'x-api-key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->attach(
                'assetData', $stream, $file->getClientOriginalName()
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
                return response()->json(['message' => 'Gagal mengunggah ke Immich: '.$response->body()], 400);
            }

            return back()->with('error', 'Gagal mengunggah ke Immich: '.$response->body());
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan saat mengunggah: '.$e->getMessage()], 500);
            }

            return back()->with('error', 'Terjadi kesalahan saat mengunggah: '.$e->getMessage());
        }
    }

    public function uploadChunk(Request $request): JsonResponse
    {
        if (empty($this->apiKey)) {
            return response()->json(['message' => 'API Key Immich belum dikonfigurasi.'], 400);
        }

        $request->validate([
            'file' => ['required', 'file'],
            'chunkIndex' => ['required', 'integer'],
            'totalChunks' => ['required', 'integer'],
            'uploadUuid' => ['required', 'string', 'regex:/^[a-zA-Z0-9-]+$/'],
            'filename' => ['required', 'string'],
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('file');
        $chunkIndex = (int) $request->input('chunkIndex');
        $totalChunks = (int) $request->input('totalChunks');
        $uuid = (string) $request->input('uploadUuid');
        $filename = (string) $request->input('filename');

        $chunksDir = storage_path('app/chunks/'.$uuid);

        if (! file_exists($chunksDir)) {
            mkdir($chunksDir, 0755, true);
        }

        // Simpan chunk
        $file->move($chunksDir, (string) $chunkIndex);

        // Cek apakah seluruh chunk sudah ter-upload
        $globResult = glob($chunksDir.'/*');
        $uploadedChunksCount = is_array($globResult) ? count($globResult) : 0;

        if ($uploadedChunksCount === $totalChunks) {
            // Seluruh chunk lengkap! Lakukan penggabungan
            $tempDir = storage_path('app/temp');
            if (! file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // sanitize filename to prevent path traversal
            $safeFilename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
            if ($safeFilename === null) {
                $safeFilename = 'uploaded_file';
            }
            $mergedFilePath = $tempDir.'/'.time().'_'.$safeFilename;

            $outputFile = fopen($mergedFilePath, 'wb');
            if ($outputFile === false) {
                return response()->json(['message' => 'Gagal membuat file gabungan.'], 500);
            }

            // Tulis chunk secara berurutan
            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = $chunksDir.'/'.$i;
                if (! file_exists($chunkPath)) {
                    fclose($outputFile);
                    @unlink($mergedFilePath);

                    return response()->json(['message' => "Chunk ke-{$i} hilang saat penggabungan."], 500);
                }

                $chunkFile = fopen($chunkPath, 'rb');
                if ($chunkFile === false) {
                    fclose($outputFile);
                    @unlink($mergedFilePath);

                    return response()->json(['message' => "Gagal membuka chunk ke-{$i}."], 500);
                }

                while (! feof($chunkFile)) {
                    $buffer = fread($chunkFile, 1024 * 1024); // read 1MB at a time
                    if ($buffer !== false) {
                        fwrite($outputFile, $buffer);
                    }
                }

                fclose($chunkFile);
            }

            fclose($outputFile);

            // Kirim file utuh secara lokal ke Immich
            $deviceId = 'SuperPosko-Web';
            $deviceAssetId = Str::uuid()->toString();
            $now = now()->toIso8601String();

            try {
                $stream = fopen($mergedFilePath, 'r');
                if ($stream === false) {
                    throw new \RuntimeException('Gagal membuka file gabungan untuk dikirim.');
                }

                $response = Http::timeout(600)->withHeaders([
                    'x-api-key' => $this->apiKey,
                    'Accept' => 'application/json',
                ])->attach(
                    'assetData', $stream, $filename
                )->post("{$this->url}/api/assets", [
                    'deviceId' => $deviceId,
                    'deviceAssetId' => $deviceAssetId,
                    'fileCreatedAt' => $now,
                    'fileModifiedAt' => $now,
                    'isFavorite' => 'false',
                ]);

                // Cleanup files
                // 1. Delete chunks folder
                $files = glob($chunksDir.'/*');
                if ($files !== false) {
                    foreach ($files as $f) {
                        @unlink($f);
                    }
                }
                @rmdir($chunksDir);

                // 2. Delete merged file
                @unlink($mergedFilePath);

                if ($response->successful()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'File berhasil diunggah secara chunk.',
                    ]);
                }

                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mengunggah ke Immich: '.$response->body(),
                ], 400);

            } catch (\Exception $e) {
                // Cleanup on exception
                $files = glob($chunksDir.'/*');
                if ($files !== false) {
                    foreach ($files as $f) {
                        @unlink($f);
                    }
                }
                @rmdir($chunksDir);
                @unlink($mergedFilePath);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan saat mengunggah ke Immich: '.$e->getMessage(),
                ], 500);
            }
        }

        return response()->json([
            'status' => 'uploading',
            'message' => "Chunk ke-{$chunkIndex} berhasil diunggah.",
        ]);
    }
}
