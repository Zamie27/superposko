<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailChangeOtp extends Model
{
    protected $fillable = [
        'user_id',
        'new_email',
        'otp_code',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the OTP.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
