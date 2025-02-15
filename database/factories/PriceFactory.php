<?php

namespace Database\Factories;

use App\Models\Price;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Price>
 */
class PriceFactory extends Factory
{
    protected $model = Price::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startAt = fake()->optional()->dateTimeBetween('-1 year', '+1 year');
        $endAt = fake()->optional()->dateTimeBetween('-1 year', '+1 year');

        return [
            'start_at' => $startAt?->format('Y-m-d H:i:s'),
            'end_at' => $endAt?->format('Y-m-d H:i:s'),
            'is_weekend' => fake()->boolean(),
            'is_default' => fake()->boolean(),
            'price' => fake()->numberBetween(10, 1000),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
