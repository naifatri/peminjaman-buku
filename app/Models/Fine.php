<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrowing_id',
        'amount',
        'damage_amount',
        'days_late',
        'late_fee_per_day',
        'max_fine_amount',
        'grace_period_days',
        'raw_late_days',
        'charged_late_days',
        'late_fee_subtotal',
        'status',
        'paid_at',
        'payment_method',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'damage_amount' => 'decimal:2',
        'days_late' => 'integer',
        'late_fee_per_day' => 'decimal:2',
        'max_fine_amount' => 'decimal:2',
        'grace_period_days' => 'integer',
        'raw_late_days' => 'integer',
        'charged_late_days' => 'integer',
        'late_fee_subtotal' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected $appends = [
        'status_label',
        'status_color',
        'late_fee_total',
        'payment_age_in_days',
    ];

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'lunas' ? 'Lunas' : 'Belum Bayar';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status === 'lunas'
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
            : 'bg-rose-50 text-rose-700 border-rose-200';
    }

    public function getLateFeeTotalAttribute(): float
    {
        return (float) $this->late_fee_subtotal;
    }

    public function getPaymentAgeInDaysAttribute(): int
    {
        $referenceDate = $this->paid_at instanceof \Illuminate\Support\Carbon
            ? $this->paid_at->copy()->startOfDay()
            : now()->startOfDay();

        return $this->created_at?->copy()->startOfDay()->diffInDays($referenceDate) ?? 0;
    }
}
