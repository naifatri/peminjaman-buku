<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Fine;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $borrowings = Borrowing::with(['user', 'book', 'fine'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('book', function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.borrowings.index', compact('borrowings'));
    }

    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['user', 'book', 'fine']);
        return view('admin.borrowings.show', compact('borrowing'));
    }

    public function approve(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'diajukan') {
            return back()->with('error', 'Aksi tidak valid.');
        }

        $borrowing->update([
            'status' => 'dipinjam',
            // If user input dates are already set, we keep them or update them to real start
            // Let's keep user's requested dates but ensure they start from now if they requested today
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Persetujuan Pinjam',
            'description' => "Admin menyetujui peminjaman {$borrowing->quantity} buku '{$borrowing->book->title}' oleh '{$borrowing->user->name}'",
        ]);

        return back()->with('success', 'Peminjaman disetujui.');
    }

    public function reject(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'diajukan') {
            return back()->with('error', 'Aksi tidak valid.');
        }

        $borrowing->update(['status' => 'ditolak']);

        // Restore stock
        $book = $borrowing->book;
        $book->stock += $borrowing->quantity;
        $book->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Penolakan Pinjam',
            'description' => "Admin menolak peminjaman buku '{$borrowing->book->title}' oleh '{$borrowing->user->name}'",
        ]);

        return back()->with('success', 'Peminjaman ditolak dan stok dikembalikan.');
    }

    public function verifyReturn(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->status !== 'dikembalikan') {
            return back()->with('error', 'Aksi tidak valid.');
        }

        $request->validate([
            'admin_notes' => 'required|string|max:500',
            'damage_fine' => 'required|numeric|min:0',
            'book_condition' => 'required|in:baik,rusak_ringan,rusak_berat,hilang',
        ]);

        $returnDate = Carbon::now();
        $dueDate = Carbon::parse($borrowing->due_date);
        
        $lateDays = $returnDate->gt($dueDate) ? $returnDate->diffInDays($dueDate) : 0;
        
        $fineSetting = \App\Models\FineSetting::first();
        $lateFeePerDay = $fineSetting ? $fineSetting->late_fee_per_day : 5000;
        
        $lateFine = $lateDays * $lateFeePerDay;
        $totalFine = $lateFine + $request->damage_fine;

        $borrowing->return_date = $returnDate->toDateString();
        $borrowing->admin_notes = $request->admin_notes;
        $borrowing->book_condition = $request->book_condition;
        $borrowing->fine_amount = $totalFine;

        if ($totalFine > 0) {
            $borrowing->status = 'verifikasi_denda';
            Fine::create([
                'borrowing_id' => $borrowing->id,
                'amount' => $totalFine,
                'damage_amount' => $request->damage_fine,
                'days_late' => $lateDays,
                'status' => 'belum_lunas',
            ]);
        } else {
            $borrowing->status = 'selesai';
            $book = $borrowing->book;
            $book->stock += $borrowing->quantity;
            $book->save();
        }

        $borrowing->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Verifikasi Kembali',
            'description' => "Admin memverifikasi pengembalian buku '{$borrowing->book->title}' oleh '{$borrowing->user->name}'",
        ]);

        return back()->with('success', 'Verifikasi pengembalian berhasil.');
    }

    public function approvePayment(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'proses_bayar') {
            return back()->with('error', 'Aksi tidak valid.');
        }

        $fine = $borrowing->fine;
        $fine->update([
            'status' => 'lunas',
            'paid_at' => Carbon::now(),
        ]);

        $borrowing->update(['status' => 'selesai']);

        // Restore stock
        $book = $borrowing->book;
        $book->stock += $borrowing->quantity;
        $book->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Persetujuan Pembayaran',
            'description' => "Admin menyetujui pembayaran denda buku '{$borrowing->book->title}' oleh '{$borrowing->user->name}'",
        ]);

        return back()->with('success', 'Pembayaran denda disetujui dan status peminjaman selesai.');
    }
}
