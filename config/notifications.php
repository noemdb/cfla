<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Retención de notificaciones (ítem 14)
    |--------------------------------------------------------------------------
    | La tabla `notifications` solo crece: cada actividad registrada, cada
    | cambio de horario y cada recordatorio deja una fila para siempre, y la
    | campana y el listado administrativo paginan sobre ella. `php artisan
    | notifications:prune` borra las ya leídas más antiguas que este número de
    | días (0 = no borrar nada por este lado).
    |
    | Las NO leídas no se tocan salvo que se pida explícitamente con
    | `--purge-unread-days`: son la bandeja de trabajo del usuario y, si se
    | perdieran, el aviso se habría perdido con ellas.
    */
    'retention_days' => (int) env('NOTIFICATIONS_RETENTION_DAYS', 180),

    /*
    |--------------------------------------------------------------------------
    | Marcar como leídas al visitar el destino (ítem 12)
    |--------------------------------------------------------------------------
    | Rutas cuyo listado ES el destino de uno o más tipos de notificación. Al
    | entrar en la ruta, los avisos de esos tipos se dan por vistos (el badge
    | baja sin clics manuales): si el usuario abrió la pantalla donde se
    | resolverían, ya los vio.
    |
    | Se resuelve en un middleware (`MarkNotificationsAsRead`) en vez de en el
    | mount() de cada componente: los listados de Livewire se vuelven a render
    | en cada actualización y ahí no debe volver a escribirse nada; el
    | middleware solo actúa en la petición GET de la ruta.
    |
    | Clave: nombre de la ruta. Valor: tipos (`data->type`) a marcar.
    */
    'auto_read' => [
        // Actividades: el listado es donde se aprueban/gestionan. Aquí caen
        // también los recordatorios de aprobaciones y de áreas inactivas.
        'app.planning.activities.index' => [
            'activity_created',
            'pending_approval_reminder',
            'stale_activities_reminder',
        ],
        'app.coordinacion.activities' => ['activity_created'],
        'app.leadership.activities' => ['activity_created'],
        'app.director.activities' => ['activity_created'],
        'app.profesors.activities.index' => ['activity_approved', 'activity_created'],

        // Observaciones de coordinación sobre pevaluaciones.
        'app.planning.pevaluacions.index' => [
            'pevaluacion_observation_updated',
            'pevaluacion_observation_registered',
        ],

        // Programas educativos.
        'app.planning.peducativos.index' => [
            'peducativo_created',
            'peducativo_updated',
            'peducativo_deleted',
        ],

        // Diagnóstico.
        'app.planning.diagnostico.index' => [
            'diag_question_created',
            'diag_question_updated',
            'diag_question_deleted',
        ],
        'app.leadership.diagnosticos' => [
            'diag_question_created',
            'diag_question_updated',
            'diag_question_deleted',
        ],

        // Horarios.
        'app.planning.timetable' => ['timetable_calendar_activated', 'timetable_changed'],
        'app.coordinacion.timetable' => ['timetable_calendar_activated', 'timetable_changed'],
        'app.profesors.timetable.substitutes' => ['substitute_assigned'],

        // Lecciones programadas y ciclo de publicación: el aviso va a la
        // previsualización (planificación/jefatura), al editor (profesorado) o
        // al listado de lecciones, así que se da por visto en todos esos
        // destinos.
        'app.planning.lms.monitor' => [
            'lesson_scheduled',
            'scheduled_lessons_reminder',
            'lms_activity_published',
            'lms_publication_deleted',
        ],
        'app.leadership.lessons' => ['lesson_scheduled', 'lms_activity_published', 'lms_publication_deleted'],
        'app.coordinacion.lessons' => ['lesson_scheduled', 'lms_activity_published', 'lms_publication_deleted'],
        'app.director.lessons' => ['lesson_scheduled', 'lms_activity_published', 'lms_publication_deleted'],
        'app.profesors.lms.editor' => ['lms_activity_published', 'lms_publication_deleted'],
    ],

];
