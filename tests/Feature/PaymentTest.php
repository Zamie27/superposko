<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Services\TripayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_payment_checkout_page()
    {
        $response = $this->get(route('payment.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_payment_checkout_page()
    {
        Setting::set('preorder_promo_active', '0');
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('payment.index'));

        $response->assertOk();
    }

    public function test_authenticated_user_cannot_access_payment_checkout_page_when_preorder_active()
    {
        Setting::set('preorder_promo_active', '1');
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('payment.index'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_authenticated_user_can_create_tripay_payment()
    {
        Setting::set('preorder_promo_active', '0');
        $user = User::factory()->create(['role' => 'user']);

        $mockTripay = Mockery::mock(TripayService::class);
        $mockTripay->shouldReceive('createTransaction')
            ->once()
            ->andReturn([
                'checkout_url' => 'https://tripay.co.id/checkout/mocked-checkout-url-xyz',
            ]);

        $this->app->instance(TripayService::class, $mockTripay);

        $response = $this->actingAs($user)
            ->postJson(route('payment.tripay.create'));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'data' => [
                    'checkout_url' => 'https://tripay.co.id/checkout/mocked-checkout-url-xyz',
                ],
            ]);
    }

    public function test_authenticated_user_cannot_create_tripay_payment_when_preorder_active()
    {
        Setting::set('preorder_promo_active', '1');
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)
            ->postJson(route('payment.tripay.create'));

        $response->assertStatus(403);
    }
}
