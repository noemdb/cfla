<?php


Route::get('/fix_ingresos_pea',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\Ingresos::fix_ingresos_pea();
});


Route::get('/fix_ingresos_pm',function (){
    $fix_db = App\Http\Controllers\Admin\FixDB\Ingresos::fix_ingresos_pm();
});

?>
