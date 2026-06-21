<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'type',
        'title',
        'description',
        'status',
    ];

    /**
     * Get the user that submitted the report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
