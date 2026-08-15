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
    /*
    |--------------------------------------------------------------------------
    | Retención por categoría (meses) — Spec §12 / mejora propuesta #9
    |--------------------------------------------------------------------------
    | El comando binnacle:archive (diario 03:00) mueve a binnacle_entries_archive
    | toda fila cuya categoría supere estos meses. La cifra es la política
    | institucional; el archivado es el ÚNICO proceso que puede borrar filas
    | (trigger ADR-004 con @binnacle_archive_process = 1).
    |
    | Racional de los valores (revisar con el equipo legal antes de Fase 3):
    |   security 24 / error 24 — eventos críticos: ventana mínima de 2 años
    |     exigida para material de auditoría de seguridad y resolución de
    |     incidentes (normas LOPDP/SIPINNA y buenas prácticas de compliance).
    |   authentication 12 / user_action 12 — actividad de usuario: 1 año,
    |     suficiente para conciliación de accesos y reclamos de representantes.
    |   system 6 — rutinas internas (backups, scheduled tasks): 6 meses.
    |   debug 1 — solo entorno de desarrollo; no debería haber filas en prod.
    | Ajustar aquí es cambiar la política; el archivo queda igualmente auditado.
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
    | Ancla externa del hash-chain — Spec §8.3 / mejora propuesta #6
    |--------------------------------------------------------------------------
    | Ruta del archivo de ancla (append-only, fuera de la BD y del control del
    | DBA). El comando binnacle:anchor (diario 04:00) añade una línea por cada
    | entrada critical/alert anclada: `timestamp|entry_id|event_type|entry_hash`.
    |
    | En Linux se recomienda marcar el archivo como append-only:
    |   sudo chattr +a storage/logs/binnacle-anchor.log
    | (el comando crea la ruta si no existe y respeta el bit si ya está marcado).
    */
    'anchor_path' => env('BINNACLE_ANCHOR_PATH', storage_path('logs/binnacle-anchor.log')),

    /*
    |--------------------------------------------------------------------------
    | Umbral de particionado por fecha — Spec §9 / mejora propuesta #8
    |--------------------------------------------------------------------------
    | El comando binnacle:check-growth (semanal) proyecta el crecimiento de
    | binnacle_entries a N meses y recomienda particionado cuando la proyección
    | supera partition_threshold. Referencia: el benchmark real a 50k filas dio
    | filtros <15ms; el umbral por defecto (1M) deja margen amplio de seguridad.
    */
    'partition_threshold' => env('BINNACLE_PARTITION_THRESHOLD', 1_000_000),
    'partition_lookahead_months' => env('BINNACLE_PARTITION_LOOKAHEAD_MONTHS', 12),

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
