<?php

namespace App\Models;

use App\Services\FinePolicyService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'quantity',
        'borrow_reason',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'fine_amount',
        'late_fee_per_day',
        'max_fine_amount',
        'grace_period_days',
        'loan_duration_days',
        'return_notes',
        'admin_notes',
        'book_condition',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'fine_amount' => 'decimal:2',
        'late_fee_per_day' => 'decimal:2',
        'max_fine_amount' => 'decimal:2',
        'grace_period_days' => 'integer',
        'loan_duration_days' => 'integer',
    ];

    protected $appends = [
        'admin_status',
        'admin_status_label',
        'admin_status_color',
        'late_days',
        'calculated_late_fine',
        'chargeable_late_days',
        'grace_period_days_value',
        'max_fine_amount_value',
        'outstanding_fine_amount',
        'fine_payment_status_label',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function fine()
    {
        return $this->hasOne(Fine::class);
    }

    public function scopeActiveLoan($query)
    {
        return $query->whereNull('return_date')
            ->whereIn('status', ['dipinjam', 'terlambat']);
    }

    public function syncTimelineStatus(): bool
    {
        if ($this->return_date !== null) {
            return false;
        }

        $today = now()->startOfDay();
        $expectedStatus = $this->due_date !== null && $this->due_date->lt($today)
            ? 'terlambat'
            : 'dipinjam';

        if (! in_array($this->status, ['dipinjam', 'terlambat'], true) || $this->status === $expectedStatus) {
            return false;
        }

        $this->status = $expectedStatus;
        $this->save();

        return true;
    }

    public function getAdminStatusAttribute(): string
    {
        if (in_array($this->status, ['diajukan', 'ditolak', 'dikembalikan', 'verifikasi_denda', 'proses_bayar', 'selesai'], true)) {
            return $this->status;
        }

        if ($this->return_date !== null) {
            return $this->fine && $this->fine->status !== 'lunas' ? 'verifikasi_denda' : 'selesai';
        }

        return $this->isOverdue() ? 'terlambat' : 'dipinjam';
    }

    public function getAdminStatusLabelAttribute(): string
    {
        return match ($this->admin_status) {
            'diajukan' => 'Pengajuan',
            'ditolak' => 'Ditolak',
            'dikembalikan' => 'Menunggu Verifikasi Kembali',
            'verifikasi_denda' => 'Menunggu Pembayaran Denda',
            'proses_bayar' => 'Verifikasi Pembayaran',
            'selesai' => 'Selesai',
            'terlambat' => 'Terlambat',
            default => 'Dipinjam',
        };
    }

    public function getAdminStatusColorAttribute(): string
    {
        return match ($this->admin_status) {
            'diajukan' => 'bg-slate-100 text-slate-700 border-slate-200',
            'ditolak' => 'bg-slate-200 text-slate-700 border-slate-300',
            'dikembalikan' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'verifikasi_denda' => 'bg-rose-50 text-rose-700 border-rose-200',
            'proses_bayar' => 'bg-amber-50 text-amber-700 border-amber-200',
            'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'terlambat' => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    }

    public function isReturned(): bool
    {
        return $this->return_date !== null || in_array($this->status, ['dikembalikan', 'selesai'], true);
    }

    public function isOverdue(?Carbon $referenceDate = null): bool
    {
        if ($this->isReturned() || $this->due_date === null) {
            return false;
        }

        $referenceDate ??= now()->startOfDay();

        return $this->due_date->lt($referenceDate);
    }

    public function lateDays(?Carbon $referenceDate = null): int
    {
        $referenceDate ??= $this->return_date?->copy()->startOfDay() ?? now()->startOfDay();

        if ($this->due_date === null || $referenceDate->lte($this->due_date)) {
            return 0;
        }

        return $this->due_date->diffInDays($referenceDate);
    }

    public function lateFeePerDay(): float
    {
        return (float) app(FinePolicyService::class)->borrowingPolicy($this)['late_fee_per_day'];
    }

    public function gracePeriodDays(): int
    {
        return (int) app(FinePolicyService::class)->borrowingPolicy($this)['grace_period_days'];
    }

    public function maxFineAmount(): ?float
    {
        return app(FinePolicyService::class)->borrowingPolicy($this)['max_fine_amount'];
    }

    public function chargeableLateDays(?Carbon $referenceDate = null): int
    {
        return (int) app(FinePolicyService::class)
            ->calculateLateFee($this->lateDays($referenceDate), app(FinePolicyService::class)->borrowingPolicy($this))['charged_late_days'];
    }

    public function calculatedLateFine(?Carbon $referenceDate = null): float
    {
        return (float) app(FinePolicyService::class)
            ->calculateLateFee($this->lateDays($referenceDate), app(FinePolicyService::class)->borrowingPolicy($this))['late_fee_subtotal'];
    }

    public function outstandingFineAmount(): float
    {
        return (float) ($this->fine?->amount ?? $this->fine_amount ?? 0);
    }

    public function getLateDaysAttribute(): int
    {
        return $this->lateDays();
    }

    public function getCalculatedLateFineAttribute(): float
    {
        return $this->calculatedLateFine();
    }

    public function getChargeableLateDaysAttribute(): int
    {
        return $this->chargeableLateDays();
    }

    public function getGracePeriodDaysValueAttribute(): int
    {
        return $this->gracePeriodDays();
    }

    public function getMaxFineAmountValueAttribute(): ?float
    {
        return $this->maxFineAmount();
    }

    public function getOutstandingFineAmountAttribute(): float
    {
        return $this->outstandingFineAmount();
    }

    public function getFinePaymentStatusLabelAttribute(): string
    {
        if (! $this->fine || $this->outstanding_fine_amount <= 0) {
            return 'Tidak ada denda';
        }

        return $this->fine->status === 'lunas' ? 'Sudah bayar' : 'Belum bayar';
    }
}
