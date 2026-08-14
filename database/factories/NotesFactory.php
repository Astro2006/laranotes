<?php

namespace Database\Factories;

use App\Models\Notes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notes>
 */
class NotesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'content' => fake()->paragraphs(3, true),
        ];
    }
}
