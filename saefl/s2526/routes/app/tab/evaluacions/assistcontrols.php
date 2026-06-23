<?php

/* resource */

use App\Http\Controllers\Evaluacion\Tab\AssitAttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/assistcontrols/attendances/index', [AssitAttendanceController::class, 'index'] )->name('evaluacions.assistcontrols.attendances.index');

?>