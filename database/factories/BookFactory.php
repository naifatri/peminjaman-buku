<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'isbn' => $this->faker->unique()->isbn13(),
            'category_id' => Category::all()->random()->id,
            'stock' => $this->faker->numberBetween(1, 20),
            'published_year' => $this->faker->year(),
            'publisher' => $this->faker->company(),
            'page_count' => $this->faker->numberBetween(120, 640),
            'rating' => $this->faker->randomFloat(1, 3.8, 5.0),
            'description' => $this->faker->paragraph(),
            'genre_tags' => collect($this->faker->words(3))->map(fn ($word) => ucfirst($word))->implode(', '),
            'cover_image' => null,
            'status' => 'tersedia',
        ];
    }
}
