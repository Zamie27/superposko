<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TripayService
{
    protected string $apiKey;

    protected string $privateKey;

    protected string $merchantCode;

    protected bool $isProduction;

    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) config('services.tripay.api_key', '');
        $this->privateKey = (string) config('services.tripay.private_key', '');
        $this->merchantCode = (string) config('services.tripay.merchant_code', '');
        $this->isProduction = (bool) config('services.tripay.is_production', false);

        $this->baseUrl = $this->isProduction
            ? 'https://tripay.co.id/api/'
            : 'https://tripay.co.id/api-sandbox/';
    }

    /**
     * Generate signature for transaction request.
     */
    public function generateRequestSignature(string $merchantRef, int $amount): string
    {
        return hash_hmac('sha256', $this->merchantCode.$merchantRef.$amount, $this->privateKey);
    }

    /**
     * Create a closed transaction in Tripay.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>|null
     */
    public function createTransaction(array $params): ?array
    {
        $merchantRef = $params['merchant_ref'] ?? '';
        $amount = (int) ($params['amount'] ?? 0);
        $method = $params['method'] ?? 'QRIS';

        $signature = $this->generateRequestSignature($merchantRef, $amount);

        $payload = [
            'method' => $method,
            'merchant_ref' => $merchantRef,
            'amount' => $amount,
            'customer_name' => $params['customer_name'] ?? 'Guest',
            'customer_email' => $params['customer_email'] ?? 'guest@superposko.web.id',
            'customer_phone' => $params['customer_phone'] ?? '',
            'order_items' => $params['order_items'] ?? [
                [
                    'name' => 'Aktivasi Lisensi SuperPosko',
                    'price' => $amount,
                    'quantity' => 1,
                ],
            ],
            'signature' => $signature,
        ];

        try {
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->withToken($this->apiKey)
                ->post($this->baseUrl.'transaction/create', $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['success']) && $responseData['success'] === true) {
                    return $responseData['data'] ?? null;
                }
            }

            Log::error('Gagal membuat transaksi Tripay: '.$response->body());

            return null;
        } catch (\Exception $e) {
            Log::error('Exception saat membuat transaksi Tripay: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Get transaction detail from Tripay.
     *
     * @return array<string, mixed>|null
     */
    public function getTransactionDetail(string $reference): ?array
    {
        try {
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->withToken($this->apiKey)
                ->get($this->baseUrl.'transaction/detail', [
                    'reference' => $reference,
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['success']) && $responseData['success'] === true) {
                    return $responseData['data'] ?? null;
                }
            }

            Log::error('Gagal mengambil detail transaksi Tripay: '.$response->body());

            return null;
        } catch (\Exception $e) {
            Log::error('Exception saat mengambil detail transaksi Tripay: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Get private key for signature validation.
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }
}
