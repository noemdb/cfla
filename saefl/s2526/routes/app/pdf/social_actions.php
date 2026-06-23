<?php
/* PDF */

// Route::get('/social_actions/community_actions/pdf/{id}', 'PDF\CommunityActionController@boletin')->name('profesors.social_actions.community_actions.pdf');
Route::get('/social_actions/community_actions/pdf/{id}', 'PDF\CommunityActionController@estudiant')->name('administracion.social_actions.estudiant.pdf');
Route::get('/social_actions/community_actions/profesor/pdf/{id}', 'PDF\CommunityActionController@profesor')->name('administracion.social_actions.community_actions.profesor.pdf');

?>
