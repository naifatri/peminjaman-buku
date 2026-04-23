<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Fine;

class FineController extends Controller
{
    public function index()
    {
        $userFines = Fine::whereHas('borrowing', function ($query) {
            $query->where('user_id', auth()->id());
        });

        $fines = Fine::with('borrowing.book')
            ->whereHas('borrowing', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->latest()
            ->paginate(10);

        $unpaidCount = (clone $userFines)->where('status', 'belum_lunas')->count();
        $unpaidAmount = (clone $userFines)->where('status', 'belum_lunas')->sum('amount');
        $paidAmount = (clone $userFines)->where('status', 'lunas')->sum('amount');

        return view('peminjam.fines.index', compact(
            'fines',
            'unpaidCount',
            'unpaidAmount',
            'paidAmount'
        ));
    }
}
