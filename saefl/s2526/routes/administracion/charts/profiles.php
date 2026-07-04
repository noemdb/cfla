<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('models/charts/profiles', 'Chart\ProfilesController@index')->name('profiles.chart');
Route::get('models/charts/profilesmonth', 'Chart\ProfilesController@ProfilesMonth')->name('profiles.month.chart');
Route::get('models/charts/profilesusers', 'Chart\ProfilesController@ProfilesUsers')->name('profiles.users.chart');
// Route::get('models/charts/usersactives', 'Chart\UsersController@UserActive')->name('users.actives.chart');
// Route::get('models/charts/usersmonth', 'Chart\UsersController@UsersMonth')->name('users.months.chart');
// Route::get('models/charts/profiles', 'Chart\ProfileController@index')->name('viewchartprofiles');
// Route::get('models/charts/rols', 'Chart\RolController@index')->name('viewchartrols');


?>