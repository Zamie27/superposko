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

    public function test_google_callback_registers_and_logins_new_user(): void
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

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'budi.google@example.com',
            'google_id' => 'google-id-123',
            'name' => 'Budi Google',
        ]);

        $this->assertAuthenticated();
    }

    public function test_google_callback_logins_existing_user_and_links_google_id(): void
    {
        $existingUser = User::factory()->create([
            'email' => 'budi.google@example.com',
            'google_id' => null,
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

        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'email' => 'budi.google@example.com',
            'google_id' => 'google-id-123',
        ]);

        $this->assertAuthenticatedAs($existingUser);
    }
}
