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

// Route::prefix('ticketcalendar')->group(function() {
//     Route::get('/', 'TicketCalendarController@index');
// });

Route::group(['middleware' => ['auth'], 'prefix' => 'v-api'], function () {
    Route::get('ticket/{ticket}/events', 'TicketCalendarController@events');
    Route::post('ticket/{ticket}/add-event', 'TicketCalendarController@add_event');
    Route::put('ticket/{ticket}/event/{event}/edit', 'TicketCalendarController@edit_event');
    Route::delete("/ticket/{ticket}/event/{event}/delete",'TicketCalendarController@delete_event');
});
