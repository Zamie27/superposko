<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailVerificationOtpController extends Controller
{
    /**
     * Verify the email using OTP code.
     */
    public function verify(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $otpInput = $request->input('otp');

        $expiresAt = $user->verification_otp_expires_at;
        if ($user->verification_otp !== $otpInput || 
            !$expiresAt || 
            \Illuminate\Support\Carbon::parse($expiresAt)->isPast()) {
            
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => 'Kode OTP tidak valid atau telah kedaluwarsa.',
            ]);

            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau telah kedaluwarsa.']);
        }

        // Mark user as verified
        $user->forceFill([
            'email_verified_at' => now(),
            'verification_otp' => null,
            'verification_otp_expires_at' => null,
        ])->save();

        /** @var RedirectResponse $response */
        $response = redirect()->intended(route('dashboard'));
        
        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Email Anda berhasil diverifikasi! Selamat datang di SuperPosko.',
        ]);

        return $response;
    }

    /**
     * Resend verification OTP code.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        // Regenerate and send OTP
        $user->sendEmailVerificationNotification();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Kode OTP baru berhasil dikirim ke email Anda.',
        ]);

        return back();
    }
}
