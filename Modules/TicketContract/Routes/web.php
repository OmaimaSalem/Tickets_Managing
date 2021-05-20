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

Route::group(['middleware' => ['auth'], 'prefix' => 'v-api'],function() {
    Route::get('/ticket_contract/excel-export','TicketContractController@excel_export');
    Route::resource('/ticket_contract', 'TicketContractController');
});    