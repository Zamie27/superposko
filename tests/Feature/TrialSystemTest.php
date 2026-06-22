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
     * Test that an active trial user can view (GET) locked pages with blurred UI but is blocked from write actions.
     */
    public function test_active_trial_user_is_blocked_from_locked_features()
    {
        $user = User::factory()->create([
            'role' => 'trial',
            'created_at' => now(),
        ]);

        $this->actingAs($user);

        // Access documentation index (GET) should succeed (to show blurred overlay)
        $response = $this->get(route('host.documentation.index'));
        $response->assertOk();

        // Write to documentation (POST) should be blocked (403)
        $responsePostDoc = $this->post(route('host.documentation.upload'), []);
        $responsePostDoc->assertStatus(403);

        // Access members index (GET) should succeed
        $responseMembers = $this->get(route('management.members.index'));
        $responseMembers->assertOk();

        // Add member (POST) should be blocked (403)
        $responsePostMember = $this->post(route('management.members.store'), [
            'name' => 'Failed Member',
            'email' => 'failed@example.com',
            'password' => 'Password123!',
            'role' => 'anggota',
        ]);
        $responsePostMember->assertStatus(403);
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

    /**
     * Test that a trial user can access preorder and payment checkout pages.
     */
    public function test_trial_user_can_access_preorder_and_payment_checkout()
    {
        $user = User::factory()->create([
            'role' => 'trial',
            'created_at' => now(),
        ]);

        $this->actingAs($user);

        // Access preorder index
        $response = $this->get(route('preorder.index'));
        $response->assertOk();

        // Access payment index
        $response = $this->get(route('payment.index'));
        $response->assertOk();
    }
}
