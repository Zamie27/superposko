<?php

namespace App\Http\Controllers\Settings;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Mail\EmailChangedNotificationMail;
use App\Mail\SendEmailChangeOtpMail;
use App\Models\EmailChangeOtp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class EmailChangeController extends Controller
{
    /**
     * Send OTP code to the new email address.
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
                function ($attribute, $value, $fail) use ($user) {
                    if ($value === $user->email) {
                        $fail('Email baru tidak boleh sama dengan email aktif Anda.');
                    }
                },
            ],
        ]);

        $newEmail = $validated['email'];

        // Generate 6-digit OTP
        $otpCode = (string) rand(100000, 999999);

        // Delete any existing OTP records for this user
        EmailChangeOtp::where('user_id', $user->id)->delete();

        // Create new OTP record (valid for 10 minutes)
        EmailChangeOtp::create([
            'user_id' => $user->id,
            'new_email' => $newEmail,
            'otp_code' => $otpCode,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP mail
        try {
            Mail::to($newEmail)->send(new SendEmailChangeOtpMail($otpCode));
        } catch (\Exception $e) {
            /** @var RedirectResponse $response */
            $response = Inertia::flash('toast', [
                'type' => 'error',
                'message' => 'Gagal mengirim email OTP. Silakan periksa kembali email Anda.',
            ])->back();

            return $response;
        }

        /** @var RedirectResponse $response */
        $response = Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Kode OTP berhasil dikirim ke '.$newEmail,
        ])->back();

        return $response->with('otp_sent', true)->with('new_email_attempt', $newEmail);
    }

    /**
     * Verify OTP and change the user's email address.
     */
    public function verifyAndChange(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $newEmail = $validated['email'];
        $otpCode = $validated['otp'];

        // Find OTP record
        $otpRecord = EmailChangeOtp::where('user_id', $user->id)
            ->where('new_email', $newEmail)
            ->where('otp_code', $otpCode)
            ->where('expires_at', '>', now())
            ->first();

        if (! $otpRecord) {
            /** @var RedirectResponse $response */
            $response = Inertia::flash('toast', [
                'type' => 'error',
                'message' => 'Kode OTP tidak valid atau telah kedaluwarsa.',
            ])->back();

            return $response;
        }

        $oldEmail = $user->email;

        // Update user email and mark as verified (since we verified the OTP)
        $user->forceFill([
            'email' => $newEmail,
            'email_verified_at' => now(),
        ])->save();

        ActivityLogHelper::log(
            'auth',
            'change_email',
            "User changed their email address from {$oldEmail} to {$newEmail}.",
            $user
        );

        // Delete the verified OTP record
        $otpRecord->delete();

        // Send notification to old email
        $reportUrl = route('reports.create', [
            'email' => $oldEmail,
            'type' => 'security',
            'title' => 'Penggantian Email Tidak Sah',
            'desc' => "Email saya baru saja diganti tanpa persetujuan dari {$oldEmail} menjadi {$newEmail}. Tolong amankan kembali akun saya.",
        ]);

        try {
            Mail::to($oldEmail)->send(new EmailChangedNotificationMail($oldEmail, $newEmail, $reportUrl));
        } catch (\Exception $e) {
            // Log or ignore mail sending errors to old email so user still successfully updates
        }

        /** @var RedirectResponse $response */
        $response = Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Alamat email akun Anda berhasil diganti.',
        ])->back();

        return $response;
    }
}
