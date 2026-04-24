<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::query()->withCount('books');

        if ($request->filled('search')) {
            $search = $request->string('search')->trim()->toString();

            $query->where(function (Builder $categoryQuery) use ($search) {
                $categoryQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $this->applyBookFilter($query, $request->string('book_filter')->toString());
        $this->applySort($query, $request->string('sort', 'name_asc')->toString());

        $perPage = $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50], true) ? $perPage : 10;

        $categories = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total' => Category::count(),
            'without_books' => Category::doesntHave('books')->count(),
            'top_category' => Category::withCount('books')
                ->orderByDesc('books_count')
                ->orderBy('name')
                ->first(),
        ];

        return view('admin.categories.index', compact('categories', 'stats', 'perPage'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->safe()->only(['name', 'slug', 'description']));

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function show(Category $category): View
    {
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->safe()->only(['name', 'slug', 'description']));

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diupdate');
    }

    public function destroy(Category $category)
    {
        if ($category->books()->exists()) {
            return redirect()->route('admin.categories.index')->with('error', 'Kategori tidak bisa dihapus karena masih memiliki buku.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus');
    }

    private function applyBookFilter(Builder $query, string $bookFilter): void
    {
        match ($bookFilter) {
            'with_books' => $query->has('books'),
            'without_books' => $query->doesntHave('books'),
            default => null,
        };
    }

    private function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'name_desc' => $query->orderByDesc('name'),
            'books_desc' => $query->orderByDesc('books_count')->orderBy('name'),
            'books_asc' => $query->orderBy('books_count')->orderBy('name'),
            default => $query->orderBy('name'),
        };
    }
}
