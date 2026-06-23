<?php

/* resource */

Route::get('/social_actions/index', 'Tab\SocialActionController@index')->name('administracion.social_actions.index');
Route::get('/social_actions/listado', 'Tab\SocialActionController@listado')->name('administracion.social_actions.listado');


?>
