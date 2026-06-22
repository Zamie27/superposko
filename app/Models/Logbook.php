<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $host_id
 * @property int $user_id
 * @property Carbon $date
 * @property string $title
 * @property string $description
 * @property string $activity_type
 * @property string|null $image_path
 * @property User $host
 * @property User $user
 */
class Logbook extends Model
{
    protected $fillable = [
        'host_id',
        'user_id',
        'date',
        'title',
        'description',
        'activity_type',
        'image_path',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    /**
     * Get the host/posko this logbook belongs to.
     *
     * @return BelongsTo<User, $this>
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    /**
     * Get the author member who logged this daily activity.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
