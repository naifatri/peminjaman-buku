<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookFavorite;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Fine;
use App\Services\FinePolicyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class BookController extends Controller
{
    public function __construct(
        private readonly FinePolicyService $finePolicyService
    ) {
    }

    public function index(Request $request): View
    {
        $search = $request->input('search');
        $category_id = $request->input('category_id');
        $status = $request->input('status');
        $author = $request->input('author');
        $publishedYear = $request->input('published_year');
        $sort = $request->input('sort', 'latest');
        $user = auth()->user();
        $favoritesEnabled = Schema::hasTable('book_favorites');

        $booksQuery = Book::with('category')
            ->withCount([
                'borrowings',
                'borrowings as active_borrowings_count' => function ($query) {
                    $query->whereIn('status', ['diajukan', 'dipinjam', 'terlambat', 'dikembalikan', 'verifikasi_denda', 'proses_bayar']);
                },
            ]);

        if ($favoritesEnabled) {
            $booksQuery->withCount('favoredByUsers');
        }

        $booksQuery = $booksQuery
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
            ->when($author, function ($query, $author) {
                return $query->where('author', 'like', "%{$author}%");
            })
            ->when($publishedYear, function ($query, $publishedYear) {
                return $query->where('published_year', $publishedYear);
            })
            ->when($status === 'tersedia', function ($query) {
                return $query->where('stock', '>', 3);
            })
            ->when($status === 'stok_sedikit', function ($query) {
                return $query->whereBetween('stock', [1, 3]);
            })
            ->when($status === 'tidak_tersedia', function ($query) {
                return $query->where('stock', '<=', 0);
            });

        $books = match ($sort) {
            'popular' => $booksQuery->orderByDesc('borrowings_count')->orderByDesc('published_year'),
            'rating' => $booksQuery->orderByDesc('rating')->orderByDesc('borrowings_count'),
            'az' => $booksQuery->orderBy('title'),
            default => $booksQuery->latest(),
        };

        $books = $books->paginate(12)->withQueryString();

        $categories = Category::all();
        $totalBooks = Book::count();
        $availableBooks = Book::where('stock', '>', 0)->count();
        $activeBorrowings = Borrowing::where('user_id', $user->id)
            ->whereIn('status', ['diajukan', 'dipinjam', 'terlambat', 'dikembalikan', 'verifikasi_denda', 'proses_bayar'])
            ->count();
        $unpaidFines = Fine::whereHas('borrowing', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->where('status', 'belum_lunas')
            ->sum('amount');
        $favoriteBookIds = $favoritesEnabled
            ? $user->favoriteBooks()->pluck('books.id')->all()
            : [];
        $authors = Book::query()->select('author')->distinct()->orderBy('author')->pluck('author');
        $publishedYears = Book::query()->select('published_year')->distinct()->orderByDesc('published_year')->pluck('published_year');
        $finePolicy = $this->finePolicyService->currentPolicy();
        $borrowEligibility = $this->borrowEligibilityData($user->id);

        return view('peminjam.books.index', compact(
            'books',
            'categories',
            'authors',
            'publishedYears',
            'favoriteBookIds',
            'favoritesEnabled',
            'finePolicy',
            'borrowEligibility',
            'totalBooks',
            'availableBooks',
            'activeBorrowings',
            'unpaidFines',
            'status',
            'sort'
        ));
    }

    public function show(Book $book): View
    {
        $finePolicy = $this->finePolicyService->currentPolicy();
        $borrowEligibility = $this->borrowEligibilityData(auth()->id());
        $favoritesEnabled = Schema::hasTable('book_favorites');
        $isFavorite = $favoritesEnabled
            ? auth()->user()->favoriteBooks()->where('books.id', $book->id)->exists()
            : false;

        return view('peminjam.books.show', compact('book', 'finePolicy', 'borrowEligibility', 'isFavorite', 'favoritesEnabled'));
    }

    public function favorites(Request $request): View
    {
        $user = auth()->user();
        $favoritesEnabled = Schema::hasTable('book_favorites');

        if (! $favoritesEnabled) {
            return view('peminjam.books.favorites', [
                'favoriteBooks' => Book::query()->whereRaw('1 = 0')->paginate(12),
                'favoriteBookIds' => [],
                'finePolicy' => $this->finePolicyService->currentPolicy(),
                'borrowEligibility' => $this->borrowEligibilityData($user->id),
                'favoritesEnabled' => false,
            ]);
        }

        $favoriteBooks = $user->favoriteBooks()
            ->with('category')
            ->withCount(['borrowings', 'favoredByUsers'])
            ->latest('book_favorites.created_at')
            ->paginate(12)
            ->withQueryString();

        $favoriteBookIds = $user->favoriteBooks()->pluck('books.id')->all();
        $finePolicy = $this->finePolicyService->currentPolicy();
        $borrowEligibility = $this->borrowEligibilityData($user->id);

        return view('peminjam.books.favorites', compact(
            'favoriteBooks',
            'favoriteBookIds',
            'finePolicy',
            'borrowEligibility',
            'favoritesEnabled'
        ));
    }

    public function toggleFavorite(Book $book)
    {
        if (! Schema::hasTable('book_favorites')) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Fitur favorit belum aktif. Jalankan migrate terlebih dahulu.',
                ], 409);
            }

            return back()->with('error', 'Fitur favorit belum aktif. Jalankan migrate terlebih dahulu.');
        }

        $existingFavorite = BookFavorite::query()->where([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
        ])->first();

        if ($existingFavorite) {
            $existingFavorite->delete();
            $isFavorite = false;
        } else {
            BookFavorite::query()->create([
                'user_id' => auth()->id(),
                'book_id' => $book->id,
            ]);
            $isFavorite = true;
        }

        if (request()->expectsJson()) {
            return response()->json([
                'is_favorite' => $isFavorite,
            ]);
        }

        return back()->with('success', $isFavorite ? 'Buku ditambahkan ke favorit.' : 'Buku dihapus dari favorit.');
    }

    private function borrowEligibilityData(int $userId): array
    {
        $activeBorrowings = Borrowing::query()
            ->where('user_id', $userId)
            ->whereIn('status', ['diajukan', 'dipinjam', 'terlambat', 'dikembalikan', 'verifikasi_denda', 'proses_bayar'])
            ->count();

        $unpaidFines = Fine::query()
            ->whereHas('borrowing', fn ($query) => $query->where('user_id', $userId))
            ->where('status', 'belum_lunas')
            ->sum('amount');

        $canBorrow = $activeBorrowings === 0 && (float) $unpaidFines <= 0;

        return [
            'can_borrow' => $canBorrow,
            'active_borrowings' => $activeBorrowings,
            'unpaid_fines' => (float) $unpaidFines,
            'max_borrowings' => 1,
            'reasons' => collect([
                $activeBorrowings > 0 ? 'Anda masih memiliki transaksi peminjaman aktif.' : null,
                (float) $unpaidFines > 0 ? 'Anda masih memiliki denda yang belum lunas.' : null,
            ])->filter()->values()->all(),
        ];
    }
}
