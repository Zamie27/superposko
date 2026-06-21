<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class MidtransService
{
    /**
     * Get the Snap API base URL.
     */
    protected function getBaseUrl(): string
    {
        $isProduction = filter_var(Setting::get('midtrans_is_production', config('services.midtrans.is_production', false)), FILTER_VALIDATE_BOOLEAN);

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
        $serverKey = Setting::get('midtrans_server_key', config('services.midtrans.server_key', ''));

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

    /**
     * Get transaction status from Midtrans.
     */
    public function getTransactionStatus(string $orderId): ?array
    {
        $serverKey = Setting::get('midtrans_server_key', config('services.midtrans.server_key', ''));

        if (empty($serverKey)) {
            return null;
        }

        $isProduction = filter_var(Setting::get('midtrans_is_production', config('services.midtrans.is_production', false)), FILTER_VALIDATE_BOOLEAN);
        $url = $isProduction
            ? "https://api.midtrans.com/v2/{$orderId}/status"
            : "https://api.sandbox.midtrans.com/v2/{$orderId}/status";

        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])
            ->withBasicAuth($serverKey, '')
            ->get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
