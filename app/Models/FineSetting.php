<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FineSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'late_fee_per_day',
        'max_fine_amount',
        'grace_period_days',
        'default_loan_duration_days',
        'updated_by',
    ];

    protected $casts = [
        'late_fee_per_day' => 'decimal:2',
        'max_fine_amount' => 'decimal:2',
        'grace_period_days' => 'integer',
        'default_loan_duration_days' => 'integer',
    ];

    public function histories()
    {
        return $this->hasMany(FineSettingHistory::class)->latest('changed_at');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
