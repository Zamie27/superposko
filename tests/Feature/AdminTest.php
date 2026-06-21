<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_users_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'host']);
        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'Anda tidak memiliki akses administrator.');
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.dashboard'));
        $response->assertOk();
    }

    public function test_admin_can_update_prices(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->putJson(route('admin.prices.update'), [
            'packageName' => 'Paket Premium KKN',
            'packagePrice' => 120000,
            'packageStrikePrice' => 200000,
            'packageDescription' => 'Description test',
            'preorderPrice' => 60000,
            'preorderStrikePrice' => 120000,
            'preorderPromoActive' => true,
        ]);

        $response->assertRedirect();
        $this->assertEquals('Paket Premium KKN', Setting::get('package_name'));
        $this->assertEquals(120000, (int) Setting::get('package_price'));
    }

    public function test_admin_can_bypass_payment_and_grant_host_access(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'member']);

        $this->actingAs($admin);

        $response = $this->putJson(route('admin.subscriptions.bypass', $user));

        $response->assertOk();
        $user->refresh();
        $this->assertEquals('host', $user->role);
        $this->assertNotNull($user->subscription_expires_at);
        $this->assertTrue($user->subscription_expires_at->isFuture());
    }
}
