<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $fillable = [
        'host_id',
        'owner_id',
        'source',
        'purchase_price',
        'finance_id',
        'name',
        'quantity',
        'condition',
        'notes',
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
            'purchase_price' => 'float',
        ];
    }

    /**
     * Get the user who owns this inventory item.
     *
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the host/posko this inventory belongs to.
     *
     * @return BelongsTo<User, $this>
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
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
