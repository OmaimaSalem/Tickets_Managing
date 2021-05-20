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
Auth::routes();
Route::group(['namespace' => 'Frontend'], function () {
    Route::get('/', 'HomeController@index');
    Route::post('/contact', 'HomeController@contactPost')->name('contactPost');
    Route::get('/impressum', 'HomeController@impressum')->name('impressum');
    Route::get('/agb', 'HomeController@agb')->name('agb');
});

Route::group(['middleware' => ['auth'], 'namespace' => 'Vue'], function () {
    Route::get('/admin', 'VueController@index')->name('admin');
});


Route::group(['middleware' => ['auth'], 'namespace' => 'API', 'prefix' => 'v-api'], function () {
    // roles
    Route::get('/roles/list', 'RolesController@list');
    Route::get('/roles/getall', 'RolesController@getAll');
    Route::resource('/roles', 'RolesController')->except('show', 'create');

    // permissions
    Route::get('/permissions/list', 'PermissionsController@list');
    Route::get('/permissions/getall', 'PermissionsController@getAll');
    Route::resource('/permissions', 'PermissionsController')->except('show', 'create');

    // users
    Route::get('/user/getAllResponsibles', 'UsersController@getAllResponsibles');
    Route::get('/user/getClientsPaginated', 'UsersController@getClientsPaginated');
    Route::get('/user/getEmployeesPaginated', 'UsersController@getEmployeesPaginated');
    Route::get('/user/getClientsPerProject/{id}', 'UsersController@getClientsPerProject');
    Route::post('/user/addToSpam/{id}', 'UsersController@addToSpam');
    Route::post('/user/removeFromSpam/{id}', 'UsersController@removeFromSpam');
    Route::post('/user/addToSupport/{id}', 'UsersController@addToSupport');
    Route::post('/user/removeFromSupport/{id}', 'UsersController@removeFromSupport');
    // Route::get('/user/get-current-user-working-time', 'UsersController@get_current_user_working_time');

    Route::resource('/users', 'UsersController')->except('create');

    //clients routes
    Route::resource('/clients', 'ClientController')->except(['create', 'edit']);

    // metadata
    Route::resource('/metadata', 'MetadataController');

    // projects
    Route::get('/projects/index', 'ProjectController@view')->name('project.view');
    Route::get('/projects/list', 'ProjectController@list');
    Route::get('/project/getAllByOwner/{owner_id}', 'ProjectController@getAllByOwner');
    Route::get('/clients/{client_id}/projectsNumber', 'ProjectController@getProjectsCountPerClient');
    Route::get('/clients/{client_id}/projects', 'ProjectController@getProjectsPerClient');
    Route::post('/projects/merge','ProjectController@mergeProjects');
    Route::post('/project/addToFav/{project_id}','ProjectController@addToFav');
    Route::post('/project/RemoveFromFav/{project_id}','ProjectController@RemoveFromFav');
    Route::get('/project/getFavorites','ProjectController@getFavorites');
    Route::post('/project/copyProject','ProjectController@copyProject');
    Route::get('/project/getCalender/{project_id}','ProjectController@getCalender');
    Route::resource('/projects', 'ProjectController')->except('create');

    // tracking tasks
    Route::get('/tracking/timeReporting', 'Tracking_taskController@timeReporting');
    Route::patch('/tracking/{task_id}/{tracking_id}', 'Tracking_taskController@update');
    Route::post('/tracking/{task_id}', 'Tracking_taskController@store');
    Route::delete('/tracking/{task_id}/{tracking_id}', 'Tracking_taskController@destroy');
    Route::get('/tracking/{task_id}', 'Tracking_taskController@tracking');
    Route::get('/tracking/checkTrackingInProgress/{task_id}', 'Tracking_taskController@checkTrackingInProgress');
    Route::get('/tracking/history/{task_id}', 'Tracking_taskController@getHistory');

    // tickets
    Route::post('/tickets/changeduedate', 'TicketController@changeduedate');
    Route::get('/tickets/getTicketDescription/{id}', 'TicketController@getTicketDescription');
    Route::get('/tickets/copyTicket/{ticket}', 'TicketController@copyticket');
    Route::post('/tickets/saveTrack', 'TicketController@saveTrack');
    Route::post('/tickets/starttime', 'TicketController@starttime');
    Route::post('/tickets/endtime', 'TicketController@endtime');

    Route::get('/tickets/gettrackhistory/{ticket_id}','TicketController@getTrackHistory');
    Route::delete('/tickets/deletetrackhistory/{track_id}','TicketController@deleteTrackHistory');
    Route::put('/tickets/edittrackhistory', 'TicketController@editTicketTrackHistory');

    Route::post('/tickets/addTicketToArchive', 'TicketController@addTicketToArchive');
    Route::post('/tickets/removeTicketFromArchive', 'TicketController@removeTicketFromArchive');

    Route::get('/projects/{project_id}/tickets/', 'TicketController@getTicketsByProjectId');
    Route::get('/clients/{client_id}/ticketsNumber', 'TicketController@getTicketsCountPerClient');
    Route::get('/clients/{client_id}/tickets', 'TicketController@getTicketsPerClient');
    Route::post('/tickets/createTicketCollection', 'TicketController@create_ticket_collection');
    Route::post('/tickets/updateTicketCollection', 'TicketController@update_ticket_collection');
    Route::post('/tickets/deleteTicketCollection', 'TicketController@delete_ticket_collection');
    Route::post('/tickets/deleteMultiTickets', 'TicketController@deleteMultiTickets');
    
    
    Route::post('/tickets/open-multi-tickets', 'TicketController@openMultiTickets');
    Route::post('/tickets/close-multi-tickets', 'TicketController@closeMultiTickets');


    Route::get('/tickets/getTicketCollection', 'TicketController@get_ticket_collection');
    Route::post('/tickets/{project_id}', 'TicketController@store');
    Route::get('/tickets/kanban','TicketController@kanban');
    Route::get('/tickets/users','TicketController@users');
    Route::get('/tickets/clients','TicketController@clients');
    Route::get('/tickets/projects','TicketController@projects');
    Route::get('/tickets/getTicketFiltered', 'TicketController@getTicketFiltered');
    Route::get('/tickets/getTicketChartsData', 'TicketController@getTicketChartsData');
    Route::put('/tickets/{ticket}/updatestatus/{status_id}', 'TicketController@update_ticket_status');

    Route::resource('/tickets', 'TicketController')->except('create');

    // task
    Route::get('/tasks', 'TaskController@index');
    Route::get('/tickets/{ticket_id}/tasks/', 'TaskController@getTasksByTicketId');
    Route::get('/projects/{project_id}/tasks/', 'TaskController@getTasksByProjectId');
    Route::get('/clients/{client_id}/tasksNumber', 'TaskController@getTasksCountPerClient');
    Route::get('/clients/{client_id}/tasks', 'TaskController@getTasksPerClient');
    Route::get('/tasks/filter', 'TaskController@filterTasks');
    Route::get('/tasks/cards', 'TaskController@tasksCard');
    Route::get('/tasks/allCards', 'TaskController@allCards');
    Route::get('/tasks/allProjects', 'TaskController@allProjects');
    Route::post('/tasks', 'TaskController@store');
    Route::put('/tasks/{id}/updatestatus/{status_id}', 'TaskController@update_task_status');
    Route::resource('/tasks', 'TaskController')->except('create', 'store', 'index');


    // owner
    Route::get('/owner/getall', 'UsersController@getClients');

    // receipt
    Route::get('/receipts/list', 'ReceiptController@list');
    Route::get('/receipts/getall', 'ReceiptController@getAll');
    Route::resource('/receipts', 'ReceiptController')->except('create', 'store');
    Route::post('/receipts/{project_id}', 'ReceiptController@store');

    // status
    Route::get('/status/getAll', 'StatusController@getAll');

    // attributes
    Route::get('/attribute/{user_type}', 'AttributeController@index');
    Route::get('/users-attributes', 'AttributeController@users_attributes');
    Route::post('/save-user-attributes', 'AttributeController@save_user_attributes');


    Route::post('/attribute-email', 'AttributeController@create_attribute_email');
    Route::get('/get-attributes-emails', 'AttributeController@get_attributes_emails');
    Route::post('/edit-attribute-email/{id}', 'AttributeController@edit_attribute_email');
    Route::delete('/delete-attribute-email/{id}', 'AttributeController@delete_attribute_email');


    Route::get('/clients-attributes', 'AttributeController@clients_attributes');



    Route::resource('/attribute', 'AttributeController')->except('create', 'edit','show');

    //project discussion
    Route::get('/project-discussion/{project_id}','ProjectDiscussionController@index');
    Route::post('/project-discussion/{project_id}','ProjectDiscussionController@store');
    Route::get('/single-discussion/{id}','ProjectDiscussionController@show');
    Route::post('/single-discussion/{id}','ProjectDiscussionController@reply');
    Route::put('/single-discussion/{id}','ProjectDiscussionController@addClients');
    Route::delete('/single-discussion/{id}','ProjectDiscussionController@destroy');
    Route::post('/single-discussion/deleteClient/{id}','ProjectDiscussionController@deleteClient');




    // notifications routes
    Route::get('/all-notifications','NotificationController@all_notifications');
    Route::get('/latest-notification','NotificationController@latest_notification');
    Route::get('/latest-layout-notification','NotificationController@latest_layout_notification');
    Route::get('/reset-layout-notification','NotificationController@reset_layout_notification');
    Route::post('/read-notification','NotificationController@read_notification');
    Route::post('/read-all-notifications','NotificationController@read_all_notifications');
    

});


Route::get('/{path}', 'Vue\VueController@index')->where('path', '^(?!v-api|share-calendar|storage/calendar_attachments).*$');
