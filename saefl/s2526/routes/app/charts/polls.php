<?php

Route::get('charts/polls/general', 'Chart\PollController@general')->name('administracion.polls.general.chart');
Route::get('charts/polls/questions', 'Chart\PollController@questions')->name('administracion.polls.questions.chart');
Route::get('charts/polls/timeline', 'Chart\PollController@timeline')->name('administracion.polls.timeline.chart');
Route::get('charts/polls/tracking', 'Chart\PollController@tracking')->name('administracion.polls.tracking.chart');
Route::get('charts/polls/question', 'Chart\PollController@question')->name('administracion.polls.question.chart');
Route::get('charts/polls/userxarea', 'Chart\PollController@userxarea')->name('administracion.polls.userxarea.chart');

?>