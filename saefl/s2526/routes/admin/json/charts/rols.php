<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas get para los graficos de rols
|
*/

Route::group(['prefix'=>'rols'], function(){
    Route::get('/rolsmonth', 'RolController@RolsMonth')->name('rolsmonth');
    Route::get('/rolestipos', 'RolController@RolsType')->name('rolestipos');
    Route::get('/rangostipos', 'RolController@RangeType')->name('rangostipos');
});


 ?>