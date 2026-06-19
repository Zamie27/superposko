<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class GoogleLoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(): SymfonyRedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal masuk dengan Google. Silakan coba lagi.',
            ]);
        }

        // Find or create the user
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            // If user exists but doesn't have a google_id, update it
            if (empty($user->google_id)) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);
            }
        } else {
            // Create user
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => null, // Password nullable for Google users
            ]);

            // Mark email as verified since it comes verified from Google
            $user->markEmailAsVerified();
        }

        Auth::login($user, true);

        return redirect()->intended(route('dashboard'));
    }
}
