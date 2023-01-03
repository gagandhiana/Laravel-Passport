<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\UserController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// https://github.com/anil-sidhu/laravel-passport-poc  (Refrence)


Route::post('register', 'App\Http\Controllers\API\UserController@register');

Route::group(['middleware' => 'auth:api'], function(){
Route::get('details', 'App\Http\Controllers\API\UserController@details');
});


Route::post('login', 'App\Http\Controllers\API\UserController@login'); 
