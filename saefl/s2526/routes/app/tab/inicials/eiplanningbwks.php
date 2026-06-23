<?php

/* resource */

use App\Http\Controllers\Inicial\Tab\EiplanningbwkController;

Route::get('/eiplanningbwks', [EiplanningbwkController::class,'index'])->name('inicials.eiplanningbwks.index');
Route::get('/eiplanningbwks/format/index/{id}', [EiplanningbwkController::class,'format'])->name('inicials.eiplanningbwks.format.index');

/*
eiplanningwks
eiplanningbwks
eiprojectks
eispecialks
eievaluationks
eifinalks
*/

?>
