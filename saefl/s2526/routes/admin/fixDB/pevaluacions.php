<?php


Route::get('/move_pevaluacions',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\PevaluacionsTrait::movePevaluacions();
});

Route::get('/fix_evaluacions_status_execution',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\PevaluacionsTrait::fix_evaluacions_status_execution();
});

?>
