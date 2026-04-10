<?php

namespace VotrePseudo\CafeBlog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use VotrePseudo\CafeBlog\Models\Category;
use VotrePseudo\CafeBlog\Models\Post;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(6);

        return [
            'user_id'      => 1,
            'category_id'  => null,
            'title'        => $title,
            'slug'         => Str::slug($title),
            'excerpt'      => $this->faker->paragraph(2),
            'body'         => collect(range(1, 4))
                ->map(fn () => '<p>' . $this->faker->paragraph(5) . '</p>')
                ->join("\n"),
            'published_at' => $this->faker->optional(0.8)->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['published_at' => now()->subDays(rand(1, 60))]);
    }

    public function draft(): static
    {
        return $this->state(fn () => ['published_at' => null]);
    }
}
