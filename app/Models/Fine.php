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
        'status',
        'paid_at',
        'payment_method',
    ];

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }
}
