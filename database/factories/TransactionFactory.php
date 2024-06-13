<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $precision = 2;
        $min = 1;
        $max = 100;
        // Generate a random integer within the specified range
        $randomInteger = mt_rand($min * pow(10, $precision), $max * pow(10, $precision));

        // Convert the integer to a float by dividing by 10^precision
        $randomFloat = $randomInteger / pow(10, $precision);

        return [
            'user_id' => \App\Models\User::all()->random()->id,
            'amount' => $randomFloat,
            'status' => fake()->randomElement(['pending', 'completed', 'failed']),
        ];
    }
}
