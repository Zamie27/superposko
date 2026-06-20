<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$url = rtrim(config('services.immich.url', ''), '/');
$apiKey = config('services.immich.api_key', '');

file_put_contents('dummy.jpg', 'fake image content');
$stream = fopen('dummy.jpg', 'r');
$now = now()->toIso8601String();

try {
    echo "Starting upload...\n";
    $response = Http::timeout(10)->withHeaders([
        'x-api-key' => $apiKey,
        'Accept' => 'application/json',
    ])->attach(
        'assetData', $stream, 'dummy.jpg'
    )->post("{$url}/api/assets", [
        'deviceId' => 'SuperPosko-Web',
        'deviceAssetId' => Str::uuid()->toString(),
        'fileCreatedAt' => $now,
        'fileModifiedAt' => $now,
        'isFavorite' => 'false',
    ]);

    echo 'Status: '.$response->status()."\n";
    echo 'Body: '.$response->body()."\n";
} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
fclose($stream);
unlink('dummy.jpg');
