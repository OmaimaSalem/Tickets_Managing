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

// Route::prefix('attend')->group(function() {
//     Route::get('/', 'AttendController@index');
// });

Route::group(['middleware' => ['auth']], function () {
    Route::post('attends/get-date-data', 'AttendController@date_data');
    Route::post('attends/set-date-data', 'AttendController@set_date_data');
    Route::get('attends/get-assigned-projects', 'AttendController@get_assigned_projects');
    Route::post('attends/day-start', 'AttendController@set_day_start');
    Route::post('attends/day-end', 'AttendController@set_day_end');
    Route::get('attends/get-day', 'AttendController@get_day');
    Route::post('attends/break-start', 'AttendController@set_break_start');
    Route::post('attends/break-end', 'AttendController@set_break_end');
    Route::post('attends/break-status', 'AttendController@get_break_status');
    Route::post('attends/get-working-days', 'AttendController@get_working_days');
    Route::post('attends/project-start', 'AttendController@project_start');
    Route::post('attends/project-end', 'AttendController@project_end');
    Route::get('attends/get-attend-projects', 'AttendController@get_attend_projects');
    Route::post('attends/get-day-data', 'AttendController@get_day_data');
    Route::post('attends/get-employees-attends', 'AttendController@get_employees_attends');
    Route::get('attends/get-employees-attends-report', 'AttendController@get_employees_attends_report');
    Route::post('attends/attendees-report-send-email', 'AttendController@attendees_report_send_email');
    Route::post('attends/set-employee-attend', 'AttendController@set_employee_attend');
    Route::post('attends/submit-attendees-to-review', 'AttendController@submit_attendees_to_review');
    Route::post('attends/set-attendees-active', 'AttendController@set_attendees_active');
    Route::post('attends/set-attendees-rejected', 'AttendController@set_attendees_rejected');
    Route::get('attends/get-preview-attend/{attend}', 'AttendController@get_preview_attend');
    // vacations
    Route::post('vacations/add-vacation', 'AttendController@add_vacation');
    Route::post('vacations/update-vacation', 'AttendController@update_vacation');
    Route::get('vacations/get-vacations', 'AttendController@get_vacations');
    
    Route::post('vacations/get-year-data', 'AttendController@get_year_data');
    Route::post('vacations/set-year-data', 'AttendController@set_year_data');
    
    
    Route::post('vacations/get-year-holidays', 'AttendController@get_year_holidays');
    Route::post('vacations/set-year-holidays', 'AttendController@set_year_holidays');
    Route::post('vacations/delete-year-holiday', 'AttendController@delete_year_holiday');
    Route::post('vacations/edit-year-holiday', 'AttendController@edit_year_holiday');
    Route::post('vacations/create-year-holiday', 'AttendController@create_year_holiday');
    Route::post('vacations/set-week-vacations', 'AttendController@set_week_vacations');
    Route::post('vacations/get-week-vacations', 'AttendController@get_week_vacations');
    
    Route::post('vacations/get-employees-vacations', 'AttendController@get_employees_vacations');
    Route::post('vacations/set-vacation-action', 'AttendController@set_vacation_action');
    
    

});


