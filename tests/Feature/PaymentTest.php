<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_payment_test_page()
    {
        $response = $this->get(route('host.payment.test'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_payment_test_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('host.payment.test'));

        $response->assertOk();
    }

    public function test_authenticated_user_can_create_snap_token()
    {
        $user = User::factory()->create(['role' => 'host']);

        $mockMidtrans = Mockery::mock(MidtransService::class);
        $mockMidtrans->shouldReceive('createSnapTransaction')
            ->once()
            ->andReturn([
                'token' => 'mocked-snap-token-123',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/mocked-snap-token-123',
            ]);

        $this->app->instance(MidtransService::class, $mockMidtrans);

        $response = $this->actingAs($user)
            ->postJson(route('host.payment.token'));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'token' => 'mocked-snap-token-123',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/mocked-snap-token-123',
            ]);
    }
}
