<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\SubscriptionRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubscriptionRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Setting::set('checkout_payment_method', 'qris_static');
    }

    public function test_user_can_submit_subscription_request()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)
            ->post(route('payment.qris.store'), [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'whatsapp' => '08123456789',
                'payment_proof' => UploadedFile::fake()->create('proof.jpg', 100, 'image/jpeg'),
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('subscription_requests', [
            'user_id' => $user->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'whatsapp' => '08123456789',
            'status' => 'pending',
        ]);

        $request = SubscriptionRequest::first();
        Storage::disk('public')->assertExists($request->payment_proof);
    }

    public function test_admin_can_approve_subscription_request()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $subRequest = SubscriptionRequest::create([
            'user_id' => $user->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'whatsapp' => '08123456789',
            'payment_proof' => 'proofs/dummy.jpg',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.subscription-requests.approve', $subRequest->id));

        $response->assertOk();

        $subRequest->refresh();
        $this->assertEquals('approved', $subRequest->status);

        $user->refresh();
        $this->assertEquals('host', $user->role);
        $this->assertNotNull($user->subscription_expires_at);
        $this->assertTrue($user->subscription_expires_at->isAfter(now()->addDays(39)));
    }

    public function test_admin_can_reject_subscription_request()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $subRequest = SubscriptionRequest::create([
            'user_id' => $user->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'whatsapp' => '08123456789',
            'payment_proof' => 'proofs/dummy.jpg',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.subscription-requests.reject', $subRequest->id), [
                'rejection_reason' => 'Transfer nominal kurang',
            ]);

        $response->assertOk();

        $subRequest->refresh();
        $this->assertEquals('rejected', $subRequest->status);
        $this->assertEquals('Transfer nominal kurang', $subRequest->rejection_reason);

        $user->refresh();
        $this->assertEquals('user', $user->role); // remains user
    }
}
