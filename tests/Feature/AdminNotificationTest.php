<?php

namespace Tests\Feature;

use App\Mail\AdminAnnouncementMail;
use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guest users cannot access push subscription storage.
     */
    public function test_guests_cannot_store_push_subscriptions()
    {
        $response = $this->postJson(route('push_subscriptions.store'), [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/some-endpoint',
            'keys' => [
                'p256dh' => 'some-public-key',
                'auth' => 'some-auth-token',
            ],
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test that authenticated users can register a push subscription.
     */
    public function test_user_can_store_push_subscription()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('push_subscriptions.store'), [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/some-endpoint',
            'keys' => [
                'p256dh' => 'some-public-key',
                'auth' => 'some-auth-token',
            ],
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $user->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/some-endpoint',
        ]);
    }

    /**
     * Test that user can unsubscribe/delete a push subscription.
     */
    public function test_user_can_delete_push_subscription()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        PushSubscription::create([
            'user_id' => $user->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/some-endpoint',
            'public_key' => 'some-public-key',
            'auth_token' => 'some-auth-token',
        ]);

        $response = $this->deleteJson(route('push_subscriptions.destroy'), [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/some-endpoint',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('push_subscriptions', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/some-endpoint',
        ]);
    }

    /**
     * Test that non-admin users cannot access the admin notifications panel.
     */
    public function test_non_admin_cannot_view_admin_notifications()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get(route('admin.notifications.index'));
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test that admin can access the notifications panel.
     */
    public function test_admin_can_view_admin_notifications()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get(route('admin.notifications.index'));
        $response->assertOk();
    }

    /**
     * Test that admin can send email broadcasts to targeted users.
     */
    public function test_admin_can_send_email_broadcast()
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['role' => 'user', 'email' => 'user1@example.com']);
        $user2 = User::factory()->create(['role' => 'host', 'email' => 'host1@example.com']);

        $this->actingAs($admin);

        $response = $this->postJson(route('admin.notifications.send_email'), [
            'subject' => 'Server Maintenance',
            'body' => 'We will have server maintenance tomorrow.',
            'target' => 'all',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        Mail::assertQueued(AdminAnnouncementMail::class, function ($mail) use ($user1) {
            return $mail->hasTo('user1@example.com') && $mail->mailSubject === 'Server Maintenance';
        });

        Mail::assertQueued(AdminAnnouncementMail::class, function ($mail) use ($user2) {
            return $mail->hasTo('host1@example.com');
        });
    }
}
