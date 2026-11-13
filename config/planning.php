<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Plan de clases / secuencia didáctica
    |--------------------------------------------------------------------------
    |
    | La bandera oculta el botón y los endpoints del wizard sin necesidad de
    | desplegar código. Se activa por configuración/entorno y se valida con un
    | usuario canario antes de habilitarla para todo el módulo de planificación.
    |
    */

    'didactic_plans_enabled' => (bool) env('DIDACTIC_PLANS_ENABLED', false),

];
