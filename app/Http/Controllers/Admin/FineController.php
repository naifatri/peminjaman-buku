<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FineController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $fines = Fine::with(['borrowing.user', 'borrowing.book'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('borrowing.user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('borrowing.book', function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        return view('admin.fines.index', compact('fines'));
    }

    public function pay(Fine $fine)
    {
        if ($fine->status === 'lunas') {
            return back()->with('error', 'Denda ini sudah lunas.');
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
