<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Fine;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category_id = $request->input('category_id');
        $status = $request->input('status');
        $sort = $request->input('sort', 'latest');

        $booksQuery = Book::with('category')
            ->withCount([
                'borrowings',
                'borrowings as active_borrowings_count' => function ($query) {
                    $query->whereIn('status', ['diajukan', 'dipinjam', 'terlambat', 'dikembalikan', 'verifikasi_denda', 'proses_bayar']);
                },
            ])
            ->when($search, function ($query, $search) {
                return $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('publisher', 'like', "%{$search}%");
                });
            })
            ->when($category_id, function ($query, $category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when($status === 'tersedia', function ($query) {
                return $query->where('stock', '>', 0);
            })
            ->when($status === 'dipinjam', function ($query) {
                return $query->where(function ($subQuery) {
                    $subQuery->where('stock', '<=', 0)
                        ->orWhereHas('borrowings', function ($borrowingQuery) {
                            $borrowingQuery->whereIn('status', ['diajukan', 'dipinjam', 'terlambat', 'dikembalikan', 'verifikasi_denda', 'proses_bayar']);
                        });
                });
            });

        $books = match ($sort) {
            'popular' => $booksQuery->orderByDesc('borrowings_count')->orderByDesc('published_year'),
            'az' => $booksQuery->orderBy('title'),
            default => $booksQuery->latest(),
        };

        $books = $books->paginate(12)->withQueryString();

        $categories = Category::all();
        $totalBooks = Book::count();
        $availableBooks = Book::where('stock', '>', 0)->count();
        $activeBorrowings = Borrowing::where('user_id', auth()->id())
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->count();
        $unpaidFines = Fine::whereHas('borrowing', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->where('status', 'belum_lunas')
            ->sum('amount');

        return view('peminjam.books.index', compact(
            'books',
            'categories',
            'totalBooks',
            'availableBooks',
            'activeBorrowings',
            'unpaidFines',
            'status',
            'sort'
        ));
    }

    public function show(Book $book)
    {
        return view('peminjam.books.show', compact('book'));
    }
}
