<?php


Route::get('/update_gemail_profesors',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateProfesors::update_gemail_profesors();
});
Route::get('/users_profesors',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateProfesors::users_profesors();
});

?>
