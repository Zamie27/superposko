<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = rtrim(config('services.immich.url', ''), '/');
$apiKey = config('services.immich.api_key', '');

// create a dummy image
file_put_contents('dummy.jpg', 'fake image content');

$now = now()->toIso8601String();

$response = Illuminate\Support\Facades\Http::withHeaders([
    'x-api-key' => $apiKey,
    'Accept' => 'application/json',
])->attach(
    'assetData', file_get_contents('dummy.jpg'), 'dummy.jpg'
)->post("{$url}/api/assets", [
    'deviceId' => 'SuperPosko-Web',
    'deviceAssetId' => \Illuminate\Support\Str::uuid()->toString(),
    'fileCreatedAt' => $now,
    'fileModifiedAt' => $now,
    'isFavorite' => 'false',
]);

echo "Upload Response:\n";
echo $response->status() . "\n";
echo $response->body() . "\n";
