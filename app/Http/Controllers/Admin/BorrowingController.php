<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Borrowing;
use App\Services\FinePolicyService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BorrowingController extends Controller
{
    public function __construct(
        private readonly FinePolicyService $finePolicyService
    ) {
    }

    public function index(Request $request): View
    {
        $this->syncBorrowingStatuses();

        $search = $request->input('search');
        $status = $request->input('status');
        $dateFilter = $request->input('date_filter');
        $sort = $request->input('sort', 'latest');

        $query = Borrowing::with(['user', 'book', 'fine'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($groupedQuery) use ($search) {
                    $groupedQuery->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    })->orWhereHas('book', function ($bookQuery) use ($search) {
                        $bookQuery->where('title', 'like', "%{$search}%");
                    });
                });
            })
            ->when($status, function ($query, $status) {
                return match ($status) {
                    'dipinjam' => $query->whereNull('return_date')->where('status', 'dipinjam'),
                    'terlambat' => $query->whereNull('return_date')->where('status', 'terlambat'),
                    'dikembalikan' => $query->whereIn('status', ['dikembalikan', 'verifikasi_denda', 'proses_bayar', 'selesai']),
                    default => $query,
                };
            })
            ->when($dateFilter, function ($query, $dateFilter) {
                return match ($dateFilter) {
                    'today' => $query->whereDate('borrow_date', today()),
                    'this_week' => $query->whereBetween('borrow_date', [
                        today()->startOfWeek(),
                        today()->endOfWeek(),
                    ]),
                    'due_today' => $query->whereNull('return_date')->whereDate('due_date', today()),
                    default => $query,
                };
            });

        match ($sort) {
            'due_soonest' => $query->orderByRaw('CASE WHEN return_date IS NULL THEN 0 ELSE 1 END')
                ->orderBy('due_date')
                ->orderByDesc('created_at'),
            default => $query->latest(),
        };

        $borrowings = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Borrowing::count(),
            'borrowed' => Borrowing::activeLoan()->where('status', 'dipinjam')->count(),
            'late' => Borrowing::activeLoan()->where('status', 'terlambat')->count(),
            'completed' => Borrowing::whereNotNull('return_date')->count(),
        ];

        $alerts = [
            'overdue_count' => Borrowing::activeLoan()->where('status', 'terlambat')->count(),
            'due_today_count' => Borrowing::activeLoan()->whereDate('due_date', today())->count(),
            'overdue_items' => Borrowing::with(['user', 'book'])
                ->activeLoan()
                ->where('status', 'terlambat')
                ->orderBy('due_date')
                ->take(3)
                ->get(),
            'due_today_items' => Borrowing::with(['user', 'book'])
                ->activeLoan()
                ->whereDate('due_date', today())
                ->orderBy('due_date')
                ->take(3)
                ->get(),
        ];

        return view('admin.borrowings.index', compact('borrowings', 'stats', 'alerts'));
    }

    public function show(Borrowing $borrowing): View
    {
        $borrowing->syncTimelineStatus();
        $borrowing->load(['user', 'book', 'fine']);

        $summary = [
            'late_days' => $borrowing->lateDays(),
            'charged_late_days' => $borrowing->chargeableLateDays(),
            'grace_period_days' => $borrowing->gracePeriodDays(),
            'late_fee_per_day' => $borrowing->lateFeePerDay(),
            'max_fine_amount' => $borrowing->maxFineAmount(),
            'late_fine' => $borrowing->calculatedLateFine(),
            'damage_fine' => (float) ($borrowing->fine?->damage_amount ?? 0),
            'total_fine' => (float) ($borrowing->fine?->amount ?? $borrowing->fine_amount ?? 0),
            'payment_status' => $borrowing->fine_payment_status_label,
        ];

        return view('admin.borrowings.show', compact('borrowing', 'summary'));
    }

    public function approve(Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->status !== 'diajukan') {
            return back()->with('error', 'Aksi tidak valid.');
        }

        $borrowing->update([
            'status' => $borrowing->due_date->lt(today()) ? 'terlambat' : 'dipinjam',
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Persetujuan Pinjam',
            'description' => "Admin menyetujui peminjaman {$borrowing->quantity} buku '{$borrowing->book->title}' oleh '{$borrowing->user->name}'",
        ]);

        return back()->with('success', 'Peminjaman disetujui.');
    }

    public function reject(Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->status !== 'diajukan') {
            return back()->with('error', 'Aksi tidak valid.');
        }

        DB::transaction(function () use ($borrowing) {
            $borrowing->update(['status' => 'ditolak']);

            $book = $borrowing->book()->lockForUpdate()->first();
            $book->increment('stock', $borrowing->quantity);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Penolakan Pinjam',
                'description' => "Admin menolak peminjaman buku '{$borrowing->book->title}' oleh '{$borrowing->user->name}'",
            ]);
        });

        return back()->with('success', 'Peminjaman ditolak dan stok dikembalikan.');
    }

    public function markReturned(Request $request, Borrowing $borrowing): RedirectResponse
    {
        if (! in_array($borrowing->status, ['dipinjam', 'terlambat', 'dikembalikan'], true) || $borrowing->return_date !== null) {
            return back()->with('error', 'Transaksi ini tidak bisa ditandai sebagai dikembalikan.');
        }

        $data = $request->validate([
            'return_date' => ['nullable', 'date', 'after_or_equal:' . $borrowing->borrow_date->toDateString()],
            'admin_notes' => ['nullable', 'string', 'max:500'],
            'damage_fine' => ['nullable', 'numeric', 'min:0'],
            'book_condition' => ['required', 'in:baik,rusak_ringan,rusak_berat,hilang'],
        ]);

        DB::transaction(function () use ($borrowing, $data) {
            $returnDate = isset($data['return_date'])
                ? Carbon::parse($data['return_date'])->startOfDay()
                : now()->startOfDay();

            $rawLateDays = $returnDate->gt($borrowing->due_date)
                ? $borrowing->due_date->diffInDays($returnDate)
                : 0;
            $lateFeeBreakdown = $this->finePolicyService->calculateLateFee(
                $rawLateDays,
                $this->finePolicyService->borrowingPolicy($borrowing)
            );
            $damageFine = (float) ($data['damage_fine'] ?? 0);
            $lateFine = (float) $lateFeeBreakdown['late_fee_subtotal'];
            $totalFine = $lateFine + $damageFine;

            $borrowing->update([
                'return_date' => $returnDate->toDateString(),
                'status' => $totalFine > 0 ? 'verifikasi_denda' : 'selesai',
                'admin_notes' => $data['admin_notes'] ?? null,
                'book_condition' => $data['book_condition'],
                'fine_amount' => $totalFine,
            ]);

            $book = $borrowing->book()->lockForUpdate()->first();
            $book->increment('stock', $borrowing->quantity);

            if ($totalFine > 0) {
                $borrowing->fine()->updateOrCreate(
                    ['borrowing_id' => $borrowing->id],
                    [
                        'amount' => $totalFine,
                        'damage_amount' => $damageFine,
                        'days_late' => $rawLateDays,
                        'late_fee_per_day' => $lateFeeBreakdown['late_fee_per_day'],
                        'max_fine_amount' => $lateFeeBreakdown['max_fine_amount'],
                        'grace_period_days' => $lateFeeBreakdown['grace_period_days'],
                        'raw_late_days' => $lateFeeBreakdown['raw_late_days'],
                        'charged_late_days' => $lateFeeBreakdown['charged_late_days'],
                        'late_fee_subtotal' => $lateFeeBreakdown['late_fee_subtotal'],
                        'status' => 'belum_lunas',
                        'paid_at' => null,
                    ]
                );
            } else {
                $borrowing->fine()->delete();
            }

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Pengembalian Buku',
                'description' => "Admin menandai buku '{$borrowing->book->title}' milik '{$borrowing->user->name}' sebagai sudah dikembalikan",
            ]);
        });

        return back()->with('success', 'Transaksi berhasil ditandai sebagai dikembalikan.');
    }

    public function extend(Request $request, Borrowing $borrowing): RedirectResponse
    {
        if (! in_array($borrowing->status, ['dipinjam', 'terlambat'], true) || $borrowing->return_date !== null) {
            return back()->with('error', 'Hanya peminjaman aktif yang bisa diperpanjang.');
        }

        $data = $request->validate([
            'due_date' => ['required', 'date', 'after:' . $borrowing->due_date->toDateString()],
        ]);

        $newDueDate = Carbon::parse($data['due_date'])->startOfDay();

        $borrowing->update([
            'due_date' => $newDueDate->toDateString(),
            'status' => $newDueDate->lt(today()) ? 'terlambat' : 'dipinjam',
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Perpanjang Peminjaman',
            'description' => "Admin memperpanjang jatuh tempo buku '{$borrowing->book->title}' milik '{$borrowing->user->name}' sampai {$newDueDate->format('d M Y')}",
        ]);

        return back()->with('success', 'Jatuh tempo berhasil diperpanjang.');
    }

    public function markFinePaid(Borrowing $borrowing): RedirectResponse
    {
        $borrowing->loadMissing('fine');

        if (! $borrowing->fine || $borrowing->fine->status === 'lunas' || ! in_array($borrowing->status, ['verifikasi_denda', 'proses_bayar'], true)) {
            return back()->with('error', 'Tidak ada denda aktif yang perlu dilunasi.');
        }

        DB::transaction(function () use ($borrowing) {
            $borrowing->fine->update([
                'status' => 'lunas',
                'paid_at' => now(),
            ]);

            $borrowing->update([
                'status' => 'selesai',
            ]);
        });

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Pelunasan Denda',
            'description' => "Admin menandai denda buku '{$borrowing->book->title}' oleh '{$borrowing->user->name}' sebagai lunas",
        ]);

        return back()->with('success', 'Status pembayaran denda diperbarui menjadi lunas.');
    }

    public function verifyReturn(Request $request, Borrowing $borrowing): RedirectResponse
    {
        return $this->markReturned($request, $borrowing);
    }

    public function approvePayment(Borrowing $borrowing): RedirectResponse
    {
        return $this->markFinePaid($borrowing);
    }

    private function syncBorrowingStatuses(): void
    {
        Borrowing::query()
            ->whereNull('return_date')
            ->where('status', 'dipinjam')
            ->whereDate('due_date', '<', today())
            ->update(['status' => 'terlambat']);

        Borrowing::query()
            ->whereNull('return_date')
            ->where('status', 'terlambat')
            ->whereDate('due_date', '>=', today())
            ->update(['status' => 'dipinjam']);
    }
}
