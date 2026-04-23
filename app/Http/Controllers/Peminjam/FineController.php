<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FineController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $status = (string) $request->input('status', 'all');
        $sort = (string) $request->input('sort', 'latest');

        $userFines = Fine::query()->whereHas('borrowing', function ($query) {
            $query->where('user_id', auth()->id());
        });

        $query = Fine::query()
            ->with('borrowing.book')
            ->whereHas('borrowing', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('borrowing.book', function ($bookQuery) use ($search) {
                    $bookQuery->where('title', 'like', "%{$search}%");
                });
            })
            ->when($status !== '' && $status !== 'all', function ($query) use ($status) {
                return match ($status) {
                    'lunas' => $query->where('status', 'lunas'),
                    'belum_lunas' => $query->where('status', 'belum_lunas'),
                    default => $query,
                };
            });

        match ($sort) {
            'amount_desc' => $query->orderByDesc('amount')->orderByDesc('created_at'),
            'amount_asc' => $query->orderBy('amount')->orderByDesc('created_at'),
            default => $query->latest(),
        };

        $fines = $query->paginate(10)->withQueryString();

        $unpaidCount = (clone $userFines)->where('status', 'belum_lunas')->count();
        $unpaidAmount = (clone $userFines)->where('status', 'belum_lunas')->sum('amount');
        $paidAmount = (clone $userFines)->where('status', 'lunas')->sum('amount');
        $totalFines = (clone $userFines)->count();
        $hasMultipleUnpaid = $unpaidCount > 1;

        return view('peminjam.fines.index', compact(
            'fines',
            'unpaidCount',
            'unpaidAmount',
            'paidAmount',
            'totalFines',
            'hasMultipleUnpaid',
            'search',
            'status',
            'sort'
        ));
    }
}
