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
    Route::post('/reports/project-users', 'EmployeeProjectsReportController@index');
    Route::get('/reports/project-users', 'EmployeeProjectsReportController@export');



    Route::get('/reports/time-tracking', 'TimeTrackingReportController@index');
    Route::get('/reports/financial', 'FinancialReportController@index');
    Route::get('/reports/vacations', 'VacationsReportController@index');



    Route::get('/reports/project-status', 'ProjectStatusController@index');



    Route::get('/reports/project-employee-tasks', 'EmployeeTasksReportController@index');





});

