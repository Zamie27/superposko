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

    public function test_guest_cannot_access_payment_checkout_page()
    {
        $response = $this->get(route('payment.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_payment_checkout_page()
    {
        \App\Models\Setting::set('preorder_promo_active', '0');
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('payment.index'));

        $response->assertOk();
    }

    public function test_authenticated_user_cannot_access_payment_checkout_page_when_preorder_active()
    {
        \App\Models\Setting::set('preorder_promo_active', '1');
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('payment.index'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_authenticated_user_can_create_snap_token()
    {
        \App\Models\Setting::set('preorder_promo_active', '0');
        $user = User::factory()->create(['role' => 'user']);

        $mockMidtrans = Mockery::mock(MidtransService::class);
        $mockMidtrans->shouldReceive('createSnapTransaction')
            ->once()
            ->andReturn([
                'token' => 'mocked-user-snap-token-xyz',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/mocked-user-snap-token-xyz',
            ]);

        $this->app->instance(MidtransService::class, $mockMidtrans);

        $response = $this->actingAs($user)
            ->postJson(route('payment.token_user'));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'token' => 'mocked-user-snap-token-xyz',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/mocked-user-snap-token-xyz',
            ]);
    }

    public function test_authenticated_user_cannot_create_snap_token_when_preorder_active()
    {
        \App\Models\Setting::set('preorder_promo_active', '1');
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)
            ->postJson(route('payment.token_user'));

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_verify_successful_payment()
    {
        \App\Models\Setting::set('preorder_promo_active', '0');
        $user = User::factory()->create(['role' => 'user']);

        $mockMidtrans = Mockery::mock(MidtransService::class);
        $mockMidtrans->shouldReceive('getTransactionStatus')
            ->once()
            ->with('SUB-12345')
            ->andReturn([
                'transaction_status' => 'settlement',
            ]);

        $this->app->instance(MidtransService::class, $mockMidtrans);

        $response = $this->actingAs($user)
            ->postJson(route('payment.success'), [
                'order_id' => 'SUB-12345',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $user->refresh();
        $this->assertEquals('host', $user->role);
        $this->assertNotNull($user->subscription_expires_at);
        $this->assertTrue($user->subscription_expires_at->isFuture());
    }

    public function test_guest_cannot_access_admin_test_payment()
    {
        $response = $this->get(route('admin.payment.test'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_access_admin_test_payment()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('admin.payment.test'));

        $response->assertForbidden();
    }

    public function test_admin_can_access_admin_test_payment()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.payment.test'));

        $response->assertOk();
    }
}
