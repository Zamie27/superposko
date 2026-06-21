<?php

namespace Tests\Feature;

use App\Models\ProkerDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProkerDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_guests_cannot_access_repository()
    {
        $response = $this->get(route('repository.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_unsubscribed_users_cannot_access_repository()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get(route('repository.index'));
        $response->assertStatus(403);
    }

    public function test_subscribed_hosts_can_access_repository()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $response = $this->get(route('repository.index'));
        $response->assertOk();
    }

    public function test_host_can_upload_document()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $file = UploadedFile::fake()->create('proposal_sanitasi.pdf', 500, 'application/pdf');

        $payload = [
            'title' => 'Proposal Sanitasi Dusun III',
            'category' => 'proposal',
            'description' => 'Proposal pengajuan dana pembangunan MCK umum.',
            'file' => $file,
        ];

        $response = $this->post(route('repository.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('proker_documents', [
            'host_id' => $host->id,
            'title' => 'Proposal Sanitasi Dusun III',
            'category' => 'proposal',
            'file_name' => 'proposal_sanitasi.pdf',
            'mime_type' => 'application/pdf',
        ]);

        $document = ProkerDocument::first();
        Storage::assertExists($document->file_path);
    }

    public function test_host_can_download_document()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $file = UploadedFile::fake()->create('notulensi.docx', 100);
        $path = $file->store("proker_documents/{$host->id}");

        $document = ProkerDocument::create([
            'host_id' => $host->id,
            'uploaded_by' => $host->id,
            'title' => 'Notulensi Rapat Pleno',
            'category' => 'notulensi',
            'file_path' => $path,
            'file_name' => 'notulensi.docx',
            'file_size' => 102400,
            'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);

        $response = $this->get(route('repository.download', $document));
        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename="notulensi.docx"');
    }

    public function test_host_cannot_download_other_hosts_document()
    {
        $host1 = User::factory()->create(['role' => 'host']);
        $host2 = User::factory()->create(['role' => 'host']);

        $file = UploadedFile::fake()->create('rahasia.pdf', 100);
        $path = $file->store("proker_documents/{$host1->id}");

        $document = ProkerDocument::create([
            'host_id' => $host1->id,
            'uploaded_by' => $host1->id,
            'title' => 'Rahasia Posko 1',
            'category' => 'lainnya',
            'file_path' => $path,
            'file_name' => 'rahasia.pdf',
            'file_size' => 102400,
            'mime_type' => 'application/pdf',
        ]);

        $this->actingAs($host2);
        $response = $this->get(route('repository.download', $document));
        $response->assertStatus(403);
    }

    public function test_host_can_delete_document()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $file = UploadedFile::fake()->create('brosur.png', 200);
        $path = $file->store("proker_documents/{$host->id}");

        $document = ProkerDocument::create([
            'host_id' => $host->id,
            'uploaded_by' => $host->id,
            'title' => 'Brosur KKN',
            'category' => 'desain',
            'file_path' => $path,
            'file_name' => 'brosur.png',
            'file_size' => 204800,
            'mime_type' => 'image/png',
        ]);

        Storage::assertExists($path);

        $response = $this->delete(route('repository.destroy', $document));
        $response->assertRedirect();

        $this->assertDatabaseMissing('proker_documents', [
            'id' => $document->id,
        ]);
        Storage::assertMissing($path);
    }

    public function test_host_can_view_document_inline()
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $file = UploadedFile::fake()->create('raport.pdf', 150);
        $path = $file->store("proker_documents/{$host->id}");

        $document = ProkerDocument::create([
            'host_id' => $host->id,
            'uploaded_by' => $host->id,
            'title' => 'Laporan Progres',
            'category' => 'lpj',
            'file_path' => $path,
            'file_name' => 'raport.pdf',
            'file_size' => 153600,
            'mime_type' => 'application/pdf',
        ]);

        $response = $this->get(route('repository.view', $document));
        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'inline; filename="raport.pdf"');
    }

    public function test_host_cannot_view_other_hosts_document_inline()
    {
        $host1 = User::factory()->create(['role' => 'host']);
        $host2 = User::factory()->create(['role' => 'host']);

        $file = UploadedFile::fake()->create('raport.pdf', 150);
        $path = $file->store("proker_documents/{$host1->id}");

        $document = ProkerDocument::create([
            'host_id' => $host1->id,
            'uploaded_by' => $host1->id,
            'title' => 'Laporan Progres Host 1',
            'category' => 'lpj',
            'file_path' => $path,
            'file_name' => 'raport.pdf',
            'file_size' => 153600,
            'mime_type' => 'application/pdf',
        ]);

        $this->actingAs($host2);
        $response = $this->get(route('repository.view', $document));
        $response->assertStatus(403);
    }
}
