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

        // Ignore Immich UUIDs for Voting as requested (use pure MinIO bucket)
        if (Str::isUuid($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return url('/media/'.ltrim($path, '/'));
    }
}
