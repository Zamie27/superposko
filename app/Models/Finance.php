<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $host_id
 * @property int|null $program_kerja_id
 * @property int $created_by
 * @property string $type
 * @property float $amount
 * @property string $title
 * @property string|null $description
 * @property string|null $category
 * @property Carbon $date
 * @property string|null $receipt_path
 * @property User $host
 * @property ProgramKerja|null $programKerja
 * @property User $creator
 */
class Finance extends Model
{
    protected $table = 'finances';

    protected $fillable = [
        'host_id',
        'program_kerja_id',
        'category',
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
