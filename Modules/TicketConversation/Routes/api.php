<?php


Route::resource('/ticketconversations', 'TicketConversationController')->except('create', 'edit');
Route::get('/conversationsPerTicket/{ticket_id}', 'TicketConversationController@getConversationsPerTicket');
