<?php 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| rutas get para los graficos de users
|
*/

Route::group(['prefix'=>'users'], function(){
    Route::get('/usersmonth', 'UserController@UsersMonth')->name('usersmonth');
    Route::get('/usersactive', 'UserController@UserActive')->name('usersactive');
    Route::get('/usersconnect', 'UserController@UserConnect')->name('usersconnect');
});


 ?>