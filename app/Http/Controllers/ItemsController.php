<?php

namespace App\Http\Controllers;

use App\Models\Items;
use Illuminate\Http\Request;

class ItemsController extends Controller{
    public function __construct(){
        $this->items = new Items();
    }

    /**
     * List all Items
     * 
     * @OA\Get(
     *     path="/api/items",
     *     tags={"Items"},
     *     summary="List all Items",
     *     description="List all items currently in the platform database, paginated",
     *     operationId="index",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     )
     * )
     */
    public function index(){
        return response()->json($this->items->getAllItems());
    }

    /**
     * Add a new item to the database.
     *
     * @OA\Post(
     *     path="/api/items",
     *     tags={"Items"},
     *     operationId="store",
     *     @OA\RequestBody(
     *          description="Input data format",
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     description="The name of the item",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="price",
     *                     description="The price of the item",
     *                     type="float",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="The price of the item",
     *                     type="string",
     *                 ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Item saved"
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Invalid input"
     *     ),
     * )
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
     * @OA\Get(
     *     path="/api/items/{items_id}",
     *     tags={"Items"},
     *     summary="Show one Item",
     *     description="Show one Item based on the ID sent by the user",
     *     operationId="show",
     *     @OA\Parameter(
     *         name="items_id",
     *         in="path",
     *         description="Item id to search",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item not found"
     *     )
     * 
     * )
     */
    public function show($items_id){
        $items = $this->items->getItem($items_id);
        if(!$items){
            return response()->json($items, 404);
        }
        return response()->json($items);
    }

    /**
     * Update an existing Item.
     *
     * @OA\Put(
     *     path="/api/items/{items_id}",
     *     tags={"Items"},
     *     operationId="update",
     *     @OA\Parameter(
     *         name="items_id",
     *         in="path",
     *         description="Item id to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         ),
     *     ),
     *     @OA\RequestBody(
     *          description="Input data format",
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     description="The name of the item",
     *                     type="string",
     *                 ),
     *                  @OA\Property(
     *                     property="price",
     *                     description="The price of the item",
     *                     type="float",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="The price of the item",
     *                     type="string",
     *                 ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid Data"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item not found"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item updated"
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/items/{items_id}",
     *     tags={"Items"},
     *     summary="Deletes an Item",
     *     operationId="destroy",
     *     @OA\Parameter(
     *         name="items_id",
     *         in="path",
     *         description="Item id to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid ID supplied",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Item not found",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item excluded",
     *     )
     * )
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
