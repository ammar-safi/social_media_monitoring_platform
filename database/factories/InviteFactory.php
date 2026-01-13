<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Invite;
use App\Models\User;
use Carbon\Carbon;

class InviteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invite::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $options = [
            Carbon::now()->subMonths(random_int(1, 3)),
            Carbon::now()->addDays(random_int(1, 10)),
            Carbon::now()->addMonths(random_int(1, 3)),
        ];
        $date = $options[array_rand($options)];
        return [
            'token' => fake()->word(),
            'user_id' => User::factory(),
            'email' => fake()->safeEmail(),
            'status' => fake()->randomElement(["pending", "approved", "rejected"]),
            'expired_at' => $date,
            'expired' => $date->isPast() ? 0 : 1,
        ];
    }
}
