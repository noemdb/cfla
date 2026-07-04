<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

/*control.evaluacions*/
Route::get('charts/learnings/actividades', 'Chart\EvaluacionController@actividades')->name('learnings.charts.evaluacion.actividades');
Route::get('charts/learnings/lessons/diaries', 'Chart\EvaluacionController@lessons_diaries')->name('learnings.charts.lessons.diaries');


?>
