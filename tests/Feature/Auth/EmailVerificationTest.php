<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered()
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertOk();
    }

    public function test_email_can_be_verified()
    {
        $user = User::factory()->unverified()->create([
            'verification_otp' => '123456',
            'verification_otp_expires_at' => now()->addMinutes(15),
        ]);

        $response = $this->actingAs($user)->post(route('verification.verify_otp'), [
            'otp' => '123456',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_is_not_verified_with_invalid_otp()
    {
        $user = User::factory()->unverified()->create([
            'verification_otp' => '123456',
            'verification_otp_expires_at' => now()->addMinutes(15),
        ]);

        $response = $this->actingAs($user)->post(route('verification.verify_otp'), [
            'otp' => '111111',
        ]);

        $response->assertSessionHasErrors(['otp']);
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
