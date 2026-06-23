<?php


Route::get('/fill_prosecucions',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\UpdateEstudiants::fill_prosecucions();
});


?>