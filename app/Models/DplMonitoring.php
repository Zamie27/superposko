<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DplMonitoring extends Model
{
    protected $fillable = [
        'dpl_id',
        'host_id',
        'status',
    ];

    /**
     * Get the DPL user.
     */
    public function dpl(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dpl_id');
    }

    /**
     * Get the host/posko user.
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }
}
