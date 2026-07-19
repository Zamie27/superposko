<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $host_id
 * @property string $source
 * @property int|null $owner_id
 * @property float|null $purchase_price
 * @property int|null $finance_id
 * @property string $name
 * @property float $quantity
 * @property string $unit
 * @property string $status
 * @property string|null $notes
 * @property User $host
 * @property User|null $owner
 * @property Finance|null $finance
 * @property \Illuminate\Support\Carbon|null $date
 */
class Logistic extends Model
{
    protected $fillable = [
        'host_id',
        'source',
        'owner_id',
        'purchase_price',
        'finance_id',
        'name',
        'quantity',
        'unit',
        'status',
        'notes',
        'date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'purchase_price' => 'float',
            'date' => 'date',
        ];
    }

    /**
     * Get the host/posko this logistic belongs to.
     *
     * @return BelongsTo<User, $this>
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    /**
     * Get the member who owns this logistic item.
     *
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the finance record created when this item was purchased from kas.
     *
     * @return BelongsTo<Finance, $this>
     */
    public function finance(): BelongsTo
    {
        return $this->belongsTo(Finance::class, 'finance_id');
    }
}
