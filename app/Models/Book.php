<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::saving(function (Book $book) {
            $book->status = $book->stock > 0 ? 'tersedia' : 'habis';
        });
    }

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'rack_location',
        'category_id',
        'stock',
        'published_year',
        'publisher',
        'page_count',
        'rating',
        'description',
        'genre_tags',
        'cover_image',
        'status',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
    ];

    protected $appends = [
        'inventory_status',
        'inventory_status_label',
        'inventory_status_color',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function favorites()
    {
        return $this->hasMany(BookFavorite::class);
    }

    public function favoredByUsers()
    {
        return $this->belongsToMany(User::class, 'book_favorites')
            ->withTimestamps();
    }

    public function getInventoryStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'out';
        }

        if ($this->stock <= 3) {
            return 'low';
        }

        return 'available';
    }

    public function getInventoryStatusLabelAttribute(): string
    {
        return match ($this->inventory_status) {
            'available' => 'Tersedia',
            'low' => 'Stok Menipis',
            default => 'Habis',
        };
    }

    public function getInventoryStatusColorAttribute(): string
    {
        return match ($this->inventory_status) {
            'available' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'low' => 'bg-amber-50 text-amber-700 border-amber-200',
            default => 'bg-rose-50 text-rose-700 border-rose-200',
        };
    }
}
