<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    protected $fillable = [
        'host_id',
        'title',
        'description',
        'image',
        'created_by',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Check if the poll is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * @return HasMany<PollOption, $this>
     */
    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    /**
     * @return HasMany<PollVote, $this>
     */
    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
