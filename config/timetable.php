<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Módulo de Horarios (Timetable) — configuración de producción
    |--------------------------------------------------------------------------
    |
    | legacy_csv_dir: directorio de los CSVs legacy (blueprint/school-timetable/
    | legacy/csv) usados por `timetable:import-legacy`. En producción se puede
    | desplegar a storage/ o a una ruta de montaje y apuntar con la variable
    | de entorno TIMETABLE_LEGACY_CSV_DIR. Default: el path del blueprint (dev).
    |
    */

    'legacy_csv_dir' => env('TIMETABLE_LEGACY_CSV_DIR', base_path('blueprint/school-timetable/legacy/csv')),

];
