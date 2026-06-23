<?php

/* coll_politicals */
Route::get('/coll_politicals/index', 'Tab\Collection\CollPoliticalController@index')->name('administracion.collections.coll_politicals.index');
Route::get('/coll_politicals/representant/group', 'Tab\Collection\CollPoliticalController@representantGroup')->name('administracion.collections.coll_politicals.representant.group');
Route::get('/coll_politicals/asistent', 'Tab\Collection\CollPoliticalController@asistent')->name('administracion.collections.coll_politicals.asistent');
Route::get('/coll_politicals/group', 'Tab\Collection\CollPoliticalController@group')->name('administracion.collections.coll_politicals.group');
Route::get('/coll_politicals/group/for/politicals', 'Tab\Collection\CollPoliticalController@groupPoliticals')->name('administracion.collections.coll_politicals.group.politicals');
Route::post('/coll_politicals/store/full', 'Tab\Collection\CollPoliticalController@fullStore')->name('administracion.collections.coll_politicals.fullStore');
Route::post('/coll_politicals/ajax/preview', 'Tab\Collection\CollPoliticalController@preview')->name('administracion.collections.coll_politicals.preview');
Route::get('/coll_politicals/ajax/preview/id/{id}', 'Tab\Collection\CollPoliticalController@previewMsnId')->name('administracion.collections.coll_politicals.preview.id');

Route::get('/coll_politicals/queuing/email/send/{id}', 'Tab\Collection\CollPoliticalController@EmailForQueuing')->name('administracion.collections.coll_politicals.queuing.email.send');
// Route::get('/coll_politicals/queuing/email/send/{id}', 'Tab\Collection\CollPoliticalController@EmailForQueuingAnuality')->name('administracion.collections.coll_politicals.queuing.email.send');

Route::get('/coll_politicals/crud/', 'Tab\Collection\CollPoliticalController@crud')->name('administracion.collections.coll_politicals.crud');
Route::get('/coll_politicals/create', 'Tab\Collection\CollPoliticalController@create')->name('administracion.collections.coll_politicals.create');
Route::post('/coll_politicals/store', 'Tab\Collection\CollPoliticalController@store')->name('administracion.collections.coll_politicals.store');
Route::get('/coll_politicals/edit/{id}', 'Tab\Collection\CollPoliticalController@edit')->name('administracion.collections.coll_politicals.edit');
Route::put('/coll_politicals/update/{id}', 'Tab\Collection\CollPoliticalController@update')->name('administracion.collections.coll_politicals.update');
Route::delete('/coll_politicals/destroy/{id}', 'Tab\Collection\CollPoliticalController@destroy')->name('administracion.collections.coll_politicals.destroy');

Route::get('/coll_politicals/calendars/index', 'Tab\Collection\CollPoliticalController@coll_calendars')->name('administracion.collections.calendars.index');



Route::get('/coll_politicals/test', 'Email\CollectionScheduleController@bacthCollectionSendScheduleTest')->name('administracion.coll_politicals.shudule.test');


?>
