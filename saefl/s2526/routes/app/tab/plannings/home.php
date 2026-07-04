<?php

/* resource */

use App\Http\Controllers\Planning\Tab\HomePlanningController;

Route::get('/home', [HomePlanningController::class,'home'])->name('plannings.home');
Route::get('/indicators', [HomePlanningController::class,'indicators'])->name('plannings.indicators');

?>
