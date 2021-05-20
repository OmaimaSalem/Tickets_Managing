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

Route::group(['prefix' => 'admin', 'middleware' => ['jwt.verify', 'setLang']], function() {
  Route::resource('/todos/boards', 'BoardController')->except('create', 'edit');
  Route::resource('/todos/cards', 'CardController')->except('create', 'edit');
  Route::resource('/todos/tasks', 'TaskController')->except('create', 'edit');


});
