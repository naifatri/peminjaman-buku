<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'category' => $this->category->name,
            'stock' => $this->stock,
            'published_year' => $this->published_year,
            'status' => $this->status,
            'cover_image' => $this->cover_image ? asset('storage/' . $this->cover_image) : null,
        ];
    }
}
