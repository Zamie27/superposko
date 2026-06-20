<?php

namespace Tests\Feature;

use App\Models\Preorder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PreorderTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_preorder_page(): void
    {
        $response = $this->get(route('user.preorder.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_preorder_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get(route('user.preorder.index'));
        $response->assertOk();
    }

    public function test_user_can_submit_preorder_with_valid_screenshot(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('proof.png', 100, 'image/png');

        $response = $this->post(route('user.preorder.store'), [
            'name' => 'John Doe',
            'email' => $user->email,
            'whatsapp' => '08123456789',
            'payment_proof' => $file,
        ]);

        $response->assertRedirect();

        $preorder = Preorder::first();
        $this->assertNotNull($preorder);
        $this->assertEquals('John Doe', $preorder->name);
        $this->assertEquals('pending', $preorder->status);
        Storage::disk('public')->assertExists($preorder->payment_proof);
    }

    public function test_admin_can_approve_preorder_and_activate_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'member']);

        $preorder = Preorder::create([
            'user_id' => $user->id,
            'name' => 'John Preorder',
            'email' => $user->email,
            'whatsapp' => '08123456789',
            'payment_proof' => 'preorders/fake_proof.jpg',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);

        $response = $this->postJson(route('admin.preorders.approve', $preorder));

        $response->assertOk();
        $preorder->refresh();
        $user->refresh();

        $this->assertEquals('approved', $preorder->status);
        $this->assertEquals('host', $user->role);
        $this->assertNotNull($user->subscription_expires_at);
        $this->assertTrue($user->subscription_expires_at->isFuture());
    }

    public function test_host_and_admin_cannot_access_preorder_page(): void
    {
        $host = User::factory()->create(['role' => 'host']);
        $this->actingAs($host);

        $response = $this->get(route('user.preorder.index'));
        $response->assertRedirect(route('host.dashboard'));

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get(route('user.preorder.index'));
        $response->assertRedirect(route('admin.dashboard'));
    }
}
