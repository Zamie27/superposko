<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aspiration extends Model
{
    protected $fillable = [
        'host_id',
        'title',
        'content',
        'user_id',
        'is_anonymous',
        'status',
        'admin_response',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<AspirationLike, $this>
     */
    public function likes(): HasMany
    {
        return $this->hasMany(AspirationLike::class);
    }

    /**
     * Check if a specific user has liked this aspiration.
     */
    public function isLikedByUser(int $userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
}
