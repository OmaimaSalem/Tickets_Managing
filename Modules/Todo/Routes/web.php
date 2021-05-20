<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth'], 'prefix' => 'v-api'], function () {
  Route::resource('/todos/boards', 'BoardController');
  Route::post('/todos/boards/assignusers', 'BoardController@assignUsers');
  Route::get('/todos/cards/{id}', 'CardController@getCardsByBoardId');
  Route::resource('/todos/cards', 'CardController');
  Route::resource('/todos/tasks', 'TaskController');
  Route::put('/todos/tasksStatus', 'TaskController@updateTaskStatus');
  Route::put('/todos/tasksStatus', 'TaskController@updateTaskStatus');
  Route::put('/todos/tasksCard', 'TaskController@updateTaskCard');
});
