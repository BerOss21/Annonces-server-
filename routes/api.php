<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('user/login', 'UserController@login');
Route::post('user/register', 'UserController@register');
Route::patch('user/update/{id}', 'UserController@update')->middleware("auth:api");

Route::resource('annoucements','AnnoucementController');
Route::get('myAnnoucements', 'AnnoucementController@myAnnoucements');
Route::resource('cities', 'CityController');
Route::resource('categories', 'CategoryController');
