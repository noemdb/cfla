<?php

use App\Http\Controllers\Academico\Tab\ManagerRegisterController;

Route::get('/manager_registers/index', [ManagerRegisterController::class,'index'])->name('academicos.manager_registers.index');

?>
