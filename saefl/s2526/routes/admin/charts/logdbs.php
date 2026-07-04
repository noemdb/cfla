<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('models/charts/logdbs', 'Chart\LogdbsController@index')->name('logdbs.chart');
Route::get('models/charts/logdbsmonths', 'Chart\LogdbsController@LogdbsMonth')->name('logdbs.months.chart');
Route::get('models/charts/logdbsusers', 'Chart\LogdbsController@LogdbsUsers')->name('logdbs.users.chart');
Route::get('models/charts/logdbsactions', 'Chart\LogdbsController@LogdbsActions')->name('logdbs.actions.chart');
// Route::get('models/charts/logdbsusers', 'Chart\LogdbsController@RolesUsers')->name('logdbs.users.chart');
// Route::get('models/charts/logdbstypes', 'Chart\LogdbsController@LogdbsTypes')->name('logdbs.types.chart');
// Route::get('models/charts/logdbsranges', 'Chart\LogdbsController@LogdbsRange')->name('logdbs.ranges.chart');


?>