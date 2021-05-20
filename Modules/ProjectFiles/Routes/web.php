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
//
//Route::prefix('projectfiles')->group(function() {
//    Route::get('/', 'ProjectFilesController@index');
//});


Route::group(['middleware' => ['auth'], 'prefix' => 'v-api'], function () {
//    Route::resource('/todos/boards', 'BoardController');
//    Route::post('/project_files/createFolder', 'ProjectFilesController@createFolder');
    Route::resource('/project_files/folders', 'FoldersController');
    Route::get('/project_files/folder/getSingleFolder/{id}', 'FoldersController@getSingleFolder');
    Route::post('/project_files/folder/copyToFolder', 'FoldersController@copyToFolder');
    Route::post('/project_files/folder/moveToFolder', 'FoldersController@moveToFolder');
    Route::post('/project_files/folder/uploadInFolder', 'FoldersController@uploadInFolder');
    Route::resource('/project_files/files', 'FilesController');
//    Route::get('/todos/cards/{id}', 'ProjectFilesController@getCardsByBoardId');
//    Route::resource('/todos/cards', 'ProjectFilesController');
//    Route::resource('/todos/tasks', 'ProjectFilesController');
//    Route::put('/todos/tasksStatus', 'ProjectFilesController@updateTaskStatus');
//    Route::put('/todos/tasksStatus', 'ProjectFilesController@updateTaskStatus');
//    Route::put('/todos/tasksCard', 'ProjectFilesController@updateTaskCard');
});
