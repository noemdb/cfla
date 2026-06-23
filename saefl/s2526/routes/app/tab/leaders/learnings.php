<?php

/* resource */
Route::get('/learnings/index', 'Tab\LearningController@index')->name('leaders.learnings.index');

Route::get('/learnings/performance', 'Tab\LearningController@performance')->name('leaders.learnings.performance');

?>
