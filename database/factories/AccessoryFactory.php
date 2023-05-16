<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Accessory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accessory>
 */
class AccessoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category' => $this->faker->randomElement(['case', 'screen protector', 'charger', 'cable', 'earphone', 'power bank', 'memory card', 'usb flash drive', 'speaker', 'others']),
            'brand' => $this->faker->randomElement(['Samsung', 'DBrand', 'Apple']),
            'type' => $this->faker->sentence(),
            'color' => $this->faker->colorName,
            'supplier_id' => $this->faker->numberBetween(1, 100),
            'guarantee' => $this->faker->randomElement(['1 year', '2 years', '3 years']),
            'price' => $this->faker->numberBetween(100, 1000),
            'sell_price' => $this->faker->numberBetween(200, 1200),
            'has_storage' => $this->faker->boolean,
            'comment' => $this->faker->sentence,
        ];
    }
}
