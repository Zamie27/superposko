<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserBanTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_ban_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'host']);

        $this->actingAs($admin);

        $response = $this->postJson(route('admin.users.ban', $user));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => "User {$user->name} berhasil di-ban.",
        ]);

        $user->refresh();
        $this->assertNotNull($user->banned_at);
    }

    public function test_admin_can_unban_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create([
            'role' => 'host',
            'banned_at' => now(),
        ]);

        $this->actingAs($admin);

        $response = $this->postJson(route('admin.users.unban', $user));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => "Ban untuk user {$user->name} berhasil dibatalkan.",
        ]);

        $user->refresh();
        $this->assertNull($user->banned_at);
    }

    public function test_banned_user_is_redirected_to_banned_page(): void
    {
        $user = User::factory()->create([
            'role' => 'host',
            'banned_at' => now(),
        ]);

        $this->actingAs($user);

        // Accessing dashboard redirects to banned
        $response = $this->get(route('host.dashboard'));
        $response->assertRedirect(route('banned'));
    }

    public function test_banned_user_can_access_banned_page_and_logout(): void
    {
        $user = User::factory()->create([
            'role' => 'host',
            'banned_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->get(route('banned'));
        $response->assertOk();
    }
}
