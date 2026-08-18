<?php

/**
 * Configuración centralizada de menús por rol.
 *
 * Cada rol define:
 * - label: nombre visible del grupo
 * - icon: SVG path o componente de icono
 * - permission: gate/permission requerido (opcional)
 * - route: nombre de ruta base
 * - active: patrón de routeIs para estado activo
 * - children: array de sub-items (para mega-menús)
 * - badge: componente Livewire opcional para contador
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Definición de grupos de menú (reutilizables entre roles)
    |--------------------------------------------------------------------------
    */
    'groups' => [

        // ---- ADMIN ----
        'admin' => [
            'label' => 'Admin',
            'icon' => 'M4 6h16M4 12h16M4 18h16',
            'permission' => 'is_admin_or_diagnostic',
            'color' => 'emerald',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'admin.index',
                    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                    'active' => 'admin.index',
                ],
                [
                    'label' => 'Usuarios',
                    'route' => 'admin.users.index',
                    'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                    'active' => 'admin.users.*',
                ],
            ],
            'admin_only_items' => [
                [
                    'label' => 'Votaciones',
                    'route' => 'admin.voting.dashboard',
                    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                ],
                [
                    'label' => 'Logs',
                    'route' => 'admin.logs',
                    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'active' => 'admin.logs',
                ],
                [
                    'label' => 'Bitácora',
                    'route' => 'admin.binnacle',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                    'active' => 'admin.binnacle',
                ],
                [
                    'label' => 'Métricas de Auditoría',
                    'route' => 'admin.binnacle.dashboard',
                    'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                    'active' => 'admin.binnacle.dashboard',
                ],
                [
                    'label' => 'Línea de Actividad',
                    'route' => 'admin.binnacle.timeline',
                    'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                    'active' => 'admin.binnacle.timeline',
                ],
                [
                    'label' => 'Contenido LMS',
                    'route' => 'app.planning.lms.monitor',
                    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'active' => 'app.planning.lms.*',
                    'badge' => 'planning.lms.lesson-pending-count',
                ],
            ],
        ],

        // ---- COORDINACIÓN ----
        'coordinacion' => [
            'label' => 'Coordinación',
            'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
            'permission' => 'is_admin_or_coordinacion',
            'color' => 'emerald',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'app.coordinacion.index',
                    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                    'active' => 'app.coordinacion.index',
                    'icon_color' => 'emerald',
                ],
                [
                    'label' => 'Pensums',
                    'route' => 'app.coordinacion.pensums',
                    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'active' => 'app.coordinacion.pensums',
                    'icon_color' => 'blue',
                ],
                [
                    'label' => 'Profesores',
                    'route' => 'app.coordinacion.profesores',
                    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                    'active' => 'app.coordinacion.profesores',
                    'icon_color' => 'green',
                ],
                [
                    'label' => 'Carga Académica',
                    'route' => 'app.coordinacion.carga-academica',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                    'active' => 'app.coordinacion.carga-academica',
                    'icon_color' => 'teal',
                ],
                [
                    'label' => 'Actividades',
                    'route' => 'app.coordinacion.activities',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                    'active' => 'app.coordinacion.activities*',
                    'icon_color' => 'cyan',
                ],
                [
                    'label' => 'Lecciones',
                    'route' => 'app.coordinacion.lessons',
                    'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                    'active' => 'app.coordinacion.lessons',
                    'icon_color' => 'purple',
                ],
                [
                    'label' => 'Recursos',
                    'route' => 'app.coordinacion.resources',
                    'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                    'active' => 'app.coordinacion.resources',
                    'icon_color' => 'amber',
                ],
                [
                    'label' => 'Horario',
                    'route' => 'app.coordinacion.timetable',
                    'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    'active' => 'app.coordinacion.timetable',
                    'icon_color' => 'sky',
                ],
                [
                    'label' => 'Suplencias',
                    'route' => 'app.coordinacion.timetable.substitutes',
                    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7zM9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'active' => 'app.coordinacion.timetable.substitutes',
                    'icon_color' => 'rose',
                ],
            ],
        ],

        // ---- COORDINACIÓN - PROFESORES (solo para coordinación) ----
        'coordinacion_profesor' => [
            'label' => 'Profesores',
            'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'permission' => 'is_profesor',
            'color' => 'emerald',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'app.coordinacion.profesores',
                    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                    'active' => 'app.coordinacion.profesores.*',
                ],
            ],
        ],

        // ---- DIRECCIÓN ----
        'director' => [
            'label' => 'Dirección',
            'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z',
            'permission' => 'is_director',
            'color' => 'sky',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'app.director.index',
                    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                    'active' => 'app.director.index',
                    'icon_color' => 'sky',
                ],
                [
                    'label' => 'Pensums',
                    'route' => 'app.director.pensums',
                    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'active' => 'app.director.pensums',
                    'icon_color' => 'blue',
                ],
                [
                    'label' => 'Carga Académica',
                    'route' => 'app.director.carga-academica',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                    'active' => 'app.director.carga-academica',
                    'icon_color' => 'teal',
                ],
                [
                    'label' => 'Actividades',
                    'route' => 'app.director.activities',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                    'active' => 'app.director.activities*',
                    'icon_color' => 'cyan',
                ],
                [
                    'label' => 'Lecciones',
                    'route' => 'app.director.lessons',
                    'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                    'active' => 'app.director.lessons',
                    'icon_color' => 'purple',
                ],
                [
                    'label' => 'Recursos',
                    'route' => 'app.director.resources',
                    'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                    'active' => 'app.director.resources',
                    'icon_color' => 'amber',
                ],
                [
                    'label' => 'Profesores',
                    'route' => 'app.director.profesores',
                    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                    'active' => 'app.director.profesores',
                    'icon_color' => 'indigo',
                ],
                [
                    'label' => 'Contenido LMS',
                    'route' => 'app.planning.lms.monitor',
                    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'active' => 'app.planning.lms.*',
                    'icon_color' => 'teal',
                    'badge' => 'planning.lms.lesson-pending-count',
                ],
            ],
        ],

        // ---- PLANIFICACIÓN (mega-menu 3 columnas) ----
        'planning' => [
            'label' => 'Planificación',
            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            'permission' => 'is_planner_or_admin_or_diagnostic',
            'color' => 'emerald',
            'mega_menu' => true,
            'columns' => [
                'evaluacion' => [
                    'title' => 'Evaluación',
                    'items' => [
                        [
                            'label' => 'Indicadores',
                            'route' => 'app.planning.indicators.index',
                            'icon' => 'M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
                            'active' => 'app.planning.indicators.*',
                            'icon_color' => 'cyan',
                        ],
                        [
                            'label' => 'Actividades',
                            'route' => 'app.planning.activities.index',
                            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                            'active' => 'app.planning.activities.*',
                            'icon_color' => 'cyan',
                        ],
                        [
                            'label' => 'Carga Académica',
                            'route' => 'app.planning.pevaluacions.index',
                            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                            'active' => 'app.planning.pevaluacions.*',
                            'icon_color' => 'teal',
                        ],
                        [
                            'label' => 'Lapsos Académicos',
                            'route' => 'app.planning.lapsos.index',
                            'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                            'active' => 'app.planning.lapsos.*',
                            'icon_color' => 'indigo',
                        ],
                    ],
                ],
                'estructura' => [
                    'title' => 'Estructura Académica',
                    'items' => [
                        [
                            'label' => 'Programas Educativos',
                            'route' => 'app.planning.peducativos.index',
                            'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                            'active' => 'app.planning.peducativos.*',
                            'icon_color' => 'orange',
                        ],
                        [
                            'label' => 'Planes de Estudio',
                            'route' => 'app.planning.pestudios.index',
                            'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                            'active' => 'app.planning.pestudios.*',
                            'icon_color' => 'purple',
                        ],
                        [
                            'label' => 'Áreas de Conocimiento',
                            'route' => 'app.planning.area-conocimientos.index',
                            'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
                            'active' => 'app.planning.area-conocimientos.*',
                            'icon_color' => 'yellow',
                        ],
                        [
                            'label' => 'Asignaturas',
                            'route' => 'app.planning.asignaturas.index',
                            'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4',
                            'active' => 'app.planning.asignaturas.*',
                            'icon_color' => 'pink',
                        ],
                        [
                            'label' => 'Grados',
                            'route' => 'app.planning.grados.index',
                            'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                            'active' => 'app.planning.grados.*',
                            'icon_color' => 'amber',
                        ],
                        [
                            'label' => 'Secciones',
                            'route' => 'app.planning.secciones.index',
                            'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                            'active' => 'app.planning.secciones.*',
                            'icon_color' => 'lime',
                        ],
                        [
                            'label' => 'Inscripciones',
                            'route' => 'app.planning.inscripcions.index',
                            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                            'active' => 'app.planning.inscripcions.*',
                            'icon_color' => 'blue',
                        ],
                        [
                            'label' => 'Pensums',
                            'route' => 'app.planning.pensums.index',
                            'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                            'active' => 'app.planning.pensums.*',
                            'icon_color' => 'rose',
                        ],
                    ],
                ],
                'herramientas' => [
                    'title' => 'Herramientas',
                    'items' => [
                        [
                            'label' => 'Diagnóstico',
                            'route' => 'app.planning.diagnostico.index',
                            'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                            'active' => 'app.planning.diagnostico.* && !app.planning.diagnostico.referents.*',
                            'icon_color' => 'sky',
                        ],
                        [
                            'label' => 'Referentes',
                            'route' => 'app.planning.diagnostico.referents.index',
                            'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                            'active' => 'app.planning.diagnostico.referents.*',
                            'icon_color' => 'fuchsia',
                        ],
                        [
                            'label' => 'Profesores',
                            'route' => 'app.planning.profesors.index',
                            'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                            'active' => 'app.planning.profesors.*',
                            'icon_color' => 'emerald',
                        ],
                        [
                            'label' => 'Competiciones',
                            'route' => 'app.planning.educational.competition.index',
                            'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                            'active' => 'app.planning.educational.*',
                            'icon_color' => 'amber',
                        ],
                        [
                            'label' => 'Contenido LMS',
                            'route' => 'app.planning.lms.monitor',
                            'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                            'active' => 'app.planning.lms.*',
                            'icon_color' => 'teal',
                            'badge' => 'planning.lms.lesson-pending-count',
                        ],
                        [
                            'label' => 'Diagramas de Flujo',
                            'route' => 'app.planning.flow.index',
                            'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
                            'active' => 'app.planning.flow.* || app.planning.diagram.*',
                            'icon_color' => 'violet',
                        ],
                        [
                            'label' => 'Horario',
                            'route' => 'app.planning.timetable',
                            'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                            'active' => 'app.planning.timetable',
                            'icon_color' => 'sky',
                        ],
                        [
                            'label' => 'Suplencias',
                            'route' => 'app.planning.timetable.substitutes',
                            'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7zM9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                            'active' => 'app.planning.timetable.substitutes',
                            'icon_color' => 'rose',
                        ],
                    ],
                ],
            ],
        ],

        // ---- PROFESOR ----
        'profesor' => [
            'label' => 'Profesor',
            'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'permission' => 'is_profesor',
            'color' => 'emerald',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'app.profesors.home',
                    'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                    'active' => 'app.profesors.home',
                ],
                [
                    'label' => 'Actividades',
                    'route' => 'app.profesors.activities.index',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                    'active' => 'app.profesors.activities.*',
                ],
                [
                    'label' => 'Diagnósticos',
                    'route' => 'app.profesors.diagnostics.index',
                    'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                    'active' => 'app.profesors.diagnostics.*',
                ],
                [
                    'label' => 'Competencias',
                    'route' => 'app.profesors.competitions.index',
                    'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                    'active' => 'app.profesors.competitions.*',
                ],
                [
                    'label' => 'Contenido LMS',
                    'route' => 'app.profesors.lms.lesson.wizard',
                    'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                    'active' => 'app.profesors.lms.*',
                ],
                [
                    'label' => 'Comentarios',
                    'route' => 'app.profesors.lms.comments',
                    'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z',
                    'active' => 'app.profesors.lms.comments*',
                    'badge' => 'profesor.lms.pending-comment-count',
                ],
                [
                    'label' => 'Mi Bitácora',
                    'route' => 'app.profesors.binnacle.mi-bitcora',
                    'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    'active' => 'app.profesors.binnacle.mi-bitcora',
                ],
                [
                    'label' => 'Mi Horario',
                    'route' => 'app.profesors.timetable',
                    'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    'active' => 'app.profesors.timetable',
                ],
                [
                    'label' => 'Mis Suplencias',
                    'route' => 'app.profesors.timetable.substitutes',
                    'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    'active' => 'app.profesors.timetable.substitutes',
                ],
            ],
        ],

        // ---- ESTUDIANTE (layout propio, no usa role-navbar) ----
        'student' => [
            'label' => 'Estudiante',
            'permission' => 'is_student',
            'items' => [
                ['label' => 'Inicio', 'route' => 'student.lms.home', 'active' => 'student.lms.home'],
                ['label' => 'Perfil', 'route' => 'student.lms.profile', 'active' => 'student.lms.profile'],
                ['label' => 'Académica', 'route' => 'student.lms.academic', 'active' => 'student.lms.academic'],
                ['label' => 'Lecciones', 'route' => 'student.lms.lessons', 'active' => 'student.lms.lessons'],
                ['label' => 'Horario', 'route' => 'student.lms.timetable', 'active' => 'student.lms.timetable'],
                ['label' => 'Recursos', 'route' => 'student.lms.resources', 'active' => 'student.lms.resources'],
                ['label' => 'Diagnóstico', 'route' => '#', 'disabled' => true],
                ['label' => 'Competiciones', 'route' => '#', 'disabled' => true],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Composición de menús por layout/rol
    |--------------------------------------------------------------------------
    | Cada layout define qué grupos muestra y en qué orden.
    | 'mobile_only' = grupos que solo aparecen en mobile (raramente usado)
    */
    'layouts' => [
        'admin' => [
            'groups' => ['admin', 'coordinacion', 'planning', 'profesor', 'director'],
        ],
        'coordinacion' => [
            'groups' => ['coordinacion', 'planning', 'coordinacion_profesor', 'admin', 'director'],
        ],
        'director' => [
            'groups' => ['director', 'coordinacion', 'admin', 'planning', 'profesor'],
        ],
        'profesor' => [
            'groups' => ['profesor', 'coordinacion', 'admin', 'planning', 'director'],
        ],
        'planning' => [
            'groups' => ['planning', 'profesor', 'coordinacion', 'admin', 'director'],
        ],
        'student' => [
            'groups' => ['student'],
        ],
    ],
];