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
 * @property string $payment_method
 * @property string|null $destination_payment_method
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
        'payment_method',
        'destination_payment_method',
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

    /**
     * Calculate general kas balance (non-proker) for a specific payment method.
     */
    public static function getGeneralMethodBalance(int $hostId, string $method): float
    {
        $mIncome = self::where('host_id', $hostId)
            ->where('payment_method', $method)
            ->where('type', 'income')
            ->whereNull('program_kerja_id')
            ->sum('amount');
            
        $mReturns = self::where('host_id', $hostId)
            ->where('type', 'allocation')
            ->where('category', 'Proker ke Kas')
            ->where(function ($q) use ($method) {
                $q->where('destination_payment_method', $method)
                  ->orWhere(function ($q2) use ($method) {
                      $q2->whereNull('destination_payment_method')->where('payment_method', $method);
                  });
            })->sum('amount');
            
        $mTransferIn = self::where('host_id', $hostId)
            ->where('destination_payment_method', $method)
            ->where('type', 'transfer')
            ->whereNull('program_kerja_id')
            ->sum('amount');

        $mExpense = self::where('host_id', $hostId)
            ->where('payment_method', $method)
            ->where('type', 'expense')
            ->whereNull('program_kerja_id')
            ->sum('amount');
            
        $mAllocations = self::where('host_id', $hostId)
            ->where('payment_method', $method)
            ->where('type', 'allocation')
            ->where('category', 'Kas ke Proker')
            ->sum('amount');
            
        $mTransferOut = self::where('host_id', $hostId)
            ->where('payment_method', $method)
            ->where('type', 'transfer')
            ->whereNull('program_kerja_id')
            ->sum('amount');

        return (float) (($mIncome + $mReturns + $mTransferIn) - ($mExpense + $mAllocations + $mTransferOut));
    }
}
