<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas para los CRUD (solo resource) de los diferentes modelos DB
|
*/


// INI resource
	Route::resource('models/crud/tasks','Crud\TaskController');
	Route::resource('models/crud/messeges','Crud\MessegeController');
	Route::resource('models/crud/alerts','Crud\AlertController');
// FIN resource

 ?>