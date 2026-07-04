<?php


Route::get('/boletins_duplicate',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\PevaluacionsTrait::boletins_duplicate();
});

?>
