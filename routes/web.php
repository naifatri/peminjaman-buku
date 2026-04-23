<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\CategoryController as AdminCategory;
use App\Http\Controllers\Admin\BookController as AdminBook;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\BorrowingController as AdminBorrowing;
use App\Http\Controllers\Admin\FineController as AdminFine;
use App\Http\Controllers\Admin\ReportController as AdminReport;
use App\Http\Controllers\Peminjam\BookController as PeminjamBook;
use App\Http\Controllers\Peminjam\BorrowingController as PeminjamBorrowing;
use App\Http\Controllers\Peminjam\FineController as PeminjamFine;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect('/admin/dashboard');
        }
        return redirect('/peminjam/books');
    }
    return redirect('/login');
});

Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('peminjam.books.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        Route::resource('/categories', AdminCategory::class);
        Route::patch('/books/{book}/stock', [AdminBook::class, 'updateStock'])->name('books.update-stock');
        Route::post('/books/bulk-action', [AdminBook::class, 'bulkAction'])->name('books.bulk-action');
        Route::resource('/books', AdminBook::class);
        Route::patch('/users/{user}/status', [AdminUser::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/reset-password', [AdminUser::class, 'resetPassword'])->name('users.reset-password');
        Route::post('/users/bulk-action', [AdminUser::class, 'bulkAction'])->name('users.bulk-action');
        Route::resource('/users', AdminUser::class);
        
        // Fine Settings
        Route::get('/settings/fine', [\App\Http\Controllers\Admin\FineSettingController::class, 'index'])->name('settings.fine.index');
        Route::patch('/settings/fine', [\App\Http\Controllers\Admin\FineSettingController::class, 'update'])->name('settings.fine.update');
        
        // Complex Borrowing Flow (Admin)
        Route::get('/borrowings', [AdminBorrowing::class, 'index'])->name('borrowings.index');
        Route::get('/borrowings/{borrowing}', [AdminBorrowing::class, 'show'])->name('borrowings.show');
        Route::post('/borrowings/{borrowing}/approve', [AdminBorrowing::class, 'approve'])->name('borrowings.approve');
        Route::post('/borrowings/{borrowing}/reject', [AdminBorrowing::class, 'reject'])->name('borrowings.reject');
        Route::patch('/borrowings/{borrowing}/extend', [AdminBorrowing::class, 'extend'])->name('borrowings.extend');
        Route::post('/borrowings/{borrowing}/mark-returned', [AdminBorrowing::class, 'markReturned'])->name('borrowings.mark-returned');
        Route::post('/borrowings/{borrowing}/mark-fine-paid', [AdminBorrowing::class, 'markFinePaid'])->name('borrowings.mark-fine-paid');
        Route::post('/borrowings/{borrowing}/verify-return', [AdminBorrowing::class, 'verifyReturn'])->name('borrowings.verify-return');
        Route::post('/borrowings/{borrowing}/approve-payment', [AdminBorrowing::class, 'approvePayment'])->name('borrowings.approve-payment');
        
        Route::get('/fines', [AdminFine::class, 'index'])->name('fines.index');
        Route::get('/fines/{fine}', [AdminFine::class, 'show'])->name('fines.show');
        Route::post('/fines/{fine}/pay', [AdminFine::class, 'pay'])->name('fines.pay');
        Route::get('/reports', [AdminReport::class, 'index'])->name('reports.index');
        Route::get('/reports/export-pdf', [AdminReport::class, 'exportPdf'])->name('reports.export-pdf');
        Route::get('/reports/export-excel', [AdminReport::class, 'exportExcel'])->name('reports.export-excel');
    });

    // Peminjam Routes
    Route::middleware('role:peminjam')->prefix('peminjam')->name('peminjam.')->group(function () {
        Route::get('/books', [PeminjamBook::class, 'index'])->name('books.index');
        Route::get('/books/favorites', [PeminjamBook::class, 'favorites'])->name('books.favorites');
        Route::post('/books/{book}/favorite', [PeminjamBook::class, 'toggleFavorite'])->name('books.favorite');
        Route::get('/books/{book}', [PeminjamBook::class, 'show'])->name('books.show');
        
        // Complex Borrowing Flow (Peminjam)
        Route::post('/books/{book}/borrow', [PeminjamBorrowing::class, 'store'])->name('borrowings.store');
        Route::get('/borrowings', [PeminjamBorrowing::class, 'index'])->name('borrowings.index');
        Route::get('/borrowings/{borrowing}', [PeminjamBorrowing::class, 'show'])->name('borrowings.show');
        Route::post('/borrowings/{borrowing}/return', [PeminjamBorrowing::class, 'returnBook'])->name('borrowings.return');
        Route::post('/borrowings/{borrowing}/pay-fine', [PeminjamBorrowing::class, 'payFine'])->name('borrowings.pay-fine');
        
        Route::get('/fines', [PeminjamFine::class, 'index'])->name('fines.index');
    });
});

require __DIR__.'/auth.php';
