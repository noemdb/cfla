<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cola dedicada
    |--------------------------------------------------------------------------
    | Worker requerido: artisan queue:work database --queue=binnacle
    | (ver supervisor-reverb.conf → programa cfla-binnacle-queue).
    */
    'queue' => env('BINNACLE_QUEUE', 'binnacle'),

    /*
    |--------------------------------------------------------------------------
    | Paginación por defecto del panel.
    |--------------------------------------------------------------------------
    */
    'per_page' => 50,

    /*
    |--------------------------------------------------------------------------
    | Severidades síncronas (ADR-002)
    |--------------------------------------------------------------------------
    | critical/alert se escriben en la misma request/transacción para no
    | perderse si la cola falla. El resto va a la cola dedicada.
    */
    'sync_severities' => ['critical', 'alert'],

    /*
    |--------------------------------------------------------------------------
    | Eventos síncronos (mejora propuesta #4)
    |--------------------------------------------------------------------------
    | Eventos que, aunque su severidad sea info/warning, se escriben en la misma
    | request (sin pasar por la cola) para no perderse si el worker cae.
    | Ej: user_login y access son los que la institución considera críticos.
    | queue_backlog DEBE ser síncrono: una alerta de cola caída no puede depender
    | de la propia cola para persistirse.
    */
    'sync_event_types' => ['user_login', 'access', 'queue_backlog'],

    /*
    |--------------------------------------------------------------------------
    | Umbral de backlog (mejora propuesta #1)
    |--------------------------------------------------------------------------
    | El comando binnacle:watch (programado) emite una entrada warning
    | event_type=queue_backlog y notifica al admin si la cola supera este
    | número de jobs pendientes (worker caído o congestionado).
    */
    'backlog_threshold' => env('BINNACLE_BACKLOG_THRESHOLD', 100),

    /*
    |--------------------------------------------------------------------------
    | Destinatarios de alertas (mejora propuesta #1 y #5)
    |--------------------------------------------------------------------------
    | Emails a los que se notifica (fallback de roles admin/director).
    */
    'alert_recipients' => array_filter(explode(',', env('BINNACLE_ALERT_RECIPIENTS', ''))),

    /*
    |--------------------------------------------------------------------------
    | Retención por categoría (meses) — Spec §12
    |--------------------------------------------------------------------------
    */
    'retention_months' => [
        'security' => 24,       // eventos críticos de seguridad
        'error' => 24,          // errores críticos
        'authentication' => 12, // eventos de usuario estándar
        'user_action' => 12,    // eventos de usuario estándar
        'system' => 6,          // eventos de sistema de rutina
        'debug' => 1,           // solo en desarrollo
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowlist de modelos "sensibles" para event_type=model_viewed (§9.2)
    |--------------------------------------------------------------------------
    | Sin esta restricción, el volumen de este único evento puede superar al
    | del resto de la tabla combinado. Se registra únicamente vía el helper
    | Binnacle::logView($model), que es ignorado si el modelo no está aquí.
    */
    'viewed_models' => [
        \App\Models\User::class,
        \App\Models\app\Learner\Estudiant::class,
        \App\Models\app\Learner\Representant::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Meta-auditoría (§6 / Fase 4)
    |--------------------------------------------------------------------------
    | Registra event_type=binnacle_accessed (category=security) cuando un
    | admin/director/leadership consulta el panel de bitácora.
    */
    'meta_audit' => env('BINNACLE_META_AUDIT', true),
];
