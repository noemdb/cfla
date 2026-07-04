<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas iniciales
|
*/
// Route::get('/', 'HomeController@index')->name('home');
// Route::get('/home', 'HomeController@index')->name('administracion.home');
Route::get('/home', 'Tab\InscripcionController@book')->name('administracion.home');

 ?>