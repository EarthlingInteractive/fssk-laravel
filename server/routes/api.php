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

Route::get('/auth', 'AuthController@index');
Route::post('/auth', 'AuthController@login');
Route::post('/users/register', 'AuthController@register');
Route::delete('/auth', 'AuthController@logout');
Route::post('/forgot-password', 'AuthController@forgotPassword');
Route::post('/reset-password', 'ResetPasswordController@reset');
Route::get('/reset-password/validate-token/{token}', 'ResetPasswordController@validateToken');


//must be logged in to get to these routes
Route::middleware('auth:api')->group(function () {

	Route::get('users/{user}/todos', 'UserController@showTodos');

	Route::apiResource('users', 'UserController');
	Route::apiResource('todos', 'TodoController');

});
