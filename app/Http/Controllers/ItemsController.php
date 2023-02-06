<?php

namespace App\Http\Controllers;

use App\Models\Items;
use Illuminate\Http\Request;

class ItemsController extends Controller{
    public function __construct(){
        $this->items = new Items();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return response()->json($this->items->getAllItems());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $validatedData = $request->validateWithBag('post', [
            'name' => ['bail','required', 'max:200'],
            'price' => ['bail','required', 'decimal:2'],
            'description' => ['bail','nullable', 'string'],
        ]);

        if(!$validatedData){
            return response()->json(['created' => false], 400);
        }

        $newItem = $this->items->createNewItem($validatedData);

        if(!$newItem){
            return response()->json(['created' => false], 400);
        }

        return response()->json(['created' => true, 'id' => $newItem], 201);

    }

    /**
     * Output the specified resource.
     *
     * @param  \App\Models\Items  $items
     * @return \Illuminate\Http\Response
     */
    public function show($items_id){
        $items = $this->items->getItem($items_id);
        if(!$items){
            return response()->json($items, 404);
        }
        return response()->json($items);
    }

    /**
     * Update the specified resource in storage .
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Items  $items
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $items_id){

        $items = $this->items->getItem($items_id);
        if(!$items){
            return response()->json(['updated' => false], 404);
        }
        
        $validatedData = $request->validateWithBag('post', [
            'name' => ['bail','required', 'max:200'],
            'price' => ['bail','required', 'decimal:2'],
            'description' => ['bail','nullable', 'string'],
        ]);

        if(!$validatedData){
            return response()->json(['updated' => false], 400);
        }

        $items = $this->updateItemData($items, $validatedData);;

        $updatedItem = $this->items->updateItem($items);

        if(!$updatedItem){
            return response()->json(['updated' => false], 400);
        }

        return response()->json(['updated' => true, 'id' => $updatedItem], 200);
        
    }

    protected function updateItemData($items, $newItemData){
        $items->name = $newItemData['name'];
        $items->price = $newItemData['price'];
        $items->description = $newItemData['description'];

        return $items;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Items  $items
     * @return \Illuminate\Http\Response
     */
    public function destroy($items_id)
    {
        $items = $this->items->getItem($items_id);
        if(!$items){
            return response()->json(['deleted' => false], 404);
        }

        $deletedItem = $this->items->deleteItem($items);

        if(!$deletedItem){
            return response()->json(['deleted' => false], 400);
        }

        return response()->json(['deleted' => true, 'id' => $deletedItem], 200);
    }
}
