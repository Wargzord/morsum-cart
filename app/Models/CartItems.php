<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class CartItems extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cart_id',
        'item_id',
        'item_value',
        'item_amount'
    ];

    public function cartHasItem($cartId, $itemId){
        try {
            return Self::where('cart_id', $cartId)->where('item_id', $itemId)->exists();
        }catch (\Exception $createError) {
            Log::error('Cart - cartHasItem error :: '.$createError->getMessage());
            return false;
        }
    }

    public function insertNewCartItem($newCartItem){
        try {
            if(Self::create($newCartItem)){
                return true;
            }
            return false;
        }catch (\Exception $createError) {
            Log::error('Cart - insertNewCartItem error :: '.$createError->getMessage());
            return false;
        }
    }

    public function updateCartItem($newCartItem){
        try {
            $cartItem = Self::where('item_id', $newCartItem['item_id'])
                ->where('cart_id', $newCartItem['cart_id'])->first();
            $cartItem->item_amount = $cartItem->item_amount + $newCartItem['item_amount'];
            if($cartItem->save()){
                return true;
            }
            return false;
        }catch (\Exception $createError) {
            Log::error('Cart - updateCartItem error :: '.$createError->getMessage());
            return false;
        }
    }

    public function getCartTotal($cartId){
        try {

            return Self::selectRaw('sum(item_value * item_amount) as cart_value')->where('cart_id', $cartId)->groupBy('cart_id')->first()->cart_value;

        }catch (\Exception $createError) {
            Log::error('Cart - insertNewItem error :: '.$createError->getMessage());
            return false;
        }
    }
    
}
