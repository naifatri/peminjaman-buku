<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
            'isbn' => ['nullable', 'string', 'max:20', Rule::unique('books')->ignore($this->route('book'))],
            'rack_location' => ['nullable', 'string', 'max:20'],
            'category_id' => ['required', 'exists:categories,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'published_year' => ['nullable', 'integer'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'page_count' => ['nullable', 'integer', 'min:1'],
            'rating' => ['nullable', 'numeric', 'between:0,5'],
            'description' => ['nullable', 'string'],
            'genre_tags' => ['nullable', 'string', 'max:255'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
