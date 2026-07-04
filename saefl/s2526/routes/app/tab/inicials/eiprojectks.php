<?php

/* resource */

use App\Http\Controllers\Inicial\Tab\EiprojectkController;

Route::get('/eiprojectks', [EiprojectkController::class,'index'])->name('inicials.eiprojectks.index');
Route::get('/eiprojectks/format/{id}', [EiprojectkController::class,'format'])->name('inicials.eiprojectks.format.index');

/*
eiplanningwks
eiplanningbwks
eiprojectks
eispecialks
eievaluationks
eifinalks
*/

?>
