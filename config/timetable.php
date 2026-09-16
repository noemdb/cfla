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

    /*
    |--------------------------------------------------------------------------
    | Solver — estrategia con fallback (PLAN-TIMETABLE-SOLVER-FALLBACK-001)
    |--------------------------------------------------------------------------
    |
    | budget_seconds: presupuesto total de la cadena de intentos por corrida.
    | attempt_seconds: deadline de cada intento (salvo el primero, que recibe
    |   la mitad del presupuesto para no penalizar el orden original).
    | restarts: número de reinicios aleatorios deterministas (ORDER_RANDOM).
    | half_group_priority: prioriza y agrupa los medio-grupos (HG-01..HG-04).
    | half_group_bonus: puntos soft por agrupar un medio-grupo junto a otro de
    |   la misma sección (debe ser < 100, el peso de "día distinto").
    | shared_teacher_priority: prioriza y agrupa las lecciones con
    |   allow_shared_teacher (ST-01..ST-03), consolidando sus bloques.
    | shared_teacher_bonus: puntos soft por agrupar dos lecciones de docente
    |   compartido del mismo profesor en el mismo período (< 100).
    |
    */

    'solver' => [
        'budget_seconds' => (int) env('TIMETABLE_SOLVER_BUDGET_SECONDS', 30),
        'attempt_seconds' => (int) env('TIMETABLE_SOLVER_ATTEMPT_SECONDS', 8),
        'restarts' => (int) env('TIMETABLE_SOLVER_RESTARTS', 6),
        'half_group_priority' => (bool) env('TIMETABLE_SOLVER_HALF_GROUP_PRIORITY', true),
        'half_group_bonus' => (int) env('TIMETABLE_SOLVER_HALF_GROUP_BONUS', 20),
        'shared_teacher_priority' => (bool) env('TIMETABLE_SOLVER_SHARED_TEACHER_PRIORITY', true),
        'shared_teacher_bonus' => (int) env('TIMETABLE_SOLVER_SHARED_TEACHER_BONUS', 15),
        'section_draft_budget_seconds' => (int) env('TIMETABLE_SOLVER_SECTION_DRAFT_BUDGET_SECONDS', 120),
    ],

    /*
    |--------------------------------------------------------------------------
    | Optimizador de sección (Paso 5) — motor híbrido
    |--------------------------------------------------------------------------
    |
    | Reorganiza los bloques de UNA sección combinando grafo/coloreo (soluciones
    | iniciales), CSP (restricciones duras) y una función de penalizaciones tipo
    | MILP (restricciones blandas) optimizada con búsqueda local.
    |
    | time_budget_seconds: presupuesto total de la optimización por sección.
    | restarts: reinicios aleatorios de la búsqueda local.
    | csp_node_budget: nodos máximos explorados por el backtracking.
    | weights: pesos de la función objetivo (mayor = más prioritario).
    |   hard: violaciones duras (docente/aula/sección).
    |   external: solape con la carga del docente en otros P.Estudios.
    |   gap: huecos entre clases del docente en el día.
    |   distribution: bloques de una misma materia concentrados el mismo día.
    |   room: bloques prácticos sin aula asignada.
    |   off_shift: bloques fuera del turno natural de la lección.
    |
    */

    'section_optimizer' => [
        'time_budget_seconds' => (float) env('TIMETABLE_SECTION_OPTIMIZER_SECONDS', 8),
        'restarts' => (int) env('TIMETABLE_SECTION_OPTIMIZER_RESTARTS', 10),
        'csp_node_budget' => (int) env('TIMETABLE_SECTION_OPTIMIZER_CSP_NODES', 20000),
        'weights' => [
            'hard' => (int) env('TIMETABLE_SECTION_OPTIMIZER_W_HARD', 1000),
            'external' => (int) env('TIMETABLE_SECTION_OPTIMIZER_W_EXTERNAL', 100),
            'gap' => (int) env('TIMETABLE_SECTION_OPTIMIZER_W_GAP', 10),
            'distribution' => (int) env('TIMETABLE_SECTION_OPTIMIZER_W_DISTRIBUTION', 25),
            'room' => (int) env('TIMETABLE_SECTION_OPTIMIZER_W_ROOM', 15),
            'off_shift' => (int) env('TIMETABLE_SECTION_OPTIMIZER_W_OFF_SHIFT', 30),
        ],
    ],

];
