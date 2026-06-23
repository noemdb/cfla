<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('charts/preinscripcions/gender', 'Chart\PreinscripcionController@gender')->name('administracion.preinscripcions.gender.chart');
Route::get('charts/preinscripcions/genderxplan', 'Chart\PreinscripcionController@genderxplan')->name('administracion.preinscripcions.genderxplan.chart');
Route::get('charts/preinscripcions/genderxgrado', 'Chart\PreinscripcionController@genderxgrado')->name('administracion.preinscripcions.genderxgrado.chart');
// Route::get('charts/usersactives', 'Chart\UsersController@UserActive')->name('users.actives.chart');
// Route::get('charts/usersmonth', 'Chart\UsersController@UsersMonth')->name('users.months.chart');
// Route::get('models/charts/profiles', 'Chart\ProfileController@index')->name('viewchartprofiles');
// Route::get('models/charts/rols', 'Chart\RolController@index')->name('viewchartrols');


?>