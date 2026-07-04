<?php


Route::get('/toggle_gemail_email_representants',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateRepresentants::toggle_gemail_email_representants();
});

Route::get('/update_gemail_representants',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateRepresentants::update_gemail_representants();
});

Route::get('/user_update_email_representants',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateRepresentants::user_update_email_representants();
});


Route::get('/create_users_representant',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\CreateUserDB::create_users_representant();
});

Route::get('/update_users_email_representant',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\CreateUserDB::update_users_email_representant();
});


?>
