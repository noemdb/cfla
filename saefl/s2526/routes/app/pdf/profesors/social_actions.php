<?php
/* PDF */

// Route::get('/social_actions/community_actions/pdf/{id}', 'PDF\CommunityActionController@boletin')->name('profesors.social_actions.community_actions.pdf');
Route::get('/social_actions/community_actions/pdf/{id}', 'PDF\CommunityActionController@community_action')->name('profesors.social_actions.community_actions.pdf');
Route::get('/social_actions/community_actions/profesor/pdf/{id}', 'PDF\CommunityActionController@profesor')->name('profesors.social_actions.community_actions.profesor.pdf');
Route::get('/social_actions/community_hours/pdf/{estudiant_id}', 'PDF\IncidentController@CommunityHourController')->name('profesors.social_actions.community_hours.pdf');


Route::get('/social_actions/community_actions/pdf/{id}', 'PDF\CommunityActionController@estudiant')->name('profesors.social_actions.estudiant.pdf');

?>
