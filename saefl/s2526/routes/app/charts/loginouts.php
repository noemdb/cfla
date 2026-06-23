<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('models/charts/loginouts', 'Chart\LoginoutsController@index')->name('loginouts.chart');
Route::get('models/charts/loginoutsmonths', 'Chart\LoginoutsController@LoginoutsMonth')->name('loginouts.months.chart');
Route::get('models/charts/loginoutsusers', 'Chart\LoginoutsController@LoginoutsUsers')->name('loginouts.users.chart');
Route::get('models/charts/loginoutstypes', 'Chart\LoginoutsController@LoginoutsTypes')->name('loginouts.types.chart');
// Route::get('models/charts/rolsusers', 'Chart\RolsController@RolesUsers')->name('rols.users.chart');
// Route::get('models/charts/rolstypes', 'Chart\RolsController@RolsTypes')->name('rols.types.chart');
// Route::get('models/charts/rolsranges', 'Chart\RolsController@RolsRange')->name('rols.ranges.chart');


?>