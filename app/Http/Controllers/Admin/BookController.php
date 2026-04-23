<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $query = Book::query()
            ->with('category')
            ->withCount([
                'borrowings as active_borrowings_count' => function ($borrowingQuery) {
                    $borrowingQuery->whereIn('status', [
                        'diajukan',
                        'dipinjam',
                        'terlambat',
                        'dikembalikan',
                        'verifikasi_denda',
                        'proses_bayar',
                    ]);
                },
            ]);

        if ($request->filled('search')) {
            $search = $request->string('search')->trim();

            $query->where(function ($bookQuery) use ($search) {
                $bookQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('rack_location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        }

        $this->applyStatusFilter($query, $request->string('status')->toString());
        $this->applyStockFilter($query, $request->string('stock')->toString());
        $this->applySort($query, $request->string('sort', 'latest')->toString());

        $books = $query->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        $stats = [
            'total' => Book::count(),
            'available' => Book::where('stock', '>', 3)->count(),
            'borrowed' => Borrowing::whereIn('status', ['diajukan', 'dipinjam', 'terlambat', 'dikembalikan', 'verifikasi_denda', 'proses_bayar'])->count(),
            'out' => Book::where('stock', 0)->count(),
        ];

        $stockAlerts = [
            'out' => Book::where('stock', 0)->orderBy('title')->limit(5)->get(['id', 'title', 'stock']),
            'low' => Book::whereBetween('stock', [1, 3])->orderBy('stock')->orderBy('title')->limit(5)->get(['id', 'title', 'stock']),
        ];

        return view('admin.books.index', compact('books', 'categories', 'stats', 'stockAlerts'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.books.create', compact('categories'));
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        Book::create($data);

        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show(Book $book): View
    {
        $book->load([
            'category',
            'borrowings' => function ($query) {
                $query->with('user')->latest()->limit(10);
            },
        ]);

        $relatedStats = [
            'borrowed_count' => $book->borrowings()->count(),
            'active_count' => $book->borrowings()
                ->whereIn('status', ['diajukan', 'dipinjam', 'terlambat', 'dikembalikan', 'verifikasi_denda', 'proses_bayar'])
                ->count(),
        ];

        return view('admin.books.show', compact('book', 'relatedStats'));
    }

    public function edit(Book $book): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }

            $data['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $book->update($data);

        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil diperbarui.');
    }

    public function updateStock(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => ['required', 'in:increment,decrement,set'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $quantity = (int) $validated['quantity'];

        if ($validated['mode'] === 'increment') {
            $book->stock += $quantity;
        } elseif ($validated['mode'] === 'decrement') {
            $book->stock = max(0, $book->stock - $quantity);
        } else {
            $book->stock = $quantity;
        }

        $book->save();

        return back()->with('success', "Stok buku '{$book->title}' berhasil diperbarui.");
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'book_ids' => ['required', 'array', 'min:1'],
            'book_ids.*' => ['integer', 'exists:books,id'],
            'bulk_action' => ['required', 'in:delete,change_category,update_status'],
            'bulk_category_id' => ['nullable', 'exists:categories,id'],
            'bulk_status' => ['nullable', 'in:tersedia,habis'],
        ]);

        $books = Book::whereIn('id', $validated['book_ids'])->get();

        if ($books->isEmpty()) {
            return back()->with('error', 'Tidak ada buku yang dipilih.');
        }

        if ($validated['bulk_action'] === 'delete') {
            $blocked = $books->filter(function (Book $book) {
                return $book->borrowings()
                    ->whereIn('status', ['diajukan', 'dipinjam', 'terlambat', 'dikembalikan', 'verifikasi_denda', 'proses_bayar'])
                    ->exists();
            });

            if ($blocked->isNotEmpty()) {
                return back()->with('error', 'Sebagian buku tidak bisa dihapus karena masih terlibat transaksi aktif.');
            }

            foreach ($books as $book) {
                if ($book->cover_image) {
                    Storage::disk('public')->delete($book->cover_image);
                }

                $book->delete();
            }

            return back()->with('success', $books->count() . ' buku berhasil dihapus.');
        }

        if ($validated['bulk_action'] === 'change_category') {
            if (! $request->filled('bulk_category_id')) {
                return back()->with('error', 'Pilih kategori tujuan terlebih dahulu.');
            }

            Book::whereIn('id', $validated['book_ids'])->update([
                'category_id' => $validated['bulk_category_id'],
            ]);

            return back()->with('success', 'Kategori buku terpilih berhasil diperbarui.');
        }

        if (! $request->filled('bulk_status')) {
            return back()->with('error', 'Pilih status tujuan terlebih dahulu.');
        }

        DB::transaction(function () use ($books, $validated) {
            foreach ($books as $book) {
                if ($validated['bulk_status'] === 'habis') {
                    $book->stock = 0;
                } elseif ($book->stock <= 0) {
                    $book->stock = 1;
                }

                $book->save();
            }
        });

        return back()->with('success', 'Status buku terpilih berhasil diselaraskan.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $activeBorrowingExists = $book->borrowings()
            ->whereIn('status', ['diajukan', 'dipinjam', 'terlambat', 'dikembalikan', 'verifikasi_denda', 'proses_bayar'])
            ->exists();

        if ($activeBorrowingExists) {
            return redirect()->route('admin.books.index')->with('error', 'Buku tidak dapat dihapus karena masih terlibat dalam transaksi aktif.');
        }

        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil dihapus.');
    }

    private function applyStatusFilter($query, string $status): void
    {
        match ($status) {
            'available' => $query->where('stock', '>', 3),
            'low' => $query->whereBetween('stock', [1, 3]),
            'out' => $query->where('stock', 0),
            default => null,
        };
    }

    private function applyStockFilter($query, string $stock): void
    {
        match ($stock) {
            'zero' => $query->where('stock', 0),
            'lt3' => $query->where('stock', '<', 3),
            'gt3' => $query->where('stock', '>', 3),
            default => null,
        };
    }

    private function applySort($query, string $sort): void
    {
        match ($sort) {
            'title_asc' => $query->orderBy('title'),
            'title_desc' => $query->orderByDesc('title'),
            'stock_desc' => $query->orderByDesc('stock')->orderBy('title'),
            'stock_asc' => $query->orderBy('stock')->orderBy('title'),
            default => $query->latest(),
        };
    }
}
