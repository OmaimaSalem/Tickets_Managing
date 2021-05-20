<?php


Route::group(['middleware' => ['auth'], 'prefix' => 'v-api'], function () {
//	Route::resource('/ticketReply', 'ReplyController')->except('create', 'edit');
//	Route::resource('/ticketComment', 'CommentController')->except('create', 'edit');

//    Route::post('/single-discussion/{id}','ProjectDiscussionController@reply');
    Route::get('/ticketReply/{id}', 'ReplyController@index');
    Route::post('/ticketReply', 'ReplyController@store');
    Route::post('/ticketSubReply', 'ReplyController@storeSub');
    Route::get('/ticketComment/{id}', 'CommentController@index');
    Route::post('/ticketComment', 'CommentController@store');
    Route::post('/ticketSubComment', 'CommentController@storeSub');
    Route::delete('/ticketComment/{id}', 'CommentController@destroy');
    Route::put('/ticketComment', 'CommentController@update');
    Route::delete('/ticketSubComment/{id}', 'CommentController@SubDestroy');
    Route::put('/ticketSubComment', 'CommentController@SubUpdate');

});
