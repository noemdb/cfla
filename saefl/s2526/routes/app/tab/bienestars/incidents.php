<?php

/* resource */

Route::get('/incidents', 'Tab\IncidentController@index')->name('bienestars.incidents.index');
Route::get('/incidents/summaries', 'Tab\IncidentController@summaries')->name('bienestars.incidents.summaries');
Route::get('/incidents/overviews', 'Tab\IncidentController@overviews')->name('bienestars.incidents.overviews');

?>
