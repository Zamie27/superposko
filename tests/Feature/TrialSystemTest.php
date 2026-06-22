<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TrialSystemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that registering a user via Fortify sets the role to trial.
     */
    public function test_user_registration_assigns_trial_role()
    {
        $response = $this->post('/register', [
            'name' => 'Trial User',
            'email' => 'trial@example.com',
            'university' => 'Universitas Indonesia',
            'group_number' => 'Group 42',
            'kkn_address' => 'Subang, Jabar',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('dashboard'));

        $user = User::where('email', 'trial@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('trial', $user->role);
    }

    /**
     * Test that a trial user under 5 days can access host dashboard and other standard host features.
     */
    public function test_active_trial_user_can_access_standard_host_features()
    {
        $user = User::factory()->create([
            'role' => 'trial',
            'created_at' => now(),
        ]);

        $this->actingAs($user);

        // Access dashboard
        $response = $this->get(route('host.dashboard'));
        $response->assertOk();

        // Access finance
        $response = $this->get(route('finance.index'));
        $response->assertOk();
    }

    /**
     * Test that an active trial user is blocked from Documentation and Member Management.
     */
    public function test_active_trial_user_is_blocked_from_locked_features()
    {
        $user = User::factory()->create([
            'role' => 'trial',
            'created_at' => now(),
        ]);

        $this->actingAs($user);

        // Access documentation index (GET)
        $response = $this->get(route('host.documentation.index'));
        $response->assertStatus(403);

        // Access members index (GET)
        $response = $this->get(route('management.members.index'));
        $response->assertStatus(403);
    }

    /**
     * Test that a trial user whose account is older than 5 days automatically reverts to 'user' role
     * and is blocked from host features.
     */
    public function test_trial_user_expires_after_5_days_and_reverts_to_user_role()
    {
        // Set created_at to 6 days ago
        $user = User::factory()->create([
            'role' => 'trial',
            'created_at' => Carbon::now()->subDays(6),
        ]);

        $this->actingAs($user);

        // Requesting a host feature (like finance) triggers middleware to update role to 'user' and block them
        $response = $this->get(route('finance.index'));
        $response->assertStatus(403);

        // Assert database role has updated to 'user'
        $user->refresh();
        $this->assertEquals('user', $user->role);
    }
}
