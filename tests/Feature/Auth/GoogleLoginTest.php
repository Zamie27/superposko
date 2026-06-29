<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_redirect_route_redirects_to_google(): void
    {
        $response = $this->get(route('auth.google'));

        // Socialite redirect returns a Symfony RedirectResponse to Google accounts
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
    }

    public function test_google_callback_redirects_new_user_to_complete_profile(): void
    {
        $mockUser = $this->createMock(\Laravel\Socialite\Two\User::class);
        $mockUser->method('getId')->willReturn('google-id-123');
        $mockUser->method('getName')->willReturn('Budi Google');
        $mockUser->method('getEmail')->willReturn('budi.google@example.com');

        $mockProvider = $this->createMock(GoogleProvider::class);
        $mockProvider->method('user')->willReturn($mockUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($mockProvider);

        $response = $this->get(route('auth.google.callback'));

        // Should redirect to complete profile route
        $response->assertRedirect(route('auth.google.complete'));
        $this->assertEquals([
            'name' => 'Budi Google',
            'email' => 'budi.google@example.com',
            'google_id' => 'google-id-123',
        ], session('google_auth_user'));

        $this->assertGuest();
    }

    public function test_google_callback_logins_existing_user_with_completed_profile(): void
    {
        $existingUser = User::factory()->create([
            'email' => 'budi.google@example.com',
            'google_id' => 'google-id-123',
            'university' => 'Universitas Indonesia',
            'group_number' => 'Kelompok 1',
            'kkn_address' => 'Sleman, DIY',
        ]);

        $mockUser = $this->createMock(\Laravel\Socialite\Two\User::class);
        $mockUser->method('getId')->willReturn('google-id-123');
        $mockUser->method('getName')->willReturn('Budi Google');
        $mockUser->method('getEmail')->willReturn('budi.google@example.com');

        $mockProvider = $this->createMock(GoogleProvider::class);
        $mockProvider->method('user')->willReturn($mockUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($mockProvider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($existingUser);
    }

    public function test_complete_profile_page_can_be_rendered(): void
    {
        $response = $this->withSession([
            'google_auth_user' => [
                'name' => 'Budi Google',
                'email' => 'budi.google@example.com',
                'google_id' => 'google-id-123',
            ],
        ])->get(route('auth.google.complete'));

        $response->assertOk();
    }

    public function test_user_can_complete_profile_and_register_successfully(): void
    {
        $response = $this->withSession([
            'google_auth_user' => [
                'name' => 'Budi Google',
                'email' => 'budi.google@example.com',
                'google_id' => 'google-id-123',
            ],
        ])->post(route('auth.google.complete.store'), [
            'university' => 'Universitas Indonesia',
            'npm' => 'D1A230000',
            'group_number' => 'Kelompok 12',
            'kkn_address' => 'Jalan Kaliurang KM 12',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'budi.google@example.com',
            'google_id' => 'google-id-123',
            'university' => 'Universitas Indonesia',
            'npm' => 'D1A230000',
            'group_number' => 'Kelompok 12',
            'kkn_address' => 'Jalan Kaliurang KM 12',
        ]);
    }
}
