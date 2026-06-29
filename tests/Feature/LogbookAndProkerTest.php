<?php

namespace Tests\Feature;

use App\Models\Logbook;
use App\Models\ProgramKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LogbookAndProkerTest extends TestCase
{
    use RefreshDatabase;

    // --- ACCESSIBILITY TESTS ---

    public function test_guests_cannot_access_logbook_page()
    {
        $response = $this->get(route('logbook.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_unsubscribed_users_cannot_access_logbook_page()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get(route('logbook.index'));
        $response->assertStatus(403);
    }

    public function test_subscribed_hosts_can_access_logbook_page()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $response = $this->get(route('logbook.index'));
        $response->assertOk();
    }

    // --- PROGRAM KERJA CRUD TESTS ---

    public function test_host_can_create_program_kerja()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $payload = [
            'name' => 'Mengajar TPA',
            'category' => 'pendidikan',
            'description' => 'Meningkatkan literasi membaca Al-Quran',
            'progress' => 10,
            'budget' => 50000,
            'pic_id' => $host->id,
            'status' => 'planned',
        ];

        $response = $this->post(route('logbook.proker.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('program_kerjas', [
            'host_id' => $host->id,
            'name' => 'Mengajar TPA',
            'category' => 'pendidikan',
            'progress' => 10,
            'budget' => 50000,
            'pic_id' => $host->id,
            'status' => 'planned',
        ]);
    }

    public function test_unauthorized_member_cannot_create_program_kerja()
    {
        $host = User::factory()->create(['role' => 'host']);
        $member = User::factory()->create([
            'role' => 'anggota',
            'host_id' => $host->id,
        ]);
        $this->actingAs($member);

        $payload = [
            'name' => 'Illegal Proker',
            'category' => 'fisik',
            'progress' => 0,
            'budget' => 0,
            'status' => 'planned',
        ];

        $response = $this->post(route('logbook.proker.store'), $payload);
        $response->assertStatus(403);
    }

    public function test_pic_can_update_program_kerja()
    {
        $host = User::factory()->create(['role' => 'host']);
        $member = User::factory()->create([
            'role' => 'anggota',
            'host_id' => $host->id,
        ]);

        $proker = ProgramKerja::create([
            'host_id' => $host->id,
            'pic_id' => $member->id,
            'name' => 'Plangisasi',
            'category' => 'fisik',
            'progress' => 0,
            'budget' => 100000,
            'status' => 'planned',
        ]);

        // Member is PIC, so they can update progress
        $this->actingAs($member);

        $payload = [
            'name' => 'Plangisasi Baru',
            'category' => 'fisik',
            'progress' => 50,
            'budget' => 120000,
            'pic_id' => $member->id,
            'status' => 'in_progress',
        ];

        $response = $this->put(route('logbook.proker.update', $proker), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('program_kerjas', [
            'id' => $proker->id,
            'progress' => 50,
            'status' => 'in_progress',
            'budget' => 120000,
        ]);
    }

    public function test_non_pic_and_non_finance_member_cannot_update_program_kerja()
    {
        $host = User::factory()->create(['role' => 'host']);
        $pic = User::factory()->create(['role' => 'anggota', 'host_id' => $host->id]);
        $otherMember = User::factory()->create(['role' => 'anggota', 'host_id' => $host->id]);

        $proker = ProgramKerja::create([
            'host_id' => $host->id,
            'pic_id' => $pic->id,
            'name' => 'Plangisasi',
            'category' => 'fisik',
            'progress' => 0,
            'budget' => 100000,
            'status' => 'planned',
        ]);

        $this->actingAs($otherMember);

        $payload = [
            'name' => 'Hack Plang',
            'category' => 'fisik',
            'progress' => 100,
            'budget' => 0,
            'status' => 'completed',
        ];

        $response = $this->put(route('logbook.proker.update', $proker), $payload);
        $response->assertStatus(403);
    }

    // --- DAILY LOGBOOK CRUD TESTS ---

    public function test_member_can_create_logbook_with_image()
    {
        Storage::fake('public');

        $host = User::factory()->create(['role' => 'host']);
        $member = User::factory()->create([
            'role' => 'anggota',
            'host_id' => $host->id,
        ]);
        $this->actingAs($member);

        $image = UploadedFile::fake()->image('kegiatan.png');

        $payload = [
            'title' => 'Mengajar PAUD Desa',
            'date' => '2026-06-22',
            'description' => 'Kami mengajarkan mewarnai gambar kepada anak-anak PAUD',
            'activity_type' => 'community',
            'image' => $image,
        ];

        $response = $this->post(route('logbook.daily.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $logbook = Logbook::first();
        $this->assertNotNull($logbook->image_path);
        Storage::disk('public')->assertExists($logbook->image_path);

        $this->assertDatabaseHas('logbooks', [
            'host_id' => $host->id,
            'user_id' => $member->id,
            'title' => 'Mengajar PAUD Desa',
            'activity_type' => 'community',
        ]);
    }

    public function test_author_can_update_logbook()
    {
        $host = User::factory()->create(['role' => 'host']);
        $member = User::factory()->create(['role' => 'anggota', 'host_id' => $host->id]);
        $this->actingAs($member);

        $logbook = Logbook::create([
            'host_id' => $host->id,
            'user_id' => $member->id,
            'title' => 'Mengajar',
            'date' => '2026-06-22',
            'description' => 'Detail awal',
            'activity_type' => 'community',
        ]);

        $payload = [
            'title' => 'Mengajar Modifikasi',
            'date' => '2026-06-22',
            'description' => 'Detail baru yang diperbarui',
            'activity_type' => 'community',
        ];

        $response = $this->put(route('logbook.daily.update', $logbook), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('logbooks', [
            'id' => $logbook->id,
            'title' => 'Mengajar Modifikasi',
            'description' => 'Detail baru yang diperbarui',
        ]);
    }

    public function test_other_member_cannot_update_logbook()
    {
        $host = User::factory()->create(['role' => 'host']);
        $author = User::factory()->create(['role' => 'anggota', 'host_id' => $host->id]);
        $thief = User::factory()->create(['role' => 'anggota', 'host_id' => $host->id]);

        $logbook = Logbook::create([
            'host_id' => $host->id,
            'user_id' => $author->id,
            'title' => 'Logbook Author',
            'date' => '2026-06-22',
            'description' => 'Rahasia',
            'activity_type' => 'internal',
        ]);

        $this->actingAs($thief);

        $payload = [
            'title' => 'Hacked',
            'date' => '2026-06-22',
            'description' => 'Hacked description',
            'activity_type' => 'internal',
        ];

        $response = $this->put(route('logbook.daily.update', $logbook), $payload);
        $response->assertStatus(403);
    }
}
