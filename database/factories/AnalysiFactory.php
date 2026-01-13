<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Analyst;
use App\Models\Post;

class AnalystFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Analyst::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'sentiment' => fake()->randomElement(["positive","negative","normal"]),
            'stance' => fake()->randomElement(["supportive","against"]),
        ];
    }
}
