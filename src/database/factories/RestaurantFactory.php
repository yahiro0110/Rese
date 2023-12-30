<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Restaurant>
 */
class RestaurantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => '',
            'email' => $this->faker->unique()->safeEmail(),
            'tel' => $this->faker->phoneNumber,
            'postal' => $this->faker->postcode,
            'address' => $this->faker->prefecture . $this->faker->city . $this->faker->streetAddress,
            'description' => '',
            'prefecture_id' => $this->faker->numberBetween(1, 47),
            'genre_id' => '',
            'user_id' => $this->faker->numberBetween(3, 50),
        ];
    }
}
