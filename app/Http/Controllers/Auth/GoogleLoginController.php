<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
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

        // Find existing user by google_id or email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        // If user exists and has university/KKN data completed, login immediately
        if ($user && ! empty($user->university)) {
            if (empty($user->google_id)) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);
            }
            Auth::login($user, true);

            return redirect()->intended(route('dashboard'));
        }

        // Otherwise (new user or incomplete profile), store Google details in session and redirect to complete profile form
        session([
            'google_auth_user' => [
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
            ],
        ]);

        return redirect()->route('auth.google.complete');
    }

    /**
     * Show the profile completion form for Google registration.
     */
    public function showCompleteProfile(): RedirectResponse|Response
    {
        $googleUser = session('google_auth_user');

        if (! $googleUser) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi Google Anda telah berakhir. Silakan coba masuk kembali.',
            ]);
        }

        return Inertia::render('auth/CompleteGoogleProfile', [
            'name' => $googleUser['name'],
            'email' => $googleUser['email'],
        ]);
    }

    /**
     * Complete user registration after Google authentication.
     */
    public function completeProfile(Request $request): RedirectResponse
    {
        $googleUser = session('google_auth_user');

        if (! $googleUser) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi Google Anda telah berakhir. Silakan coba masuk kembali.',
            ]);
        }

        $input = $request->all();

        Validator::make($input, [
            'university' => ['required', 'string', 'max:255'],
            'npm' => ['required', 'string', 'min:8', 'max:20'],
            'group_number' => ['required', 'string', 'max:255'],
            'kkn_address' => ['required', 'string', 'max:1000'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols(), 'confirmed'],
        ])->validate();

        // Find existing user by email or create new
        $user = User::where('email', $googleUser['email'])->first();

        if (! $user) {
            $user = new User;
            $user->email = $googleUser['email'];
            $user->role = 'trial';
            $user->trial_ends_at = now()->addDays(5);
        }

        $user->name = $googleUser['name'];
        $user->google_id = $googleUser['google_id'];
        $user->university = $input['university'];
        $user->npm = $input['npm'];
        $user->group_number = $input['group_number'];
        $user->kkn_address = $input['kkn_address'];

        $user->password = Hash::make($input['password']);

        $user->save();

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Auth::login($user, true);

        session()->forget('google_auth_user');

        return redirect()->route('dashboard');
    }
}
