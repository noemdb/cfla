<?php


Route::get('/update_gemail_profesors',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateProfesors::update_gemail_profesors();
});

Route::get('/update_gemail_profesors_ci',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateProfesors::update_gemail_profesors_ci();
});

Route::get('/users_profesors',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateProfesors::users_profesors();
});

Route::get('/user_update_email_profesors',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateProfesors::user_update_email_profesors();
});

Route::get('/update_card_number_profesors',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateProfesors::update_card_number_profesors();
});

Route::get('/user_update_email_profesors_email',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateProfesors::user_update_email_profesors_email();
});

?>
