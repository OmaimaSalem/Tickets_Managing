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

// Route::prefix('calender')->group(function() {
//     Route::get('/', 'CalenderController@index');
// });

Route::post('share-calendar','CalendarController@ajax_calendar_add_event')->name("ajax_calendar_add_event");

Route::get('share-calendar/{user_id}','CalendarController@html_calendar');

Route::group(['middleware' => ['auth'], 'prefix' => 'v-api'], function () {
    Route::get('calendar/{month}/{userid}', 'CalendarController@index');
    Route::post('calendar/event', 'CalendarController@addevent');
    Route::post('calendar/delete-event', 'CalendarController@deleteEvent');
    Route::post('calendar/update-event', 'CalendarController@updateEvent');
    Route::post('calendar/approve-event', 'CalendarController@approveEvent');
    Route::post('calendar/reject-event', 'CalendarController@rejectEvent');
    Route::get('calendar/get-categories', 'CalendarController@get_categories');
    Route::delete('calendar/delete-category/{category}', 'CalendarController@delete_category');
    Route::post('calendar/add-category', 'CalendarController@add_category');
    Route::post('calendar/edit-category/{id}', 'CalendarController@edit_category');

    Route::post('calendar/settings', 'CalendarController@calendar_settings');
    Route::get('calendar/settings', 'CalendarController@get_calendar_settings');
    Route::get('calendar/get-show-calendars', 'CalendarController@get_show_calendars');

    Route::get('calendar/get-free-times','CalendarController@get_free_times');
    Route::post('calendar/add-free-times','CalendarController@add_free_times');

    

});
  



