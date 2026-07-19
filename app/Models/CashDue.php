<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $host_id
 * @property int $week_number
 * @property float $amount
 * @property int|null $finance_id
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property User $user
 * @property User $host
 * @property Finance|null $finance
 * @property User|null $creator
 */
class CashDue extends Model
{
    protected $table = 'cash_dues';

    protected $fillable = [
        'user_id',
        'host_id',
        'week_number',
        'amount',
        'finance_id',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'week_number' => 'integer',
        ];
    }

    /**
     * Get the user who is paying the dues.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the host/posko this dues belong to.
     *
     * @return BelongsTo<User, $this>
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    /**
     * Get the related finance record.
     *
     * @return BelongsTo<Finance, $this>
     */
    public function finance(): BelongsTo
    {
        return $this->belongsTo(Finance::class, 'finance_id');
    }

    /**
     * Get the user who recorded this.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
