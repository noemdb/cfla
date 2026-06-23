<?php

/* resource */

use App\Http\Controllers\Evaluacion\Tab\LessonController;

Route::get('/leaders/lessons/index', [LessonController::class, 'index'] )->name('evaluacions.leaders.lessons.index');

?>