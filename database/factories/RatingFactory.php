<?php

namespace Database\Factories;

use App\Models\GovOrg;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Rating;
use App\Models\User;

class RatingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rating::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'gov_org_id' => GovOrg::factory(),
            'rating' => fake()->randomElement([rand(0,5)]),
            'comment' => fake()->text(),
        ];
    }
}
