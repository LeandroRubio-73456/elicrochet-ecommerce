<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word.' '.$this->faker->numberBetween(1, 1000);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence,

            'icon' => 'ti-box',
            'required_specs' => [],
        ];
    }
}
