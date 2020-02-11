<?php

use Illuminate\Http\Request;

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

Route::get('resources', 'ResourceController@index');
Route::get('resources/{id}', 'ResourceController@show');
Route::any('resources/{resource}/contacts', 'ResourceController@resourceContacts');
Route::post('resources', 'ResourceController@uploadSource');

Route::get('contacts', 'ContactController@index');
Route::get('contacts/{id}', 'ContactController@show');
