<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Http;

require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$url = rtrim(config('services.immich.url', ''), '/');
$apiKey = config('services.immich.api_key', '');

$response = Http::withHeaders([
    'x-api-key' => $apiKey,
    'Accept' => 'application/json',
])->get("{$url}/api/users/me");

echo "Stats:\n";
echo $response->body()."\n";
