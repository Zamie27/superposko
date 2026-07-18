<?php

namespace App\Helpers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImmichHelper
{
    /**
     * Get Immich configuration (url, apiKey).
     *
     * @param int|null $hostId
     * @return array{url: string, apiKey: string}|null
     */
    public static function getConfig(?int $hostId = null): ?array
    {
        $url = rtrim(Setting::get('immich_url', config('services.immich.url', '')), '/');
        $apiKey = config('services.immich.api_key', '');

        if ($hostId) {
            $host = User::find($hostId);
            if ($host && $host->immich_api_key) {
                $apiKey = $host->immich_api_key;
            }
        }

        if (empty($url) || empty($apiKey)) {
            return null;
        }

        return ['url' => $url, 'apiKey' => $apiKey];
    }

    /**
     * Upload a file to Immich and add it to an album.
     *
     * @param UploadedFile $file
     * @param string $albumName
     * @param int|null $hostId
     * @return string|null Asset ID
     */
    public static function uploadToAlbum(UploadedFile $file, string $albumName, ?int $hostId = null): ?string
    {
        $config = self::getConfig($hostId);
        if (!$config) {
            return null;
        }

        $assetId = self::uploadAsset($file, $config);
        if (!$assetId) {
            return null;
        }

        $albumId = self::getOrCreateAlbum($albumName, $config);
        if ($albumId) {
            self::addAssetToAlbum($albumId, $assetId, $config);
        }

        return $assetId;
    }

    /**
     * Upload the asset file.
     */
    private static function uploadAsset(UploadedFile $file, array $config): ?string
    {
        $deviceId = 'SuperPosko-Web';
        $deviceAssetId = Str::uuid()->toString();
        $now = now()->toIso8601String();

        $stream = fopen($file->getPathname(), 'r');
        if ($stream === false) {
            return null;
        }

        $response = Http::timeout(300)->withHeaders([
            'x-api-key' => $config['apiKey'],
            'Accept' => 'application/json',
        ])->attach(
            'assetData', $stream, $file->getClientOriginalName()
        )->post("{$config['url']}/api/assets", [
            'deviceId' => $deviceId,
            'deviceAssetId' => $deviceAssetId,
            'fileCreatedAt' => $now,
            'fileModifiedAt' => $now,
            'isFavorite' => 'false',
        ]);

        if ($response->successful()) {
            return $response->json('id');
        }

        return null;
    }

    /**
     * Get or create an album by name.
     */
    private static function getOrCreateAlbum(string $albumName, array $config): ?string
    {
        // Get all albums
        $response = Http::withHeaders([
            'x-api-key' => $config['apiKey'],
            'Accept' => 'application/json',
        ])->get("{$config['url']}/api/albums");

        if ($response->successful()) {
            $albums = $response->json();
            foreach ($albums as $album) {
                if (($album['albumName'] ?? '') === $albumName) {
                    return $album['id'];
                }
            }
        }

        // Create album if not found
        $response = Http::withHeaders([
            'x-api-key' => $config['apiKey'],
            'Accept' => 'application/json',
        ])->post("{$config['url']}/api/albums", [
            'albumName' => $albumName,
        ]);

        if ($response->successful()) {
            return $response->json('id');
        }

        return null;
    }

    /**
     * Add an asset to an album.
     */
    private static function addAssetToAlbum(string $albumId, string $assetId, array $config): bool
    {
        $response = Http::withHeaders([
            'x-api-key' => $config['apiKey'],
            'Accept' => 'application/json',
        ])->put("{$config['url']}/api/albums/{$albumId}/assets", [
            'assetIds' => [$assetId],
        ]);

        return $response->successful();
    }

    /**
     * Generate the thumbnail URL for the asset.
     */
    public static function getThumbnailUrl(string $assetId, ?int $hostId = null): ?string
    {
        $config = self::getConfig($hostId);
        if (!$config) {
            return null;
        }

        // If your frontend accesses Immich directly:
        // return "{$config['url']}/api/assets/{$assetId}/thumbnail";
        // However, this requires the API key in the frontend.
        // It's better to proxy it via your application.
        return route('host.documentation.thumbnail', ['id' => $assetId], false);
    }
}
