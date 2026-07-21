<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class StorageHelper
{
    /**
     * Get accessible public URL for any stored file or asset path.
     */
    public static function getUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::isUuid($path)) {
            return ImmichHelper::getThumbnailUrl($path);
        }

        $awsUrl = env('AWS_URL');
        if ($awsUrl && ! Str::contains($awsUrl, ['minio:9000', 'localhost:9000'])) {
            return rtrim($awsUrl, '/').'/'.ltrim($path, '/');
        }

        return url('/media/'.ltrim($path, '/'));
    }
}
