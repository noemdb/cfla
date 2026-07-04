<?php

/* resource App\Http\Controllers\Profesor\SocialActions */

Route::get('/social_actions/index', 'SocialActions\HomeController@index')->name('profesors.social_actions.index');
Route::get('/social_actions/community_actions/index', 'SocialActions\Tab\CommunityActionController@index')->name('profesors.social_actions.community_actions.index');
Route::get('/social_actions/community_hours/index', 'SocialActions\Tab\CommunityHourController@index')->name('profesors.social_actions.community_hours.index');

?>
