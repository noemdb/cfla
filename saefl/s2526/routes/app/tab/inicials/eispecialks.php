<?php

/* resource */

use App\Http\Controllers\Inicial\Tab\EispecialkController;

Route::get('/eispecialks', [EispecialkController::class,'index'])->name('inicials.eispecialks.index');
Route::get('/eispecialks/format/index/{id}', [EispecialkController::class,'format'])->name('inicials.eispecialks.format.index');

?>
