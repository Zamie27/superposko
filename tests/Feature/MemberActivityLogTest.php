<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberActivityLogTest extends TestCase
{
    use RefreshDatabase;

    // --- DASHBOARD TESTS ---

    public function test_guests_cannot_access_dashboard()
    {
        $response = $this->get(route('host.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_subscribed_host_can_access_dashboard_with_all_props()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $response = $this->get(route('host.dashboard'));
        $response->assertOk();

        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('metrics')
            ->has('todayRoster')
            ->has('events')
        );
    }

    // --- ACTIVITY LOGS TESTS ---

    public function test_guests_cannot_access_member_activity_logs()
    {
        $response = $this->get(route('management.activity-logs.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_host_can_access_activity_logs_of_posko_members()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $member = User::factory()->create([
            'role' => 'anggota',
            'host_id' => $host->id,
        ]);

        // Create log entry for member
        ActivityLog::create([
            'user_id' => $member->id,
            'user_name' => $member->name,
            'user_email' => $member->email,
            'role' => $member->role,
            'category' => 'member',
            'action' => 'test_action',
            'description' => 'User did a test action.',
        ]);

        $response = $this->get(route('management.activity-logs.index'));
        $response->assertOk();

        $response->assertInertia(fn ($page) => $page
            ->component('management/activity-logs/Index')
            ->has('logs.data')
            ->has('members')
            ->has('categories')
        );
    }

    public function test_host_cannot_see_activity_logs_of_other_posko_members()
    {
        $host1 = User::factory()->create(['role' => 'host']);
        $host2 = User::factory()->create(['role' => 'host']);

        $member1 = User::factory()->create([
            'role' => 'anggota',
            'host_id' => $host1->id,
        ]);

        // Log entry for member of Posko 1
        ActivityLog::create([
            'user_id' => $member1->id,
            'user_name' => $member1->name,
            'user_email' => $member1->email,
            'role' => $member1->role,
            'category' => 'member',
            'action' => 'posko_1_action',
            'description' => 'Posko 1 action description.',
        ]);

        // Act as Host 2
        $this->actingAs($host2);

        $response = $this->get(route('management.activity-logs.index'));
        $response->assertOk();

        // Host 2's activity logs list should NOT contain Posko 1's action description
        $logsData = $response->original->getData()['page']['props']['logs']['data'];
        $this->assertEmpty($logsData);
    }
}
