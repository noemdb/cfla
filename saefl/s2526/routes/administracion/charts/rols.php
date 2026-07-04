<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('models/charts/rols', 'Chart\RolsController@index')->name('rols.chart');
Route::get('models/charts/rolsmonth', 'Chart\RolsController@RolsMonth')->name('rols.month.chart');
Route::get('models/charts/rolsusers', 'Chart\RolsController@RolsUsers')->name('rols.users.chart');
Route::get('models/charts/rolstypes', 'Chart\RolsController@RolsTypes')->name('rols.types.chart');
Route::get('models/charts/rolsranges', 'Chart\RolsController@RolsRange')->name('rols.ranges.chart');


?>