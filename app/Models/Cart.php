<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Cart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'cart_value',
        'checkout'
    ];

    public function userHasCart($userId){
        try {
            return Self::where('user_id', $userId)->where('checkout', 0)->exists();
        }catch (\Exception $createError) {
            Log::error('Cart - userHasCart error :: '.$createError->getMessage());
            return false;
        }
    }
    
    public function getUserCart($userId){
        try {
            $data = Self::select('id')->where('user_id', $userId)->where('checkout', 0)->first();
            return $data->id;
        }catch (\Exception $createError) {
            Log::error('Cart - getUserCart error :: '.$createError->getMessage());
            return false;
        }
    }

    public function getUserCartPaymentData($userId){
        try {
            $data = Self::select('id', 'cart_value')->where('user_id', $userId)->where('checkout', 0)->first();
            return $data;
        }catch (\Exception $createError) {
            Log::error('Cart - getUserCart error :: '.$createError->getMessage());
            return false;
        }
    }

    public function createNewCart($newCart){
        try {
            $test = Self::create($newCart);
            if($test){
                return true;
            }
            return false;
        }catch (\Exception $createError) {
            Log::error('Cart - createNewCart error :: '.$createError->getMessage());
            return false;
        }
    }

    public function updateCartValue($cartId, $cartValue){
        try {
            $updateValue = Self::where('id', $cartId)
                ->update(['cart_value' => $cartValue]);
            if($updateValue){
                return true;
            }
            return false;
        }catch (\Exception $createError) {
            Log::error('Cart - createNewCart error :: '.$createError->getMessage());
            return false;
        }
    }

    public function updateCartCheckoutStatus($cartId, $checkout){
        try {
            $updateCheckout = Self::where('id', $cartId)
                ->update(['checkout' => $checkout]);
            if($updateCheckout){
                return true;
            }
            return false;
        }catch (\Exception $createError) {
            Log::error('Cart - createNewCart error :: '.$createError->getMessage());
            return false;
        }
    }
}
