<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
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
            'author_id' => Author::factory(),
            'title' => fake()->sentence(3),
            'year' => fake()->numberBetween(1950, 2026),
            'price' => fake()->randomFloat(2, 5, 100),
            'stock' => fake()->numberBetween(5, 100),
        ];
    }
}
