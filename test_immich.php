<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$url = rtrim(config('services.immich.url', ''), '/');
$apiKey = config('services.immich.api_key', '');

$response = Illuminate\Support\Facades\Http::withHeaders([
    'x-api-key' => $apiKey,
    'Accept' => 'application/json',
])->get("{$url}/api/users/me");

echo "Stats:\n";
echo $response->body() . "\n";
