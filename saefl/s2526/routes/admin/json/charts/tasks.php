<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas get para los graficos de tasks
|
*/

Route::group(['prefix'=>'tasks'], function(){
    Route::get('/taskmonth', 'TasksController@getApiTaskMonth')->name('taskmonth');
    Route::get('/uservrstask', 'TasksController@getApiUserTaskLoad')->name('uservrstask');
    Route::get('/uservrstaskasig', 'TasksController@getApiUserTaskAsig')->name('uservrstaskasig');
    Route::get('/uservrstaskdone', 'TasksController@getApiUserTaskDone')->name('uservrstaskdone');
});


 ?>