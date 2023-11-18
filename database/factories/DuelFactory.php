<?php

namespace Database\Factories;

use App\Enums\DuelStatusEnum;
use App\Models\FakeOpponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Duel>
 */
class DuelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'fake_opponent_id' => FakeOpponent::factory(),
            'user_points' => $this->faker->numberBetween(0, 100),
            'opponent_points' => $this->faker->numberBetween(0, 100),
            'status' => DuelStatusEnum::Finished->value,
        ];
    }
}
