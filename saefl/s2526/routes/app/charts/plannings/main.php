<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

/*control.evaluacions*/
Route::get('charts/evaluacions/actividades', 'Chart\EvaluacionController@actividades')->name('plannings.charts.evaluacions.actividades');
Route::get('charts/evaluacions/lessons', 'Chart\EvaluacionController@lessons')->name('plannings.charts.leaders.lessons');


?>
