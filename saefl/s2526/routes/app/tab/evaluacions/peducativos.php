
<?php

Route::get('/peducativos/index', [
    \App\Http\Controllers\Evaluacion\Tab\PeducativoController::class, 
    'index'
])->name('evaluacions.peducativos.index');