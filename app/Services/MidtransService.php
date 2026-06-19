<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MidtransService
{
    /**
     * Get the Snap API base URL.
     */
    protected function getBaseUrl(): string
    {
        $isProduction = filter_var(config('services.midtrans.is_production', false), FILTER_VALIDATE_BOOLEAN);

        return $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Create Snap Token.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>|null
     */
    public function createSnapTransaction(array $params): ?array
    {
        $serverKey = config('services.midtrans.server_key');

        if (empty($serverKey)) {
            return null;
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
            ->withBasicAuth($serverKey, '')
            ->post($this->getBaseUrl(), $params);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
