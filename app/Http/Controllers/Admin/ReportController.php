<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->resolveFilters($request);
        $baseQuery = $this->filteredQuery($filters);

        $borrowings = (clone $baseQuery)
            ->with(['user', 'book', 'fine'])
            ->orderBy(...$this->resolveSort($filters['sort']))
            ->paginate(10)
            ->withQueryString();

        $summary = $this->buildSummary(clone $baseQuery);
        $statusBreakdown = $this->buildStatusBreakdown(clone $baseQuery);
        $charts = $this->buildCharts(clone $baseQuery, $filters);

        return view('admin.reports.index', compact(
            'borrowings',
            'filters',
            'summary',
            'statusBreakdown',
            'charts'
        ));
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->resolveFilters($request);
        $borrowings = $this->filteredQuery($filters)
            ->with(['user', 'book', 'fine'])
            ->orderBy(...$this->resolveSort($filters['sort']))
            ->get();

        $summary = $this->buildSummary($this->filteredQuery($filters));
        $statusBreakdown = $this->buildStatusBreakdown($this->filteredQuery($filters));

        $pdf = Pdf::loadView('admin.reports.pdf', compact('borrowings', 'filters', 'summary', 'statusBreakdown'));

        return $pdf->download('laporan-peminjaman.pdf');
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $filters = $this->resolveFilters($request);
        $borrowings = $this->filteredQuery($filters)
            ->with(['user', 'book', 'fine'])
            ->orderBy(...$this->resolveSort($filters['sort']))
            ->get();

        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=laporan-peminjaman.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($borrowings) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, ['ID', 'Peminjam', 'Buku', 'Tanggal Pinjam', 'Tanggal Kembali', 'Status', 'Hari Terlambat', 'Denda']);

            foreach ($borrowings as $borrowing) {
                fputcsv($file, [
                    $borrowing->id,
                    $borrowing->user->name,
                    $borrowing->book->title,
                    optional($borrowing->borrow_date)->format('Y-m-d'),
                    optional($borrowing->return_date)->format('Y-m-d') ?? '-',
                    $borrowing->admin_status_label,
                    $borrowing->late_days,
                    (float) $borrowing->fine_amount,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function filteredQuery(array $filters): Builder
    {
        return Borrowing::query()
            ->withCasts([
                'borrow_date' => 'date',
                'return_date' => 'date',
                'due_date' => 'date',
            ])
            ->when($filters['quick_filter'], function (Builder $query, string $quickFilter) use ($filters) {
                return match ($quickFilter) {
                    'today' => $query->whereDate('borrow_date', Carbon::today()),
                    'this_week' => $query->whereBetween('borrow_date', [
                        Carbon::today()->startOfWeek(),
                        Carbon::today()->endOfWeek(),
                    ]),
                    'this_month' => $query->whereBetween('borrow_date', [
                        Carbon::today()->startOfMonth(),
                        Carbon::today()->endOfMonth(),
                    ]),
                    default => $query,
                };
            })
            ->when($filters['start_date'], fn (Builder $query, string $date) => $query->whereDate('borrow_date', '>=', $date))
            ->when($filters['end_date'], fn (Builder $query, string $date) => $query->whereDate('borrow_date', '<=', $date))
            ->when($filters['user_name'], function (Builder $query, string $value) {
                $query->whereHas('user', fn (Builder $userQuery) => $userQuery->where('name', 'like', "%{$value}%"));
            })
            ->when($filters['book_title'], function (Builder $query, string $value) {
                $query->whereHas('book', fn (Builder $bookQuery) => $bookQuery->where('title', 'like', "%{$value}%"));
            })
            ->when($filters['status'], function (Builder $query, string $status) {
                return match ($status) {
                    'selesai' => $query->where(function (Builder $builder) {
                        $builder->whereNotNull('return_date')
                            ->orWhereIn('status', ['dikembalikan', 'selesai']);
                    }),
                    'terlambat' => $query->where(function (Builder $builder) {
                        $builder->where('status', 'terlambat')
                            ->orWhere(function (Builder $overdue) {
                                $overdue->whereNull('return_date')
                                    ->whereDate('due_date', '<', Carbon::today());
                            });
                    }),
                    'dipinjam' => $query->whereNull('return_date')->where('status', 'dipinjam'),
                    default => $query->where('status', $status),
                };
            });
    }

    private function resolveFilters(Request $request): array
    {
        return [
            'user_name' => trim((string) $request->input('user_name')),
            'book_title' => trim((string) $request->input('book_title')),
            'status' => (string) $request->input('status', ''),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'quick_filter' => (string) $request->input('quick_filter', ''),
            'sort' => (string) $request->input('sort', 'borrow_date_desc'),
        ];
    }

    private function resolveSort(string $sort): array
    {
        return match ($sort) {
            'borrow_date_asc' => ['borrow_date', 'asc'],
            'return_date_desc' => ['return_date', 'desc'],
            'return_date_asc' => ['return_date', 'asc'],
            'fine_desc' => ['fine_amount', 'desc'],
            'fine_asc' => ['fine_amount', 'asc'],
            default => ['borrow_date', 'desc'],
        };
    }

    private function buildSummary(Builder $query): array
    {
        $borrowings = $query->get(['id', 'status', 'return_date', 'due_date', 'fine_amount']);

        $returnedCount = $borrowings->filter(function (Borrowing $borrowing) {
            return $borrowing->return_date !== null || in_array($borrowing->status, ['dikembalikan', 'selesai'], true);
        })->count();

        $lateCount = $borrowings->filter(function (Borrowing $borrowing) {
            return $borrowing->status === 'terlambat'
                || ($borrowing->return_date === null && $borrowing->due_date?->lt(Carbon::today()));
        })->count();

        return [
            'total_borrowings' => $borrowings->count(),
            'returned_borrowings' => $returnedCount,
            'late_borrowings' => $lateCount,
            'total_fines' => (float) $borrowings->sum('fine_amount'),
        ];
    }

    private function buildStatusBreakdown(Builder $query): array
    {
        $borrowings = $query->get(['id', 'status', 'return_date', 'due_date']);

        $counts = [
            'diajukan' => 0,
            'dipinjam' => 0,
            'terlambat' => 0,
            'selesai' => 0,
            'ditolak' => 0,
        ];

        foreach ($borrowings as $borrowing) {
            if ($borrowing->status === 'ditolak') {
                $counts['ditolak']++;
                continue;
            }

            if ($borrowing->return_date !== null || in_array($borrowing->status, ['dikembalikan', 'selesai'], true)) {
                $counts['selesai']++;
                continue;
            }

            if ($borrowing->status === 'diajukan') {
                $counts['diajukan']++;
                continue;
            }

            if ($borrowing->status === 'terlambat' || $borrowing->due_date?->lt(Carbon::today())) {
                $counts['terlambat']++;
                continue;
            }

            $counts['dipinjam']++;
        }

        return $counts;
    }

    private function buildCharts(Builder $query, array $filters): array
    {
        $groupFormat = $this->shouldUseMonthlyGrouping($filters) ? 'Y-m' : 'Y-m-d';
        $labelFormat = $groupFormat === 'Y-m' ? 'M Y' : 'd M';

        $chartRows = $query->get(['id', 'borrow_date', 'status', 'return_date', 'due_date']);

        $trend = $chartRows
            ->groupBy(fn (Borrowing $borrowing) => $borrowing->borrow_date?->format($groupFormat))
            ->sortKeys()
            ->map(function ($items, $key) use ($groupFormat, $labelFormat) {
                $date = Carbon::createFromFormat($groupFormat, $key);

                return [
                    'label' => $date->translatedFormat($labelFormat),
                    'value' => $items->count(),
                ];
            })
            ->values();

        $statusBreakdown = $this->buildStatusBreakdown($this->filteredQuery($filters));

        return [
            'trend' => [
                'labels' => $trend->pluck('label')->all(),
                'values' => $trend->pluck('value')->all(),
            ],
            'status' => [
                'labels' => ['Selesai', 'Terlambat', 'Aktif/Pending'],
                'values' => [
                    $statusBreakdown['selesai'],
                    $statusBreakdown['terlambat'],
                    $statusBreakdown['dipinjam'] + $statusBreakdown['diajukan'],
                ],
            ],
        ];
    }

    private function shouldUseMonthlyGrouping(array $filters): bool
    {
        if (! $filters['start_date'] || ! $filters['end_date']) {
            return false;
        }

        return Carbon::parse($filters['start_date'])->diffInDays(Carbon::parse($filters['end_date'])) > 45;
    }
}
