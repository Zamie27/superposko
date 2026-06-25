<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TripayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripayWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected string $privateKey = 'mocked_private_key_value';

    protected function setUp(): void
    {
        parent::setUp();

        // Bind mock TripayService with standard private key
        $this->mock(TripayService::class, function ($mock) {
            $mock->shouldReceive('getPrivateKey')->andReturn($this->privateKey);
        });
    }

    /**
     * Test successful payment webhook callback upgrades user role.
     */
    public function test_webhook_with_valid_signature_and_paid_status_upgrades_user()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $payload = [
            'reference' => 'T1234567890',
            'merchant_ref' => 'SUB-' . $user->id . '-12345678',
            'status' => 'PAID',
            'total_amount' => 100000,
        ];

        $jsonPayload = json_encode($payload);
        $signature = hash_hmac('sha256', $jsonPayload, $this->privateKey);

        $response = $this->withHeaders([
            'X-Callback-Signature' => $signature,
        ])->postJson(route('payment.tripay.callback'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Pembayaran berhasil diproses, posko aktif.',
            ]);

        $user->refresh();
        $this->assertEquals('host', $user->role);
        $this->assertNotNull($user->subscription_expires_at);
        $this->assertTrue($user->subscription_expires_at->isFuture());
    }

    /**
     * Test webhook with invalid signature is rejected.
     */
    public function test_webhook_with_invalid_signature_is_rejected()
    {
        $payload = [
            'reference' => 'T1234567890',
            'merchant_ref' => 'SUB-1-12345678',
            'status' => 'PAID',
            'total_amount' => 100000,
        ];

        $response = $this->withHeaders([
            'X-Callback-Signature' => 'invalid_signature_hash',
        ])->postJson(route('payment.tripay.callback'), $payload);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Signature tidak valid.',
            ]);
    }

    /**
     * Test webhook with missing signature is rejected.
     */
    public function test_webhook_with_missing_signature_is_rejected()
    {
        $payload = [
            'reference' => 'T1234567890',
            'merchant_ref' => 'SUB-1-12345678',
            'status' => 'PAID',
            'total_amount' => 100000,
        ];

        $response = $this->postJson(route('payment.tripay.callback'), $payload);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Signature tidak ditemukan.',
            ]);
    }

    /**
     * Test webhook with unpaid status does not change user role.
     */
    public function test_webhook_with_unpaid_status_does_not_upgrade_user()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $payload = [
            'reference' => 'T1234567890',
            'merchant_ref' => 'SUB-' . $user->id . '-12345678',
            'status' => 'UNPAID',
            'total_amount' => 100000,
        ];

        $jsonPayload = json_encode($payload);
        $signature = hash_hmac('sha256', $jsonPayload, $this->privateKey);

        $response = $this->withHeaders([
            'X-Callback-Signature' => $signature,
        ])->postJson(route('payment.tripay.callback'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Callback diterima namun tidak memicu aksi.',
            ]);

        $user->refresh();
        $this->assertEquals('user', $user->role);
        $this->assertNull($user->subscription_expires_at);
    }
}
