<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/items', [ItemsController::class, 'index']);
Route::post('/items', [ItemsController::class, 'store']);
Route::get('/items/{items_id}', [ItemsController::class, 'show']);
Route::put('/items/{items_id}', [ItemsController::class, 'update']);
Route::delete('/items/{items_id}', [ItemsController::class, 'destroy']);


Route::post('cart/addItem', [CartController::class, 'addItem']);
Route::post('cart/checkout', [CartController::class, 'checkout']);
