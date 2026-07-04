<?php

/* PollMain */
Route::get('/polls/index', 'Tab\Polls\PollMainController@index')->name('administracion.polls.index');
Route::get('/polls/competitors', 'Tab\Polls\PollMainController@competitors')->name('administracion.polls.competitors');
Route::get('/polls/analyzers', 'Tab\Polls\PollMainController@analyzers')->name('administracion.polls.analyzers');


/* PollQuestion */
Route::get('/polls/questions/index', 'Tab\Polls\PollMainController@questions')->name('administracion.polls.questions.index');

/* PollQuestion */
Route::get('/polls/options/index', 'Tab\Polls\PollMainController@options')->name('administracion.polls.options.index');

?>
