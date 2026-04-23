<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\ActivityLog;
use App\Models\Fine;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowingController extends Controller
{
    public function index()
    {
        $userBorrowings = Borrowing::where('user_id', auth()->id());

        $borrowings = Borrowing::with('book')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        $activeCount = (clone $userBorrowings)->where('status', 'dipinjam')->count();
        $lateCount = (clone $userBorrowings)->where('status', 'terlambat')->count();
        $pendingCount = (clone $userBorrowings)->whereIn('status', ['diajukan', 'dikembalikan', 'verifikasi_denda', 'proses_bayar'])->count();

        return view('peminjam.borrowings.index', compact(
            'borrowings',
            'activeCount',
            'lateCount',
            'pendingCount'
        ));
    }

    public function show(Borrowing $borrowing)
    {
        if ($borrowing->user_id !== auth()->id()) {
            abort(403);
        }
        $borrowing->load(['book', 'fine']);
        return view('peminjam.borrowings.show', compact('borrowing'));
    }

    public function store(Request $request, Book $book)
    {
        $request->validate([
            'quantity' => "required|integer|min:1|max:{$book->stock}",
            'borrow_reason' => 'required|string|max:500',
            'borrow_date' => 'required|date|after_or_equal:today',
            'due_date' => 'required|date|after:borrow_date',
        ]);

        $user = auth()->user();

        $activeBorrowingsCount = Borrowing::where('user_id', $user->id)
            ->whereIn('status', ['diajukan', 'dipinjam', 'terlambat', 'dikembalikan', 'verifikasi_denda', 'proses_bayar'])
            ->count();

        if ($activeBorrowingsCount >= 3) {
            return back()->with('error', 'Anda sudah mencapai batas maksimal peminjaman (3 transaksi).');
        }

        Borrowing::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'quantity' => $request->quantity,
            'borrow_reason' => $request->borrow_reason,
            'borrow_date' => $request->borrow_date,
            'due_date' => $request->due_date,
            'status' => 'diajukan',
        ]);

        // Reserve stock
        $book->stock -= $request->quantity;
        $book->save();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Pengajuan Pinjam',
            'description' => "Pengguna mengajukan pinjam {$request->quantity} buku '{$book->title}' untuk alasan: {$request->borrow_reason}",
        ]);

        return redirect()->route('peminjam.borrowings.index')->with('success', 'Peminjaman diajukan. Menunggu persetujuan admin.');
    }

    public function returnBook(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->user_id !== auth()->id() || !in_array($borrowing->status, ['dipinjam', 'terlambat'])) {
            return back()->with('error', 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'return_notes' => 'required|string|max:500',
        ]);

        $borrowing->update([
            'status' => 'dikembalikan',
            'return_notes' => $request->return_notes,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Pengembalian Buku (User)',
            'description' => "Pengguna mengembalikan buku '{$borrowing->book->title}' dengan catatan: {$request->return_notes}",
        ]);

        return back()->with('success', 'Buku berhasil dikembalikan secara sistem. Silakan serahkan buku fisik ke petugas.');
    }

    public function payFine(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->user_id !== auth()->id() || $borrowing->status !== 'verifikasi_denda') {
            return back()->with('error', 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'payment_method' => 'required|in:tunai,ganti_buku,qris',
        ]);

        $fine = $borrowing->fine;
        $fine->update([
            'payment_method' => $request->payment_method,
        ]);

        $borrowing->update([
            'status' => 'proses_bayar',
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Konfirmasi Denda',
            'description' => "Pengguna memilih metode pembayaran {$request->payment_method} untuk denda buku '{$borrowing->book->title}'",
        ]);

        return back()->with('success', 'Metode pembayaran dikonfirmasi. Menunggu verifikasi admin.');
    }
}
