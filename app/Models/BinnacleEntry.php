<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinnacleEntry extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'binnacle_entries';

    protected $guarded = ['*'];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Defensa en profundidad adicional a los triggers de BD (ADR-004):
     * una fila de bitácora nunca se actualiza ni elimina por Eloquent.
     * El job de archivado (Fase 3/4) usa DB::table() con la variable de
     * sesión @binnacle_archive_process, que no pasa por estos eventos.
     */
    protected static function booted(): void
    {
        static::updating(function () {
            throw new \RuntimeException('binnacle_entries es de solo escritura (INSERT). No se permite UPDATE.');
        });

        static::deleting(function () {
            throw new \RuntimeException('binnacle_entries es de solo escritura. DELETE solo permitido vía proceso de archivado.');
        });
    }
}
