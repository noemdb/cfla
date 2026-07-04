<?php

Route::get('/movement_estudiants',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateEstudiants::movement_estudiants();
});

Route::get('/update_gemail_estudiants',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateEstudiants::update_gemail_estudiants();
});

Route::get('/create_users_estudiants',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\CreateUserDB::create_users_estudiants();
});
Route::get('/update_estudiant_saldos',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateEstudiantSaldos::update_estudiant_saldos();
});

Route::get('/update_estudiant_pagos',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateEstudiantPagos::update_estudiant_pagos();
});

Route::get('/update_gemail_inscripcions',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateInscripcions::update_gemail_inscripcions();
});

Route::get('/update_hnotas_grupo_estable_id',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateHnotas::update_hnotas_grupo_estable_id();
});

?>
