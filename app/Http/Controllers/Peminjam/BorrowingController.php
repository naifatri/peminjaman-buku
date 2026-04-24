<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\ActivityLog;
use App\Models\Fine;
use App\Services\FinePolicyService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BorrowingController extends Controller
{
    public function __construct(
        private readonly FinePolicyService $finePolicyService
    ) {
    }

    public function index(Request $request)
    {
        Borrowing::query()
            ->where('user_id', auth()->id())
            ->whereNull('return_date')
            ->where('status', 'dipinjam')
            ->whereDate('due_date', '<', today())
            ->update(['status' => 'terlambat']);

        $search = trim((string) $request->input('search'));
        $status = (string) $request->input('status');

        $userBorrowings = Borrowing::query()
            ->with(['book', 'fine'])
            ->where('user_id', auth()->id());

        $borrowings = (clone $userBorrowings)
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('book', function ($bookQuery) use ($search) {
                    $bookQuery->where('title', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                return match ($status) {
                    'dipinjam' => $query->where('status', 'dipinjam'),
                    'terlambat' => $query->where('status', 'terlambat'),
                    'selesai' => $query->where('status', 'selesai'),
                    'proses' => $query->whereIn('status', ['diajukan', 'dikembalikan', 'verifikasi_denda', 'proses_bayar']),
                    'denda' => $query->whereHas('fine', fn ($fineQuery) => $fineQuery->where('status', 'belum_lunas')),
                    default => $query,
                };
            })
            ->latest()
            ->paginate(10);

        $activeCount = (clone $userBorrowings)->where('status', 'dipinjam')->count();
        $lateCount = (clone $userBorrowings)->where('status', 'terlambat')->count();
        $pendingCount = (clone $userBorrowings)->whereIn('status', ['diajukan', 'dikembalikan', 'verifikasi_denda', 'proses_bayar'])->count();
        $completedCount = (clone $userBorrowings)->where('status', 'selesai')->count();

        $alerts = [
            'due_soon' => Borrowing::query()
                ->with('book')
                ->where('user_id', auth()->id())
                ->where('status', 'dipinjam')
                ->whereNull('return_date')
                ->whereBetween('due_date', [today(), today()->copy()->addDays(2)])
                ->orderBy('due_date')
                ->get(),
            'unpaid_fines' => Borrowing::query()
                ->with(['book', 'fine'])
                ->where('user_id', auth()->id())
                ->whereHas('fine', fn ($fineQuery) => $fineQuery->where('status', 'belum_lunas'))
                ->latest()
                ->get(),
        ];

        return view('peminjam.borrowings.index', compact(
            'borrowings',
            'activeCount',
            'lateCount',
            'pendingCount',
            'completedCount',
            'alerts',
            'search',
            'status'
        ));
    }

    public function show(Borrowing $borrowing)
    {
        if ($borrowing->user_id !== auth()->id()) {
            abort(403);
        }
        $borrowing->load(['book', 'fine']);
        $policy = $this->finePolicyService->borrowingPolicy($borrowing);

        return view('peminjam.borrowings.show', compact('borrowing', 'policy'));
    }

    public function store(Request $request, Book $book)
    {
        if ($book->stock < 1) {
            return back()->with('error', 'Stok buku habis. Peminjaman tidak dapat diproses.');
        }

        $request->validate([
            'quantity' => "required|integer|min:1|max:{$book->stock}",
            'borrow_reason' => 'required|string|max:500',
            'borrow_date' => 'required|date|after_or_equal:today',
            'due_date' => 'required|date|after_or_equal:borrow_date',
        ]);

        $user = auth()->user();

        $activeBorrowingsCount = Borrowing::where('user_id', $user->id)
            ->whereIn('status', ['diajukan', 'dipinjam', 'terlambat', 'dikembalikan', 'verifikasi_denda', 'proses_bayar'])
            ->count();

        if ($activeBorrowingsCount > 0) {
            return back()->with('error', 'Anda masih memiliki pinjaman atau transaksi aktif. Selesaikan terlebih dahulu sebelum meminjam buku lain.');
        }

        $unpaidFineAmount = Fine::query()
            ->whereHas('borrowing', fn ($query) => $query->where('user_id', $user->id))
            ->where('status', 'belum_lunas')
            ->sum('amount');

        if ((float) $unpaidFineAmount > 0) {
            return back()->with('error', 'Anda memiliki denda belum lunas. Lunasi terlebih dahulu sebelum meminjam buku baru.');
        }

        DB::transaction(function () use ($request, $user, $book) {
            $freshBook = Book::query()->lockForUpdate()->findOrFail($book->id);
            $policy = $this->finePolicyService->currentPolicy();
            $borrowDate = Carbon::parse($request->borrow_date)->startOfDay();
            $dueDate = Carbon::parse($request->due_date)->startOfDay();
            $loanDurationDays = max(1, $borrowDate->diffInDays($dueDate));

            if ($freshBook->stock < $request->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok buku tidak mencukupi untuk jumlah yang dipilih.',
                ]);
            }

            Borrowing::create([
                'user_id' => $user->id,
                'book_id' => $freshBook->id,
                'quantity' => $request->quantity,
                'borrow_reason' => $request->borrow_reason,
                'borrow_date' => $borrowDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'late_fee_per_day' => $policy['late_fee_per_day'],
                'max_fine_amount' => $policy['max_fine_amount'],
                'grace_period_days' => $policy['grace_period_days'],
                'loan_duration_days' => $loanDurationDays,
                'status' => 'diajukan',
            ]);

            $freshBook->decrement('stock', $request->quantity);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'Pengajuan Pinjam',
                'description' => "Pengguna mengajukan pinjam {$request->quantity} buku '{$freshBook->title}' untuk alasan: {$request->borrow_reason}",
            ]);
        });

        return redirect()->route('peminjam.borrowings.index')->with('success', 'Peminjaman diajukan. Menunggu persetujuan admin.');
    }

    public function returnBook(Request $request, Borrowing $borrowing)
    {
        $borrowing->syncTimelineStatus();

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
            'payment_proof' => ['nullable', 'image', 'max:2048', 'required_if:payment_method,qris'],
        ]);

        $fine = $borrowing->fine;
        $paymentProofPath = $fine->payment_proof;

        if ($request->payment_method === 'qris' && $request->hasFile('payment_proof')) {
            if ($paymentProofPath) {
                Storage::disk('public')->delete($paymentProofPath);
            }

            $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        }

        if ($request->payment_method !== 'qris' && $paymentProofPath) {
            Storage::disk('public')->delete($paymentProofPath);
            $paymentProofPath = null;
        }

        $fine->update([
            'payment_method' => $request->payment_method,
            'payment_proof' => $paymentProofPath,
        ]);

        $borrowing->update([
            'status' => 'proses_bayar',
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Konfirmasi Denda',
            'description' => "Pengguna memilih metode pembayaran {$request->payment_method} untuk denda buku '{$borrowing->book->title}'" . ($request->payment_method === 'qris' ? ' dan mengunggah bukti pembayaran.' : '.'),
        ]);

        return back()->with('success', 'Metode pembayaran dikonfirmasi. Menunggu verifikasi admin.');
    }
}
