<?php

namespace Database\Factories;

use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;

class FieldFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Field::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true) . ' Field',
            'type' => $this->faker->randomElement(['Futsal', 'Soccer', 'Basketball', 'Volleyball']),
            'price' => $this->faker->numberBetween(100000, 500000),
            'description' => $this->faker->paragraph(),
            'image' => null,
            'status' => 'active',
        ];
    }
}
