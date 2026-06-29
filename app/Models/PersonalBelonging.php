<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalBelonging extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'quantity',
        'unit',
        'is_packed_departure',
        'is_packed_return',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_packed_departure' => 'boolean',
            'is_packed_return' => 'boolean',
            'quantity' => 'integer',
        ];
    }

    /**
     * Get the user who owns this personal belonging item.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
