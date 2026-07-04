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

	//INI SYS
		Route::resource('models/crud/users','Crud\UserController');
		Route::resource('models/crud/profiles','Crud\ProfileController');
		Route::resource('models/crud/rols','Crud\RolController');

		Route::resource('models/crud/tasks','Crud\TaskController');
		Route::resource('models/crud/messeges','Crud\MessegeController');
		Route::resource('models/crud/alerts','Crud\AlertController');

		Route::resource('models/crud/loginouts','Crud\LoginoutController');
		Route::resource('models/crud/logdbs','Crud\LogdbController');

		Route::resource('models/crud/settings','Crud\SettingController');
		Route::resource('models/crud/selectopts','Crud\SelectoOptController');
	//FIN SYS

	//INI LOGICA DEL NEGOCIO

	//FIN LOGICA DEL NEGOCIO

// FIN resource

 ?>