<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cart;
use App\Models\Items;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItems>
 */
class CartItemsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'cart_id' => Cart::factory()->create()->id,
            'item_id' => Items::factory()->create()->id,
            'item_value' => fake()->randomFloat(2, 50, 500),
            'item_amount' => fake()->numberBetween(1, 10)
        ];
    }
}
