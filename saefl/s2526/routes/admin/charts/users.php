<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('models/charts/users', 'Chart\UsersController@index')->name('users.chart');
Route::get('models/charts/usersactives', 'Chart\UsersController@UserActive')->name('users.actives.chart');
Route::get('models/charts/usersmonth', 'Chart\UsersController@UsersMonth')->name('users.months.chart');
// Route::get('models/charts/profiles', 'Chart\ProfileController@index')->name('viewchartprofiles');
// Route::get('models/charts/rols', 'Chart\RolController@index')->name('viewchartrols');


?>