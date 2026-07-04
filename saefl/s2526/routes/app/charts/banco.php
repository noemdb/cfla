<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('charts/bancos/configuraciones/ingresoxmonth', 'Chart\BancosController@IngresoXMonth')->name('administracion.configuraciones.bancos.ingresoxmonth.chart');
Route::get('charts/bancos/configuraciones/ingresoxdia', 'Chart\BancosController@IngresoXdia')->name('administracion.configuraciones.bancos.ingresoxdia.chart');
Route::get('charts/bancos/configuraciones/ingresoxdayxmonth', 'Chart\BancosController@IngresoXdayMonth')->name('administracion.configuraciones.bancos.ingresoxdayxmonth.chart');
Route::get('charts/bancos/configuraciones/ingresoxmetodo', 'Chart\BancosController@IngresoXMetodo')->name('administracion.configuraciones.bancos.ingresoxmetodo.chart');
Route::get('charts/bancos/configuraciones/deuda_representate_month', 'Chart\BancosController@deuda_representate_month')->name('administracion.configuraciones.bancos.deuda_representate_month.chart');
// Route::get('charts/administrativas/genderxplan', 'Chart\AdministrativasController@genderxplan')->name('administracion.administrativas.genderxplan.chart');
// Route::get('charts/administrativas/genderxgrado', 'Chart\AdministrativasController@genderxgrado')->name('administracion.administrativas.genderxgrado.chart');

?>
