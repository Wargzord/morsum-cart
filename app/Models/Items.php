<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Items extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
        'description',
    ];

    public function getAllItems(){
        return Self::paginate(15);
    }

    public function getItem($itemId){
        return Self::where('id', $itemId)->first();
    }

    public function itemExists($itemId){
        try {
            return Self::where('id', $itemId)->exists();
        }catch (\Exception $createError) {
            Log::error('User - itemExists error :: '.$createError->getMessage());
            return false;
        }
    }

    public function getItemPrice($itemId){
        try {
            return Self::select('price')->where('id', $itemId)->first()->price;
        }catch (\Exception $createError) {
            Log::error('User - getItemPrice error :: '.$createError->getMessage());
            return false;
        }
    }

    public function createNewItem($newItemData){
        try {
            $newItem = Self::create($newItemData);
            if($newItem){
                return $newItem->id;
            }
            return false;            
        }catch (\Exception $createError) {
            Log::error('Items - create error :: '.$createError->getMessage());
            return false;
        }
    }

    public function updateItem($item){
        try {
            if($item->save()){
                return $item->id;
            }
            return false;            
        }catch (\Exception $createError) {
            Log::error('Items - update error :: '.$createError->getMessage());
            return false;
        }
    }

    public function deleteItem($item){
        try {
            $id = $item->id;
            if($item->delete()){
                return $id;
            }
            return false;            
        }catch (\Exception $createError) {
            Log::error('Items - delete error :: '.$createError->getMessage());
            return false;
        }
    }
}
