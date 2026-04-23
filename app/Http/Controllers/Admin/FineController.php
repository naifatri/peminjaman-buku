<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Fine;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FineController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $amountRange = $request->input('amount_range');
        $dateFilter = $request->input('date_filter');
        $sort = $request->input('sort', 'latest');

        $query = Fine::with(['borrowing.user', 'borrowing.book'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($groupedQuery) use ($search) {
                    $groupedQuery->whereHas('borrowing.user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    })->orWhereHas('borrowing.book', function ($bookQuery) use ($search) {
                        $bookQuery->where('title', 'like', "%{$search}%");
                    });
                });
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($amountRange, function ($query, $amountRange) {
                return match ($amountRange) {
                    'lt_10000' => $query->where('amount', '<', 10000),
                    '10000_25000' => $query->whereBetween('amount', [10000, 25000]),
                    'gt_25000' => $query->where('amount', '>', 25000),
                    default => $query,
                };
            })
            ->when($dateFilter, function ($query, $dateFilter) {
                return match ($dateFilter) {
                    'today' => $query->whereDate('created_at', today()),
                    'this_week' => $query->whereBetween('created_at', [
                        today()->startOfWeek(),
                        today()->endOfWeek(),
                    ]),
                    'old_unpaid' => $query->where('status', 'belum_lunas')
                        ->whereDate('created_at', '<=', today()->subDays(7)),
                    default => $query,
                };
            });

        match ($sort) {
            'highest' => $query->orderByDesc('amount')->orderByDesc('created_at'),
            default => $query->latest(),
        };

        $fines = $query->paginate(10)->withQueryString();

        $stats = [
            'unpaid_total' => Fine::where('status', 'belum_lunas')->sum('amount'),
            'paid_total' => Fine::where('status', 'lunas')->sum('amount'),
            'users_with_fines' => Fine::query()
                ->whereHas('borrowing')
                ->join('borrowings', 'fines.borrowing_id', '=', 'borrowings.id')
                ->distinct('borrowings.user_id')
                ->count('borrowings.user_id'),
        ];

        $alerts = [
            'unpaid_count' => Fine::where('status', 'belum_lunas')->count(),
            'old_unpaid_count' => Fine::where('status', 'belum_lunas')
                ->whereDate('created_at', '<=', today()->subDays(7))
                ->count(),
            'unpaid_items' => Fine::with(['borrowing.user', 'borrowing.book'])
                ->where('status', 'belum_lunas')
                ->latest()
                ->take(3)
                ->get(),
            'old_unpaid_items' => Fine::with(['borrowing.user', 'borrowing.book'])
                ->where('status', 'belum_lunas')
                ->whereDate('created_at', '<=', today()->subDays(7))
                ->oldest('created_at')
                ->take(3)
                ->get(),
        ];

        $activePolicy = app(\App\Services\FinePolicyService::class)->currentPolicy();

        return view('admin.fines.index', compact('fines', 'stats', 'alerts', 'activePolicy'));
    }

    public function show(Fine $fine): View
    {
        $fine->load(['borrowing.user', 'borrowing.book']);

        $summary = [
            'late_fee_per_day' => $fine->late_fee_per_day,
            'late_fee_total' => $fine->late_fee_total,
            'raw_late_days' => $fine->raw_late_days ?: $fine->days_late,
            'charged_late_days' => $fine->charged_late_days,
            'grace_period_days' => $fine->grace_period_days,
            'max_fine_amount' => $fine->max_fine_amount,
            'damage_amount' => (float) $fine->damage_amount,
            'payment_status' => $fine->status_label,
            'payment_date' => $fine->paid_at,
        ];

        return view('admin.fines.show', compact('fine', 'summary'));
    }

    public function pay(Fine $fine): RedirectResponse
    {
        if ($fine->status === 'lunas') {
            return back()->with('error', 'Denda ini sudah ditandai lunas sebelumnya.');
        }

        $fine->update([
            'status' => 'lunas',
            'paid_at' => Carbon::now(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Pembayaran Denda',
            'description' => "Admin mencatat pembayaran denda oleh '{$fine->borrowing->user->name}' sebesar Rp " . number_format($fine->amount, 0, ',', '.'),
        ]);

        return back()->with('success', 'Pembayaran denda berhasil dicatat.');
    }
}
