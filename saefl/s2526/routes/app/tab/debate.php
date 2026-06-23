<?php

/* resource */

use App\Http\Controllers\Administracion\Tab\Educational\DebateController;

Route::get('/educational/debate/index', [DebateController::class,"index"])->name('administracion.educational.debate.index');
Route::get('/educational/debate/indicators', [DebateController::class,"indicators"])->name('administracion.educational.debate.indicators');
// Route::get('/educational/debate/fix', [DebateController::class,"fix"])->name('administracion.educational.debate.fix');
// Route::get('/educational/debate/fix2', [DebateController::class,"fix2"])->name('administracion.educational.debate.fix2');

// Route::get('/educational/debate/fix3', [DebateController::class,"fix3"])->name('administracion.educational.debate.fix3');