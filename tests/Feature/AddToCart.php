<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddToCart extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_add_to_cart()
    {
        $response = $this->postJson('/api/addToCart', ['product_id' => '1', 'quantity' => '2']);
 
        $response
            ->assertStatus(201)
            ->assertJson([
                'created' => true,
            ]);

    }
}
