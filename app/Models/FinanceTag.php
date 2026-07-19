<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $host_id
 * @property string $name
 * @property string $type
 * @property User $host
 */
class FinanceTag extends Model
{
    protected $fillable = [
        'host_id',
        'name',
        'type',
    ];

    /**
     * Get the host/posko this tag belongs to.
     *
     * @return BelongsTo<User, $this>
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }
}
