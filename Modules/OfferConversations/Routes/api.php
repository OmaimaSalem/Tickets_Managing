<?php



Route::resource('/offerconversations', 'OfferConversationsController')->except('create', 'edit');
Route::get('/conversationsPerOffer/{offer_id}', 'OfferConversationsController@getConversationsPerOffer');
