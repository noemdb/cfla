<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los graficos del usuario
|
*/

Route::get('models/charts/messeges', 'Chart\MessegesController@index')->name('messeges.chart');
Route::get('models/charts/messegesmonth', 'Chart\MessegesController@MessegesMonth')->name('messeges.months.chart');
Route::get('models/charts/messegesusers', 'Chart\MessegesController@MessegesUsers')->name('messeges.users.chart');
Route::get('models/charts/messegestypes', 'Chart\MessegesController@MessegesTypes')->name('messeges.types.chart');

?>