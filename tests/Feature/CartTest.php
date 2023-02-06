<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\User;
use App\Models\Cart;
use App\Models\Items;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A create one item test.
     *
     * @return void
     */
    public function test_add_item_to_cart (){
        $newUser = User::factory()->create();
        $newItem = Items::factory()->create();

        $itemToAdd = [
            'user_id' => $newUser->id,
            'item_id' => $newItem->id,
            'item_amount' => rand(1,10)
        ];

        $response = $this->withoutExceptionHandling()->postJson('/api/cart/addItem', $itemToAdd);

        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('inserted')
                    ->where('inserted', true)
            );
    }

    /**
     * A create one item test.
     *
     * @return void
     */
    public function test_cart_checkout (){
        $newCart = Cart::factory()->create();

        $itemToAdd = [
            'user_id' => $newCart->user_id
        ];

        $response = $this->withoutExceptionHandling()->postJson('/api/cart/checkout', $itemToAdd);

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('payment')
                    ->where('payment', true)
            );
    }
}
