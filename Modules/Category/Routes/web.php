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
    Route::get('/categories/list', 'CategoryController@listAllCategories');
    Route::resource('/categories', 'CategoryController')->except('create', 'edit');
    Route::post('/category/{id}/addusers','CategoryController@addusers');


    Route::get('/sub-categories', 'SubCategoryController@listAllCategories');


//    Route::get('/sub-categories/list', 'SubCategoryController@listAllCategories');

});
