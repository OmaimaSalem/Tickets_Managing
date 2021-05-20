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
	Route::get('/spamTickets', 'SpamTicketsController@index');
	Route::post('/spamTickets/add', 'SpamTicketsController@store');
	Route::post('/spamTickets/convertTicket', 'SpamTicketsController@convertTicket');
	Route::post('/spamTickets/remove', 'SpamTicketsController@destroy');
	Route::get('/spamTickets/{id}', 'SpamTicketsController@show');
});
