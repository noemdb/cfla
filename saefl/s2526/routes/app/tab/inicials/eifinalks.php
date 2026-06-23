<?php

/* resource */

use App\Http\Controllers\Inicial\Tab\EifinalkController;
use Illuminate\Support\Facades\Route;

Route::get('/eifinalks', [EifinalkController::class, 'index'])->name('inicials.eifinalks.index');
Route::get('/eifinalks/{eifinalk}/print', [EifinalkController::class, 'print'])->name('inicials.eifinalks.print');
Route::get('/eifinalks/estudiant/{estudiant}/lapso/{lapso}/print-all', [EifinalkController::class, 'printAllforLapso'])->name('inicials.eifinalks.print-all-for-lapso');

/*
eiplanningwks
eiplanningbwks
eiprojectks
eispecialks
eievaluationks
eifinalks
*/
