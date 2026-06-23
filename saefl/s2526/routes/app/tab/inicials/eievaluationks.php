<?php

/* resource */

use App\Http\Controllers\Inicial\Tab\EievaluationkController;

Route::get('/eievaluationks', [EievaluationkController::class,'index'])->name('inicials.eievaluationks.index');
Route::get('/eievaluationks/format/index/{id}', [EievaluationkController::class,'format'])->name('inicials.eievaluationks.format.index');

/*
eiplanningwks
eiplanningbwks
eiprojectks
eispecialks
eievaluationks
eifinalks
*/

?>
