<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DutyRoster;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected User $host;
    protected User $member;
    protected User $otherHost;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Host posko A
        $this->host = User::factory()->create([
            'role' => 'host',
            'subscription_expires_at' => now()->addDays(30),
        ]);

        // Create Member of posko A
        $this->member = User::factory()->create([
            'role' => 'anggota',
            'host_id' => $this->host->id,
        ]);

        // Create Host posko B
        $this->otherHost = User::factory()->create([
            'role' => 'host',
            'subscription_expires_at' => now()->addDays(30),
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('management.schedule.index'));
        $response->assertRedirect('/login');
    }

    public function test_user_can_view_schedule_page(): void
    {
        $response = $this->actingAs($this->member)->get(route('management.schedule.index'));
        $response->assertOk();
    }

    public function test_host_can_assign_piket(): void
    {
        $response = $this->actingAs($this->host)->post(route('management.schedule.roster.store'), [
            'day_of_week' => 'monday',
            'task_name' => 'Masak & Belanja',
            'user_id' => $this->member->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('duty_rosters', [
            'host_id' => $this->host->id,
            'day_of_week' => 'monday',
            'task_name' => 'Masak & Belanja',
            'user_id' => $this->member->id,
        ]);
    }

    public function test_regular_member_cannot_assign_piket(): void
    {
        $response = $this->actingAs($this->member)->post(route('management.schedule.roster.store'), [
            'day_of_week' => 'monday',
            'task_name' => 'Masak & Belanja',
            'user_id' => $this->member->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_host_can_delete_piket(): void
    {
        $roster = DutyRoster::create([
            'host_id' => $this->host->id,
            'day_of_week' => 'monday',
            'task_name' => 'Kebersihan',
            'user_id' => $this->member->id,
        ]);

        $response = $this->actingAs($this->host)->delete(route('management.schedule.roster.destroy', $roster));
        $response->assertRedirect();
        $this->assertModelMissing($roster);
    }

    public function test_host_can_create_event(): void
    {
        $response = $this->actingAs($this->host)->post(route('management.schedule.event.store'), [
            'title' => 'Sosialisasi Program Kerja',
            'description' => 'Sosialisasi proker dengan warga desa di balai desa.',
            'start_time' => now()->addDay()->toDateTimeString(),
            'location' => 'Balai Desa',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('events', [
            'host_id' => $this->host->id,
            'title' => 'Sosialisasi Program Kerja',
            'location' => 'Balai Desa',
            'created_by' => $this->host->id,
        ]);
    }

    public function test_host_can_update_event(): void
    {
        $event = Event::create([
            'host_id' => $this->host->id,
            'title' => 'Rapat Koordinasi',
            'start_time' => now()->addDay(),
            'created_by' => $this->host->id,
        ]);

        $response = $this->actingAs($this->host)->put(route('management.schedule.event.update', $event), [
            'title' => 'Rapat Koordinasi Evaluasi',
            'start_time' => now()->addDays(2)->toDateTimeString(),
            'location' => 'Posko KKN',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Rapat Koordinasi Evaluasi',
            'location' => 'Posko KKN',
        ]);
    }

    public function test_host_can_delete_event(): void
    {
        $event = Event::create([
            'host_id' => $this->host->id,
            'title' => 'Kunjungan DPL',
            'start_time' => now()->addDay(),
            'created_by' => $this->host->id,
        ]);

        $response = $this->actingAs($this->host)->delete(route('management.schedule.event.destroy', $event));
        $response->assertRedirect();
        $this->assertModelMissing($event);
    }

    public function test_posko_isolation_prevents_modifying_other_group_schedule(): void
    {
        // Event belongs to Posko B
        $event = Event::create([
            'host_id' => $this->otherHost->id,
            'title' => 'Event Posko B',
            'start_time' => now()->addDay(),
            'created_by' => $this->otherHost->id,
        ]);

        // User from Posko A tries to delete it
        $response = $this->actingAs($this->host)->delete(route('management.schedule.event.destroy', $event));
        $response->assertStatus(403);
        $this->assertModelExists($event);
    }
}
