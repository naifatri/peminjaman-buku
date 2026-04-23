<?php

namespace App\Providers;

use App\Models\Borrowing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            $notifications = collect();

            if (Auth::check() && Auth::user()->isAdmin()) {
                $notifications = Borrowing::with(['user', 'book', 'fine'])
                    ->whereIn('status', ['diajukan', 'dikembalikan', 'proses_bayar'])
                    ->latest('updated_at')
                    ->take(6)
                    ->get()
                    ->map(function (Borrowing $borrowing) {
                        $message = match ($borrowing->status) {
                            'diajukan' => "{$borrowing->user->name} mengajukan peminjaman buku {$borrowing->book->title}.",
                            'dikembalikan' => "{$borrowing->user->name} mengembalikan buku {$borrowing->book->title} dan menunggu verifikasi.",
                            'proses_bayar' => "{$borrowing->user->name} mengonfirmasi pembayaran denda untuk buku {$borrowing->book->title}.",
                            default => "Ada pembaruan pada transaksi {$borrowing->book->title}.",
                        };

                        return (object) [
                            'message' => $message,
                            'time' => $borrowing->updated_at,
                            'url' => route('admin.borrowings.show', $borrowing),
                            'accent' => match ($borrowing->status) {
                                'diajukan' => 'indigo',
                                'dikembalikan' => 'amber',
                                'proses_bayar' => 'rose',
                                default => 'slate',
                            },
                        ];
                    });
            }

            $view->with('headerNotifications', $notifications)
                ->with('headerNotificationCount', $notifications->count());
        });

        View::composer('components.peminjam-layout', function ($view) {
            $notifications = collect();

            if (Auth::check() && Auth::user()->isPeminjam()) {
                $notifications = Borrowing::with(['book', 'fine'])
                    ->where('user_id', Auth::id())
                    ->whereIn('status', ['diajukan', 'dipinjam', 'ditolak', 'dikembalikan', 'verifikasi_denda', 'proses_bayar', 'selesai', 'terlambat'])
                    ->latest('updated_at')
                    ->take(6)
                    ->get()
                    ->map(function (Borrowing $borrowing) {
                        $message = match ($borrowing->status) {
                            'diajukan' => "Pengajuan peminjaman {$borrowing->book->title} sedang menunggu persetujuan admin.",
                            'dipinjam' => "Peminjaman {$borrowing->book->title} sudah disetujui. Silakan ambil bukunya.",
                            'ditolak' => "Pengajuan peminjaman {$borrowing->book->title} ditolak oleh admin.",
                            'dikembalikan' => "Pengembalian {$borrowing->book->title} sedang diperiksa petugas.",
                            'verifikasi_denda' => "Ada denda untuk {$borrowing->book->title}. Silakan pilih metode pembayaran.",
                            'proses_bayar' => "Pembayaran denda untuk {$borrowing->book->title} sedang diverifikasi admin.",
                            'selesai' => "Transaksi {$borrowing->book->title} telah selesai.",
                            'terlambat' => "Peminjaman {$borrowing->book->title} sudah melewati batas pengembalian.",
                            default => "Ada pembaruan pada transaksi {$borrowing->book->title}.",
                        };

                        return (object) [
                            'message' => $message,
                            'time' => $borrowing->updated_at,
                            'url' => route('peminjam.borrowings.show', $borrowing),
                            'accent' => match ($borrowing->status) {
                                'dipinjam', 'selesai' => 'emerald',
                                'ditolak', 'verifikasi_denda', 'terlambat' => 'rose',
                                'dikembalikan', 'proses_bayar' => 'amber',
                                default => 'indigo',
                            },
                        ];
                    });
            }

            $view->with('headerNotifications', $notifications)
                ->with('headerNotificationCount', $notifications->count());
        });
    }
}
