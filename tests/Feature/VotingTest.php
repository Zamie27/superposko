<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Aspiration;
use App\Models\AspirationLike;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VotingTest extends TestCase
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
        $response = $this->get(route('voting.index'));
        $response->assertRedirect('/login');
    }

    public function test_user_can_view_voting_dashboard(): void
    {
        $response = $this->actingAs($this->member)->get(route('voting.index'));
        $response->assertStatus(200);
    }

    public function test_host_can_create_poll(): void
    {
        $response = $this->actingAs($this->host)->post(route('voting.poll.store'), [
            'title' => 'Pilih Seragam KKN',
            'description' => 'Pilih warna kaos posko kita',
            'options' => ['Biru Dongker', 'Hitam Polos', 'Merah Maroon'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('polls', [
            'title' => 'Pilih Seragam KKN',
            'host_id' => $this->host->id,
        ]);
        $this->assertDatabaseCount('poll_options', 3);
    }

    public function test_regular_member_cannot_create_poll(): void
    {
        $response = $this->actingAs($this->member)->post(route('voting.poll.store'), [
            'title' => 'Pilih Seragam KKN',
            'options' => ['Biru Dongker', 'Hitam Polos'],
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_cast_vote(): void
    {
        $poll = Poll::create([
            'host_id' => $this->host->id,
            'title' => 'Voting Menu Makan Siang',
            'created_by' => $this->host->id,
        ]);

        $option = PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Ayam Geprek',
        ]);

        $response = $this->actingAs($this->member)->post(route('voting.poll.vote', $poll), [
            'poll_option_id' => $option->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('poll_votes', [
            'poll_id' => $poll->id,
            'poll_option_id' => $option->id,
            'user_id' => $this->member->id,
        ]);
    }

    public function test_user_cannot_vote_twice(): void
    {
        $poll = Poll::create([
            'host_id' => $this->host->id,
            'title' => 'Voting Menu Makan Siang',
            'created_by' => $this->host->id,
        ]);

        $option1 = PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Ayam Geprek',
        ]);

        $option2 = PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Pecel Lele',
        ]);

        // First vote
        $this->actingAs($this->member)->post(route('voting.poll.vote', $poll), [
            'poll_option_id' => $option1->id,
        ]);

        // Second vote attempt
        $response = $this->actingAs($this->member)->post(route('voting.poll.vote', $poll), [
            'poll_option_id' => $option2->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('poll_votes', 1);
    }

    public function test_user_can_cancel_vote(): void
    {
        $poll = Poll::create([
            'host_id' => $this->host->id,
            'title' => 'Voting Cancel Test',
            'created_by' => $this->host->id,
        ]);

        $option = PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Option 1',
        ]);

        // Cast vote first
        $this->actingAs($this->member)->post(route('voting.poll.vote', $poll), [
            'poll_option_id' => $option->id,
        ]);

        $this->assertDatabaseHas('poll_votes', [
            'poll_id' => $poll->id,
            'user_id' => $this->member->id,
        ]);

        // Cancel vote
        $response = $this->actingAs($this->member)->delete(route('voting.poll.vote.destroy', $poll));

        $response->assertRedirect();
        $this->assertDatabaseMissing('poll_votes', [
            'poll_id' => $poll->id,
            'user_id' => $this->member->id,
        ]);
    }


    public function test_user_cannot_vote_on_expired_poll(): void
    {
        $poll = Poll::create([
            'host_id' => $this->host->id,
            'title' => 'Voting Selesai',
            'created_by' => $this->host->id,
            'expires_at' => now()->subHour(),
        ]);

        $option = PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Opsi A',
        ]);

        $response = $this->actingAs($this->member)->post(route('voting.poll.vote', $poll), [
            'poll_option_id' => $option->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('poll_votes', 0);
    }

    public function test_host_can_delete_poll(): void
    {
        $poll = Poll::create([
            'host_id' => $this->host->id,
            'title' => 'Voting Dihapus',
            'created_by' => $this->host->id,
        ]);

        $response = $this->actingAs($this->host)->delete(route('voting.poll.destroy', $poll));
        $response->assertRedirect();
        $this->assertModelMissing($poll);
    }

    public function test_user_can_submit_aspiration(): void
    {
        $response = $this->actingAs($this->member)->post(route('voting.aspiration.store'), [
            'title' => 'Saran Kebersihan Posko',
            'content' => 'Mohon dibuat piket harian menyapu halaman depan posko.',
            'is_anonymous' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('aspirations', [
            'title' => 'Saran Kebersihan Posko',
            'is_anonymous' => true,
            'host_id' => $this->host->id,
        ]);
    }

    public function test_user_can_toggle_like_on_aspiration(): void
    {
        $aspiration = Aspiration::create([
            'host_id' => $this->host->id,
            'title' => 'Saran Piket',
            'content' => 'Isi saran',
            'user_id' => $this->member->id,
            'is_anonymous' => false,
        ]);

        // Toggle like (On)
        $response = $this->actingAs($this->member)->post(route('voting.aspiration.like', $aspiration));
        $response->assertRedirect();
        $this->assertDatabaseHas('aspiration_likes', [
            'aspiration_id' => $aspiration->id,
            'user_id' => $this->member->id,
        ]);

        // Toggle like (Off)
        $response = $this->actingAs($this->member)->post(route('voting.aspiration.like', $aspiration));
        $response->assertRedirect();
        $this->assertDatabaseMissing('aspiration_likes', [
            'aspiration_id' => $aspiration->id,
            'user_id' => $this->member->id,
        ]);
    }

    public function test_host_can_respond_to_aspiration(): void
    {
        $aspiration = Aspiration::create([
            'host_id' => $this->host->id,
            'title' => 'Saran Piket',
            'content' => 'Isi saran',
            'user_id' => $this->member->id,
            'is_anonymous' => false,
        ]);

        $response = $this->actingAs($this->host)->put(route('voting.aspiration.respond', $aspiration), [
            'status' => 'resolved',
            'admin_response' => 'Jadwal piket sudah diperbarui dan ditempel di mading posko.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('aspirations', [
            'id' => $aspiration->id,
            'status' => 'resolved',
            'admin_response' => 'Jadwal piket sudah diperbarui dan ditempel di mading posko.',
        ]);
    }

    public function test_posko_isolation_prevents_voting_in_other_groups(): void
    {
        // Poll belongs to Posko B
        $poll = Poll::create([
            'host_id' => $this->otherHost->id,
            'title' => 'Voting Posko B',
            'created_by' => $this->otherHost->id,
        ]);

        $option = PollOption::create([
            'poll_id' => $poll->id,
            'option_text' => 'Opsi Posko B',
        ]);

        // User from Posko A tries to vote on Posko B's poll
        $response = $this->actingAs($this->member)->post(route('voting.poll.vote', $poll), [
            'poll_option_id' => $option->id,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('poll_votes', 0);
    }
}
