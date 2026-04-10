<?php

namespace VotrePseudo\CafeBlog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use VotrePseudo\CafeBlog\Models\Category;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->word();
        return [
            'name'        => ucfirst($name),
            'slug'        => Str::slug($name),
            'description' => $this->faker->sentence(),
        ];
    }
}
