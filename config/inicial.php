<?php

/*
|--------------------------------------------------------------------------
| Módulo de Educación Inicial
|--------------------------------------------------------------------------
| Configuración del módulo de Educación Inicial (preescolar / kínder), cuyo
| plan de estudio es `pestudios.id = 6` ("EDUCACION INICIAL", grados 22 = 1ER
| GRUPO, 23 = 2DO GRUPO, 24 = 3ER GRUPO — ids idénticos en el legacy `s2526`).
|
| Blueprint: blueprint/inicial · decisiones D4 (constantes de dominio) y D6
| (máximos de indicadores sin tocar el esquema).
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Plan de estudio
    |--------------------------------------------------------------------------
    | Identificador del plan de estudio de Educación Inicial. Se usa en lugar
    | del literal `6` que el legacy repetía en cada listado
    | (`Grado::list_pestudio_grado(6)`, `Profesor::list_profesors_pestudio(6)`).
    */
    'pestudio_id' => 6,

    /*
    |--------------------------------------------------------------------------
    | Grados de Educación Inicial
    |--------------------------------------------------------------------------
    | Grupos de edad queabras el currículo (áreas y expectativas de aprendizaje).
    | Coinciden con los `grado_id` que usa `EILearningSeeder`.
    */
    'grados' => [22, 23, 24],

    /*
    |--------------------------------------------------------------------------
    | Perspectivas de acceso
    |--------------------------------------------------------------------------
    | Traducción de las 4 perspectivas del legacy a los flags booleanos de
    | cfla (decisión D2). El legacy las resolvía con la tabla `rols`
    | (área/rol/vigencia) y 4 checks `Is{...}` que se solapaban entre sí.
    |
    | `pestudio_id` es la constante del módulo (D4).
    */
    'perspectivas' => [
        // Docente de Inicial: CRUD completo de los 6 documentos + formatos.
        'inicial' => 'isInicial',
        // Coordinación de Evaluación: revisión + escritura de
        // `observacion` (planes) y `recomendacion` (evaluaciones).
        'evaluacion' => 'isDiagnostic',
        // Planificación: solo lectura con filtros.
        'planning' => 'isPlanner',
        // Académico/Dirección: solo lectura limitada.
        'academico' => 'isAdmin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Indicadores estadísticos (decisión D6)
    |--------------------------------------------------------------------------
    | El legacy leía `peducativos.max_number_eiplanningwks`, `..._bwks`,
    | `..._projectks`, `..._specialks`, `..._evaluationks` y `..._finalks`.
    | ESAS COLUMNAS NO EXISTEN: ni en `s2526` ni en `s2627` — el legacy además
    | pintaba badges hardcodeados y falsos (+12%, +5, 67%), que aquí no se
    | portan en ninguna circunstancia.
    |
    | Estos máximos alimentan los indicadores de la perspectiva de Evaluación
    | (fase F5). While no se ajusten, los contadores se muestran como dato
    | absoluto y NUNCA como porcentaje contra un máximo inventado.
    */
    'max_number' => [
        'eiplanningwks' => null,
        'eiplanningbwks' => null,
        'eiprojectks' => null,
        'eispecialks' => null,
        'eievaluationks' => null,
        'eifinalks' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rejilla de estrategias
    |--------------------------------------------------------------------------
    | La rejilla es día de la semana × momento de la rutina diaria:
    | 5 × 10 = 50 celdas por documento.
    |
    | ⚠️ QUIRK DE PERSISTENCIA: el texto de la estrategia se guarda SIEMPRE en
    | la columna `lunes` de las tablas `*strategies`; el día real va en
    | `day_of_week`. Está preservado a propósito porque los ~2.000 registros
    | que se migrarán desde `s2526` tienen esa forma (decisión D3). La UI solo
    | debe leer y escribir el atributo virtual `estrategia`.
    */
    'dias_semana' => [
        'lunes' => 'Lunes',
        'martes' => 'Martes',
        'miercoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
    ],

    'momentos_rutina_diaria' => [
        'Recibimiento' => 'Recibimiento',
        'Momento Cívico' => 'Momento Cívico',
        'Aseo-Desayuno-Aseo' => 'Aseo-Desayuno-Aseo',
        'Periodo: Planificación' => 'Periodo: Planificación',
        'Periodo: Trabajo Libre' => 'Periodo: Trabajo Libre',
        'Periodo: Orden y limpieza' => 'Periodo: Orden y limpieza',
        'Periodo: Intercambio y Recuento' => 'Periodo: Intercambio y Recuento',
        'Periodo: Trabajos en Pequeños Grupos' => 'Periodo: Trabajos en Pequeños Grupos',
        'Periodo: Actividades Colectivas' => 'Periodo: Actividades Colectivas',
        'Periodo: Despedida' => 'Periodo: Despedida',
    ],

];
