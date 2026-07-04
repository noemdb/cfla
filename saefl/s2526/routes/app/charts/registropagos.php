<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/
/*administracion*/
Route::get('charts/registropagos/actividades', 'Chart\RegistroPagosController@actividades')->name('administracion.registro_pagos.actividades.chart');
Route::get('charts/registropagos/activitiesxmonth', 'Chart\RegistroPagosController@activitiesXMonth')->name('administracion.registro_pagos.activitiesxmonth.chart');

// Route::get('charts/profesors/genderxplan', 'Chart\profesorsController@genderxplan')->name('administracion.profesors.genderxplan.chart');
// Route::get('charts/profesors/genderxgrado', 'Chart\profesorsController@genderxgrado')->name('administracion.profesors.genderxgrado.chart');
// Route::get('charts/usersactives', 'Chart\UsersController@UserActive')->name('users.actives.chart');
// Route::get('charts/usersmonth', 'Chart\UsersController@UsersMonth')->name('users.months.chart');
// Route::get('models/charts/profiles', 'Chart\ProfileController@index')->name('viewchartprofiles');
// Route::get('models/charts/rols', 'Chart\RolController@index')->name('viewchartrols');

// Route::get('charts/configuraciones/bancos/ingresoxmonth', 'Chart\BancosController@IngresoXMonth')->name('administracion.configuraciones.bancos.ingresoxmonth.chart');


?>
