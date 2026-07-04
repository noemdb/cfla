<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('models/charts/tasks', 'Chart\TasksController@index')->name('tasks.chart');
Route::get('models/charts/tasksmonths', 'Chart\TasksController@TasksMonth')->name('tasks.months.chart');
Route::get('models/charts/tasksusers', 'Chart\TasksController@TasksUsers')->name('tasks.users.chart');
Route::get('models/charts/taskstypes', 'Chart\TasksController@TasksTypes')->name('tasks.types.chart');

?>