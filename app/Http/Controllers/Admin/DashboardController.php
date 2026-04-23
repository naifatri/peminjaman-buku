<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\User;
use App\Models\Fine;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks = Book::count();
        $totalCategories = Category::count();
        $totalUsers = User::where('role', 'peminjam')->count();
        $totalBorrowings = Borrowing::count();

        $activeBorrowings = Borrowing::where('status', 'dipinjam')->count();
        $unpaidFines = Fine::where('status', 'belum_lunas')->sum('amount');

        $recentBorrowings = Borrowing::with(['user', 'book'])->latest()->take(5)->get();

        // Data for Chart.js: borrowings count per month for the current year
        $borrowingsPerMonth = [];
        $months = [];
        $currentYear = Carbon::now()->year;

        for ($i = 1; $i <= 12; $i++) {
            $borrowingsPerMonth[] = Borrowing::whereYear('borrow_date', $currentYear)
                ->whereMonth('borrow_date', $i)
                ->count();
            $months[] = Carbon::create()->month($i)->format('M');
        }

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalCategories',
            'totalUsers',
            'totalBorrowings',
            'recentBorrowings',
            'activeBorrowings',
            'unpaidFines',
            'borrowingsPerMonth',
            'months',
            'currentYear'
        ));
    }
}
