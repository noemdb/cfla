<?php

/* resource */

use App\Http\Controllers\Inicial\Tab\EiplanningwkController;

Route::get('/eiplanningwks', [EiplanningwkController::class,'index'])->name('inicials.eiplanningwks.index');
Route::get('/eiplanningwks/format/index/{id}', [EiplanningwkController::class,'format'])->name('inicials.eiplanningwks.format.index');

/*
eiplanningwks
eiplanningbwks
eiprojectks
eispecialks
eievaluationks
eifinalks
*/

?>


