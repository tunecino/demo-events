<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->randomElement([
            "Ping Pong Tournament",
            "Hiking Adventure",
            "Fishing Trip",
            "Bike Tour",
        ]);

        $start = $this->faker->dateTimeBetween("+1 day", "+5 days");
        $end = (clone $start)->modify("+4 hours");

        return [
            "id" => (string) Str::uuid(),
            "name" => $name,
            "description" => $this->faker->sentence(8),
            "image" => $this->faker->imageUrl(800, 600, "sports", true),
            "start_at" => $start,
            "end_at" => $end,
            "amount" => $this->faker->numberBetween(1000, 5000),
            "currency" => "EUR",
        ];
    }
}
