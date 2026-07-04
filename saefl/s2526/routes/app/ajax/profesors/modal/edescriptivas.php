<?php

/*fill parials*/
Route::get('/ajax/fill/parials/modal/edescriptivas/create/{id}', 'Ajax\FillPartialController@EdescriptivaCreate')->name('profesors.ajax.fill.modal.edescriptiva.create');
Route::get('/ajax/fill/parials/modal/edescriptivas/details/{id}', 'Ajax\FillPartialController@EdescriptivaDetails')->name('profesors.ajax.fill.modal.edescriptiva.details');
