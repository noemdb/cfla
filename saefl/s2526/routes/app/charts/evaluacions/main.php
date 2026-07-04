<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

/*control.evaluacions*/
Route::get('charts/evaluacions/actividades', 'Chart\EvaluacionController@actividades')->name('evaluacions.charts.evaluacions.actividades');
Route::get('charts/evaluacions/lessons', 'Chart\EvaluacionController@lessons')->name('evaluacions.charts.leaders.lessons');


?>
