<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\GovOrg;
use App\Models\Post;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'content' => fake()->paragraphs(3, true),
            'platform' => fake()->word(),
            'account' => fake()->word(),
            'gov_org_id' => GovOrg::factory(),
        ];
    }
}
