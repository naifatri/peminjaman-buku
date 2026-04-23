<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'isbn',
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }
}
