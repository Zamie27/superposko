<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramKerja extends Model
{
    protected $table = 'program_kerjas';

    protected $fillable = [
        'host_id',
        'pic_id',
        'name',
        'category',
        'description',
        'progress',
        'budget',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'budget' => 'float',
        ];
    }

    /**
     * Get the host/posko this program belongs to.
     *
     * @return BelongsTo<User, $this>
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    /**
     * Get the user who is the Penanggung Jawab (PIC).
     *
     * @return BelongsTo<User, $this>
     */
    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    /**
     * Get the finance transactions linked to this program kerja.
     *
     * @return HasMany<Finance, $this>
     */
    public function finances(): HasMany
    {
        return $this->hasMany(Finance::class, 'program_kerja_id');
    }
}
