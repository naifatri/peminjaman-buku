<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:20', 'unique:books,isbn'],
            'rack_location' => ['nullable', 'string', 'max:20'],
            'category_id' => ['required', 'exists:categories,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'published_year' => ['nullable', 'integer'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'page_count' => ['nullable', 'integer', 'min:1'],
            'rating' => ['nullable', 'numeric', 'between:0,5'],
            'description' => ['nullable', 'string'],
            'genre_tags' => ['nullable', 'string', 'max:255'],
            'cover_image' => ['nullable', 'image', 'max:2048'], // 2MB max
        ];
    }
}
