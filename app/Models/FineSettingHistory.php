<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FineSettingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'fine_setting_id',
        'changed_by',
        'old_late_fee_per_day',
        'new_late_fee_per_day',
        'old_max_fine_amount',
        'new_max_fine_amount',
        'old_grace_period_days',
        'new_grace_period_days',
        'old_default_loan_duration_days',
        'new_default_loan_duration_days',
        'changed_at',
    ];

    protected $casts = [
        'old_late_fee_per_day' => 'decimal:2',
        'new_late_fee_per_day' => 'decimal:2',
        'old_max_fine_amount' => 'decimal:2',
        'new_max_fine_amount' => 'decimal:2',
        'old_grace_period_days' => 'integer',
        'new_grace_period_days' => 'integer',
        'old_default_loan_duration_days' => 'integer',
        'new_default_loan_duration_days' => 'integer',
        'changed_at' => 'datetime',
    ];

    public function setting()
    {
        return $this->belongsTo(FineSetting::class, 'fine_setting_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
