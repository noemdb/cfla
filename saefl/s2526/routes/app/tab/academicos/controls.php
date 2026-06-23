<?php

use App\Http\Controllers\Academico\Tab\ControlController;

Route::get('/control/performance', [ControlController::class,'performance'])->name('academicos.control.performance');

?>
