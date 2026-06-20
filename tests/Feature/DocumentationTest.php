<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Set config for Immich
        config(['services.immich.url' => 'http://immich-local:2283']);
        config(['services.immich.api_key' => 'test-api-key']);
    }

    public function test_guests_are_redirected_from_documentation_index(): void
    {
        $response = $this->get(route('documentation.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_view_documentation_index_with_assets(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            'http://immich-local:2283/api/search/metadata' => Http::response([
                'assets' => [
                    'items' => [
                        [
                            'id' => 'asset-id-123',
                            'type' => 'IMAGE',
                            'fileCreatedAt' => '2026-06-20T20:00:00Z',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->get(route('documentation.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('documentation/Index')
            ->has('assets', 1)
            ->where('assets.0.id', 'asset-id-123')
            ->where('assets.0.type', 'IMAGE')
        );
    }

    public function test_chunk_upload_requires_validation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('documentation.upload_chunk'), []);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['file', 'chunkIndex', 'totalChunks', 'uploadUuid', 'filename']);
    }

    public function test_successful_chunked_upload_process(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Fake Immich asset upload
        Http::fake([
            'http://immich-local:2283/api/assets' => Http::response(['id' => 'new-asset-id'], 201),
        ]);

        $uuid = 'test-upload-uuid-123';
        $filename = 'test_video.mp4';

        // Chunk 1 of 2
        $fileChunk1 = UploadedFile::fake()->create('chunk0.bin', 100);
        $response1 = $this->postJson(route('documentation.upload_chunk'), [
            'file' => $fileChunk1,
            'chunkIndex' => 0,
            'totalChunks' => 2,
            'uploadUuid' => $uuid,
            'filename' => $filename,
        ]);

        $response1->assertOk();
        $response1->assertJson([
            'status' => 'uploading',
            'message' => 'Chunk ke-0 berhasil diunggah.',
        ]);

        // Verify chunk 1 is saved in storage
        $this->assertFileExists(storage_path("app/chunks/{$uuid}/0"));

        // Chunk 2 of 2 (Trigger completion)
        $fileChunk2 = UploadedFile::fake()->create('chunk1.bin', 100);
        $response2 = $this->postJson(route('documentation.upload_chunk'), [
            'file' => $fileChunk2,
            'chunkIndex' => 1,
            'totalChunks' => 2,
            'uploadUuid' => $uuid,
            'filename' => $filename,
        ]);

        $response2->assertOk();
        $response2->assertJson([
            'status' => 'success',
            'message' => 'File berhasil diunggah secara chunk.',
        ]);

        // Verify chunks directory and merged file are cleaned up
        $this->assertDirectoryDoesNotExist(storage_path("app/chunks/{$uuid}"));
    }
}
