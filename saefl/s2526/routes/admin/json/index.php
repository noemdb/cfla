<?php 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| archivo main (/admin/Json) para llas rutas JSON
|
*/

Route::group(['prefix'=>'json','namespace'=>'Json'], function(){

    //INI Charts
    Route::group(['prefix'=>'charts','namespace'=>'Charts'], function(){

        //INI graficas para users
        require (__DIR__ . '/charts/users.php');            
        //INI graficas para users

        //INI graficas para profiles
        require (__DIR__ . '/charts/profiles.php');            
        //FIN graficas para profiles

        //INI graficas para rols
        require (__DIR__ . '/charts/rols.php');            
        //FIN graficas para rols
        
        //INI graficas para tasks
        require (__DIR__ . '/charts/tasks.php');            
        //FIN graficas para tasks
        
    });
    //FIN Charts
    
    //INI Navbar
    require (__DIR__ . '/navbar/top.php');
    //FIN Navbar        

});

?>