<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\Items;

class ItemsTest extends TestCase{
    use RefreshDatabase;
    
    /**
     * A list all items test.
     *
     * @return void
     */
    public function test_list_all_items(){
        Items::factory()->create();

        $response = $this->withoutExceptionHandling()->getJson('/api/items');
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('current_page')
                    ->has('data')
                    ->has('first_page_url')
                    ->has('from')
                    ->has('last_page')
                    ->has('last_page_url')
                    ->has('links')
                    ->has('next_page_url')
                    ->has('path')
                    ->has('per_page')
                    ->has('prev_page_url')
                    ->has('to')
                    ->has('total')
                    ->has('data.0', fn ($json) =>
                        $json->whereType('id', 'integer')
                            ->whereType('name', 'string')
                            ->whereType('price', 'double|integer')
                            ->whereType('description', 'string')
                            ->etc()
                     )
            );
    }

    /**
     * A list one item test.
     *
     * @return void
     */
    public function test_show_one_item(){
        $item = Items::factory()->create();

        $response = $this->withoutExceptionHandling()->getJson('/api/items/'.$item->id);

        $response->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('id', $item->id)
                ->where('name', $item->name)
                ->where('price', $item->price)
                ->where('description', $item->description)
                ->etc()
            );
    }

    /**
     * A create one item test.
     *
     * @return void
     */
    public function test_create_item(){
        $newItem = $this->assembleNewItem();

        $response = $this->withoutExceptionHandling()->postJson('/api/items', $newItem);
 
        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->hasAll(['id', 'created'])
                    ->missingAll(['name', 'price', 'description'])
                    ->where('created', true)
            );
    }

    protected function assembleNewItem() : array {
        $newItemFactory = Items::factory()->make();

        $newItem = [
            'name' => $newItemFactory->name,
            'price' => $newItemFactory->price,
            'description' => $newItemFactory->description,
        ];

        return $newItem;
    }

    /**
     * A update item test.
     *
     * @return void
     */
    public function test_update_item(){
        $item = Items::factory()->create();

        $newItem = $this->assembleNewItem();

        $response = $this->withoutExceptionHandling()->putJson('/api/items/'.$item->id, $newItem);
 
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->hasAll(['id', 'updated'])
                    ->missingAll(['name', 'price', 'description'])
                    ->where('id', $item->id)
                    ->where('updated', true)
            );
    }

    /**
     * A delete item test.
     *
     * @return void
     */
    public function test_delete_item(){
        $item = Items::factory()->create();

        $response = $this->withoutExceptionHandling()->deleteJson('/api/items/'.$item->id);
        
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->hasAll(['id', 'deleted'])
                    ->missingAll(['name', 'price', 'description'])
                    ->where('deleted', true)
            );
    }
}
