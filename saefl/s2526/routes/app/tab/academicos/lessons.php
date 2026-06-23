<?php

/* resource */

use App\Http\Controllers\Academico\Tab\LessonController;

Route::get('/lessons/index', [LessonController::class, 'index'])->name('academicos.lessons.index');

?>
