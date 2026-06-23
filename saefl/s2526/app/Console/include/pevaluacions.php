<?php

// INI Eliminar notas duplicadas
    $schedule->call(function () {
        $fix_db = App\Http\Controllers\Admin\FixDB\PevaluacionsTrait::boletins_duplicate();
    })->runInBackground()->dailyAt('08:00')->dailyAt('14:00')->dailyAt('20:00');
// FIN Eliminar notas duplicadas

?>
