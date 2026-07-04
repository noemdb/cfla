<?php

// activity_log
$schedule->command('backup:truncate-table')
->when(function () {             
    return now()->isLastOfMonth(); // Ejecutar solo el último día del mes
})
->at('23:50'); // O cualquier hora deseada

?>
