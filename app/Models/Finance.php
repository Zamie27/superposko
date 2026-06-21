<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Finance extends Model
{
    protected $table = 'finances';

    protected $fillable = [
        'host_id',
        'program_kerja_id',
        'created_by',
        'type',
        'amount',
        'title',
        'description',
        'date',
        'receipt_path',
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
            'date' => 'date',
        ];
    }

    /**
     * Get the host/posko this finance belongs to.
     *
     * @return BelongsTo<User, $this>
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    /**
     * Get the program kerja this finance is linked to.
     *
     * @return BelongsTo<ProgramKerja, $this>
     */
    public function programKerja(): BelongsTo
    {
        return $this->belongsTo(ProgramKerja::class, 'program_kerja_id');
    }

    /**
     * Get the user who recorded this transaction.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
