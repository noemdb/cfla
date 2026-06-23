<?php


Route::get('/update_work_id_users',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateUsers::update_work_id_users();
});

Route::get('/update_assit_schedule_id_rol',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateUsers::update_assit_schedule_id_rol();
});


Route::get('/update_user_to_number_id',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateUsers::update_user_to_number_id();
});


?>
