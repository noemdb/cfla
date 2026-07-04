<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas get para la barra superior del layout
|
*/


Route::group(['prefix'=>'navbar','namespace'=>'Navbar'], function(){
    Route::get('/messenges', 'TopController@getApiMesseges')->name('getmessenges');
    Route::get('/tasks', 'TopController@getApiTasks')->name('gettasks');
    Route::get('/alerts', 'TopController@getApiAlerts')->name('getalerts');
    Route::get('/logdbs', 'TopController@getApiLogdbs')->name('getlogdbs');
    Route::get('/loginouts', 'TopController@getApiLoginouts')->name('getloginouts');
});
?>