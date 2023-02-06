<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cart;
use App\Models\Items;
use App\Models\CartItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function __construct(){
        $this->user = new User();
        $this->cart = new Cart();
        $this->item = new Items();
        $this->cartItems = new CartItems();
    }

    /**
     * Add a new item to the cart.
     *
     * @OA\Post(
     *     path="/api/cart/addItem",
     *     tags={"Cart"},
     *     operationId="addItem",
     *     @OA\RequestBody(
     *          description="Input data format",
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="user_id",
     *                     description="The name of the item",
     *                     type="integer",
     *                 ),
     *                  @OA\Property(
     *                     property="item_id",
     *                     description="The price of the item",
     *                     type="integer",
     *                 ),
     *                 @OA\Property(
     *                     property="item_amount",
     *                     description="The price of the item",
     *                     type="integer",
     *                 ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Item added to the user cart"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid data"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Error"
     *     ),
     * )
     */
    public function addItem(Request $request){
        $validatedData = $request->validateWithBag('post', [
            'user_id' => ['bail','required', 'integer', 'min:1'],
            'item_id' => ['bail','required', 'integer', 'min:1'],
            'item_amount' => ['bail','required', 'integer', 'min:1'],
        ]);

        if(!$validatedData){
            return response()->json(['inserted' => false, 'message' => 'Invalid data was given.'], 400);
        }

        $userExists  = $this->userExists($validatedData['user_id']);
        if(!$userExists){
            return response()->json(['inserted' => false, 'message' => 'Invalid User.'], 404);
        }

        $itemExists  = $this->itemExists($validatedData['item_id']);
        if(!$itemExists){
            return response()->json(['inserted' => false, 'message' => 'Invalid Item.'], 404);
        }

        $userCart = $this->userHasCart($validatedData['user_id']);
        if(!$userCart){
            $newCart = $this->createUserCart($validatedData['user_id']);
            if(!$newCart){
                return response()->json(['inserted' => false, 'message' => 'An error has occurred, please contact the administrators.'], 500);
            }
        }

        $cartId = $this->cart->getUserCart($validatedData['user_id']);

        $addToCart = $this->addItemToCart($validatedData, $cartId);
        if(!$addToCart){
            return response()->json(['inserted' => false, 'message' => 'An error has occurred, please contact the administrators.'], 500);
        }

        $updateCartValue = $this->updateCartValue($cartId);
        if(!$updateCartValue){
            return response()->json(['inserted' => false, 'message' => 'An error has occurred, please contact the administrators.'], 500);
        }

        return response()->json(['inserted' => true], 201);

    }

    protected function itemExists($itemId){
        return $this->item->itemExists($itemId);
    }

    protected function userExists($userId){
        return $this->user->userExists($userId);
    }

    protected function userHasCart($userId){
        return $this->cart->userHasCart($userId);
    }

    protected function createUserCart($userId){
        $newCart = [
            'user_id' => $userId,
            'cart_value' => 0.00,
            'checkout' => 0
        ];

        return $this->cart->createNewCart($newCart);
    }

    protected function addItemToCart($newCartItemData, $cartId){
        $itemValue = $this->item->getItemPrice($newCartItemData['item_id']);
        $newCartItem = [
            'cart_id' => $cartId,
            'item_id' => $newCartItemData['item_id'],
            'item_value' => $itemValue,
            'item_amount' => $newCartItemData['item_amount']
        ];

        $cartHasItem = $this->cartItems->cartHasItem($newCartItem['cart_id'], $newCartItem['item_id']);

        if($cartHasItem){
            return $this->cartItems->updateCartItem($newCartItem);
        }

        return $this->cartItems->insertNewCartItem($newCartItem);
    }

    protected function updateCartValue($cartId){
        $cartValue = $this->cartItems->getCartTotal($cartId);
        if(!$cartValue){
            return false;
        }
        return $this->cart->updateCartValue($cartId, $cartValue);
    }

    /**
     * Checkout a cart.
     *
     * @OA\Post(
     *     path="/api/cart/checkout",
     *     tags={"Cart"},
     *     operationId="checkout",
     *     @OA\Parameter(
     *         name="items_id",
     *         in="path",
     *         description="User id to checkout",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment was successfull"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid data"
     *     ),
     *     @OA\Response(
     *         response=402,
     *         description="Payment Denied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal error"
     *     ),
     * )
     */
    public function checkout(Request $request){
        $validatedData = $request->validateWithBag('post', [
            'user_id' => ['bail','required', 'integer', 'min:1']
        ]);

        if(!$validatedData){
            return response()->json(['payment' => false, 'message' => 'Invalid data was given.'], 400);
        }

        $userExists  = $this->userExists($validatedData['user_id']);
        if(!$userExists){
            return response()->json(['payment' => false, 'message' => 'Invalid User.'], 404);
        }

        $userCart = $this->userHasCart($validatedData['user_id']);
        if(!$userCart){
            return response()->json(['payment' => false, 'message' => 'An error has occurred, please contact the administrators.'], 500);
        }

        $cart = $this->cart->getUserCartPaymentData($validatedData['user_id']);

        $paymentApproval = $this->paymentGateway($cart);
        if(!$paymentApproval){
            return response()->json(['payment' => false, 'message' => 'An error has occurred, please contact the administrators.'], 402);
        }

        $updateCartCheckoutStatus = $this->updateCartCheckoutStatus($cart->id);
        if(!$updateCartCheckoutStatus){
            return response()->json(['payment' => false, 'message' => 'An error has occurred, please contact the administrators.'], 500);
        }

        $saleProcessed = $this->processSale($cart);

        return response()->json(['payment' => true], 200);

    }

    protected function paymentGateway($cart){
        /**
         * Just a mock function to act as a payment gateway api, such as paypal
         */

         return true;
    }

    protected function processSale($cart){
        /**
         * Just a mock function to act as a function that will send the items in cart to processing
         */

         return true;
    }

    protected function updateCartCheckoutStatus($cartId){
        return $this->cart->updateCartCheckoutStatus($cartId, 1);
    }
}
