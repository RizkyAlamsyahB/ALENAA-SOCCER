<?php

namespace Database\Factories;

use App\Models\Field;
use App\Models\Membership;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Membership::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Bronze', 'Silver', 'Gold']) . ' Membership',
            'field_id' => Field::factory(),
            'type' => $this->faker->randomElement(['bronze', 'silver', 'gold']),
            'price' => $this->faker->numberBetween(300000, 1000000),
            'description' => $this->faker->paragraph(),
            'status' => 'active',
            'session_duration' => $this->faker->randomElement([1, 2]),
            'sessions_per_week' => 3,
            'includes_ball' => $this->faker->boolean(),
            'includes_water' => $this->faker->boolean(),
            'includes_photographer' => $this->faker->boolean(),
            'photographer_duration' => $this->faker->optional()->randomElement([1, 2]),
            'image' => null,
        ];
    }
}
