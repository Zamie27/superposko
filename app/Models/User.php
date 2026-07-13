<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $subscription_expires_at
 * @property Carbon|null $trial_ends_at
 * @property Carbon|null $banned_at
 * @property string|null $verification_otp
 * @property Carbon|null $verification_otp_expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'password', 'google_id', 'university', 'npm', 'group_number', 'kkn_address', 'role', 'custom_role_id', 'host_id', 'subscription_expires_at', 'trial_ends_at', 'banned_at', 'verification_otp', 'verification_otp_expires_at', 'immich_api_key', 'immich_email', 'immich_password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'subscription_expires_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'banned_at' => 'datetime',
            'verification_otp_expires_at' => 'datetime',
        ];
    }

    /**
     * Send OTP email verification notification instead of standard link.
     */
    public function sendEmailVerificationNotification(): void
    {
        $otp = (string) rand(100000, 999999);
        
        $this->forceFill([
            'verification_otp' => $otp,
            'verification_otp_expires_at' => now()->addMinutes(15),
        ])->save();

        \Illuminate\Support\Facades\Mail::to($this->email)->send(new \App\Mail\SendRegistrationOtpMail($otp));
    }

    /**
     * Get the members belonging to this host.
     *
     * @return HasMany<User, $this>
     */
    public function members(): HasMany
    {
        return $this->hasMany(User::class, 'host_id');
    }

    /**
     * Get the host this user belongs to.
     *
     * @return BelongsTo<User, $this>
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    /**
     * Get the preorders submitted by this user.
     *
     * @return HasMany<Preorder, $this>
     */
    public function preorders(): HasMany
    {
        return $this->hasMany(Preorder::class);
    }

    /**
     * Get the custom role if the user's role is 'lainnya'.
     *
     * @return BelongsTo<CustomRole, $this>
     */
    public function customRole(): BelongsTo
    {
        return $this->belongsTo(CustomRole::class);
    }
}
