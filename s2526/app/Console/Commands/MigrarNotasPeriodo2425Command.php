<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrarNotasPeriodo2425Command extends Command
{
    // php artisan migrar:notas-2425
    // php artisan migrar:notas-2425 --verificar
    // php artisan migrar:notas-2425 --chequear
    // php artisan migrar:notas-2425 --solo=historico
    // php artisan migrar:notas-2425 --solo=hnotas
    // php artisan migrar:notas-2425 --forzar --solo=hnotas
    // php artisan migrar:notas-2425 --deleted_at=true
    // php artisan migrar:notas-2425 --solo=historico --deleted_at=true --verificar

    protected $signature = 'migrar:notas-2425
                            {--verificar}
                            {--chequear}
                            {--forzar}
                            {--solo=}
                            {--deleted_at=}';

    protected $description = 'Migración historico_notas y hnotas de 24-25 a 25-26';

    protected $source = 's2425';
    protected $target = 'mysql';

    public function handle()
    {
        $verificar       = $this->option('verificar');
        $forzar          = $this->option('forzar');
        $solo            = $this->option('solo');
        $syncDeletedAt   = filter_var($this->option('deleted_at'), FILTER_VALIDATE_BOOLEAN);

        if ($this->option('chequear')) {
            $this->checkAllMigrated();
            return 0;
        }

        if (! $forzar && ! $verificar) {
            if (! $this->confirm('¿Seguro que deseas ejecutar la migración?')) {
                return 0;
            }
        }

        if ($syncDeletedAt) {
            $this->info('⚙️ Modo sincronización de deleted_at activado.');
        }

        $this->info('Iniciando proceso...');

        DB::connection($this->target)->beginTransaction();

        try {
            $this->syncCatalogs();

            if (! $solo || $solo === 'historico') {
                $mapHistorico = $this->migrarHistorico($verificar, $syncDeletedAt);
            } else {
                $mapHistorico = [];
            }

            if (! $solo || $solo === 'hnotas') {
                $this->migrarHNotas($mapHistorico, $verificar, $syncDeletedAt);
            }

            if ($verificar) {
                DB::connection($this->target)->rollBack();
                $this->warn('Modo verificación: rollback ejecutado.');
            } else {
                DB::connection($this->target)->commit();
                $this->info('Migración completada.');
            }

        } catch (\Throwable $e) {
            DB::connection($this->target)->rollBack();
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * MIGRAR historico_notas
     */
    protected function migrarHistorico($verificar, $syncDeletedAt = false)
    {
        $this->info('Migrando historico_notas...');

        // Se remueve whereNull('deleted_at') para procesar TODOS los registros (incluidos soft-deleted)
        $sourceData = DB::connection($this->source)
            ->table('historico_notas')
            ->get();

        $map = [];
        $inserted = 0;
        $updated  = 0;

        foreach ($sourceData as $row) {
            $existe = DB::connection($this->target)
                ->table('historico_notas')
                ->where('estudiant_id', $row->estudiant_id)
                ->where('pestudio_id', $row->pestudio_id)
                ->where('fecha_expedicion', $row->fecha_expedicion)
                ->first();

            if ($existe) {
                $map[$row->id] = $existe->id;

                // Si se activó --deleted_at=true, actualizar el campo en destino
                if ($syncDeletedAt && ! $verificar) {
                    $sourceDeletedAt = $row->deleted_at ?? null;
                    $targetDeletedAt = $existe->deleted_at ?? null;

                    if ($sourceDeletedAt !== $targetDeletedAt) {
                        DB::connection($this->target)
                            ->table('historico_notas')
                            ->where('id', $existe->id)
                            ->update(['deleted_at' => $sourceDeletedAt]);
                        $updated++;
                    }
                }
                continue;
            }

            if (! $verificar) {
                $data = [
                    'pestudio_id'      => $row->pestudio_id,
                    'estudiant_id'     => $row->estudiant_id,
                    'description'      => $row->description,
                    'observations'     => $row->observations,
                    'fecha_expedicion' => $row->fecha_expedicion,
                    'created_at'       => $row->created_at ?? now(),
                    'updated_at'       => $row->updated_at ?? now(),
                ];

                // Copiar deleted_at en inserciones nuevas si existe
                if (property_exists($row, 'deleted_at')) {
                    $data['deleted_at'] = $row->deleted_at;
                }

                $newId = DB::connection($this->target)
                    ->table('historico_notas')
                    ->insertGetId($data);

                $map[$row->id] = $newId;
                $inserted++;
            }
        }

        $this->info("historico_notas: {$inserted} insertados, {$updated} actualizados (deleted_at).");
        return $map;
    }

    /**
     * MIGRAR hnotas
     */
    protected function migrarHNotas($mapHistorico, $verificar, $syncDeletedAt = false)
    {
        $this->info('Migrando hnotas...');

        $sourceData = DB::connection($this->source)
            ->table('hnotas')
            ->get();

        $count    = 0;
        $inserted = 0;
        $updated  = 0;

        foreach ($sourceData as $row) {
            $historicoDestinoId = $row->historico_nota_id
                ? ($mapHistorico[$row->historico_nota_id] ?? null)
                : null;

            $existe = DB::connection($this->target)
                ->table('hnotas')
                ->where('estudiant_id', $row->estudiant_id)
                ->where('pensum_id', $row->pensum_id)
                ->where('fecha', $row->fecha)
                ->where('valor', $row->valor)
                ->first();

            if ($existe) {
                if ($syncDeletedAt && ! $verificar) {
                    $sourceDeletedAt = $row->deleted_at ?? null;
                    $targetDeletedAt = $existe->deleted_at ?? null;

                    if ($sourceDeletedAt !== $targetDeletedAt) {
                        DB::connection($this->target)
                            ->table('hnotas')
                            ->where('id', $existe->id)
                            ->update(['deleted_at' => $sourceDeletedAt]);
                        $updated++;
                    }
                }
                continue;
            }

            if (! $verificar) {
                $data = [
                    'pensum_id'         => $row->pensum_id,
                    'grupo_estable_id'  => $row->grupo_estable_id,
                    'tevaluacion_id'    => $row->tevaluacion_id,
                    'estudiant_id'      => $row->estudiant_id,
                    'historico_nota_id' => $historicoDestinoId,
                    'institucion_id'    => $row->institucion_id,
                    'valor'             => $row->valor,
                    'literal'           => $row->literal,
                    'tipo'              => $row->tipo,
                    'fecha'             => $row->fecha,
                    'description'       => $row->description,
                    'observations'      => $row->observations,
                    'user_id'           => $row->user_id,
                    'created_at'        => $row->created_at ?? now(),
                    'updated_at'        => $row->updated_at ?? now(),
                ];

                if (property_exists($row, 'deleted_at')) {
                    $data['deleted_at'] = $row->deleted_at;
                }

                DB::connection($this->target)
                    ->table('hnotas')
                    ->insert($data);

                $inserted++;
            }

            $count++;
        }

        $this->info("hnotas: {$inserted} insertadas, {$updated} actualizadas (deleted_at).");
    }

    /**
     * Sincronizar catálogos necesarios
     */
    protected function syncCatalogs()
    {
        $this->syncTable('oinstitucions');
        $this->syncTable('grupo_estables');
    }

    /**
     * Sincronizar tabla genérica
     */
    protected function syncTable($table)
    {
        $this->info("Sincronizando tabla: $table...");

        $sourceData = DB::connection($this->source)->table($table)->get();
        $targetIds  = DB::connection($this->target)->table($table)->pluck('id')->toArray();

        $count = 0;
        foreach ($sourceData as $row) {
            if (! in_array($row->id, $targetIds)) {
                $data = (array) $row;
                
                // Asegurar timestamps si la tabla los requiere
                if (array_key_exists('created_at', $data) && empty($data['created_at'])) {
                    $data['created_at'] = now();
                }
                if (array_key_exists('updated_at', $data) && empty($data['updated_at'])) {
                    $data['updated_at'] = now();
                }

                DB::connection($this->target)->table($table)->insert($data);
                $count++;
            }
        }

        if ($count > 0) {
            $this->info("Registros sincronizados en $table: $count");
        }
    }

    /**
     * Verificar que todas las notas en source están en target
     */
    protected function checkAllMigrated()
    {
        $this->info('Validando integridad de la migración...');

        // 1. Validar Historico
        $sourceHistorico = DB::connection($this->source)
            ->table('historico_notas')
            ->count();

        $missingHistorico = 0;
        $mismatchDeletedAt = 0;

        $this->info("Chequeando $sourceHistorico historico_notas...");

        DB::connection($this->source)
            ->table('historico_notas')
            ->orderBy('id')
            ->chunk(500, function ($rows) use (&$missingHistorico, &$mismatchDeletedAt) {
                foreach ($rows as $row) {
                    $existe = DB::connection($this->target)
                        ->table('historico_notas')
                        ->where('estudiant_id', $row->estudiant_id)
                        ->where('pestudio_id', $row->pestudio_id)
                        ->where('fecha_expedicion', $row->fecha_expedicion)
                        ->first();

                    if (! $existe) {
                        $missingHistorico++;
                    } elseif (($row->deleted_at ?? null) !== ($existe->deleted_at ?? null)) {
                        $mismatchDeletedAt++;
                    }
                }
            });

        // 2. Validar HNotas
        $sourceHNotas = DB::connection($this->source)
            ->table('hnotas')
            ->count();

        $missingHNotas = 0;
        $mismatchDeletedAtH = 0;

        $this->info("Chequeando $sourceHNotas hnotas...");

        DB::connection($this->source)
            ->table('hnotas')
            ->orderBy('id')
            ->chunk(500, function ($rows) use (&$missingHNotas, &$mismatchDeletedAtH) {
                foreach ($rows as $row) {
                    $existe = DB::connection($this->target)
                        ->table('hnotas')
                        ->where('estudiant_id', $row->estudiant_id)
                        ->where('pensum_id', $row->pensum_id)
                        ->where('fecha', $row->fecha)
                        ->where('valor', $row->valor)
                        ->first();

                    if (! $existe) {
                        $missingHNotas++;
                    } elseif (($row->deleted_at ?? null) !== ($existe->deleted_at ?? null)) {
                        $mismatchDeletedAtH++;
                    }
                }
            });

        if ($missingHistorico === 0 && $missingHNotas === 0) {
            $this->info("✅ TODO CORRECTO: Registros encontrados en el destino.");
            if ($mismatchDeletedAt === 0 && $mismatchDeletedAtH === 0) {
                $this->info("✅ deleted_at coincidente en todos los registros.");
            } else {
                $this->warn("⚠️  deleted_at diferente en:");
                $this->warn("   - historico_notas: $mismatchDeletedAt registros");
                $this->warn("   - hnotas: $mismatchDeletedAtH registros");
            }
        } else {
            $this->error("❌ ERROR: Faltan registros por migrar.");
            $this->warn("- Historico faltantes: $missingHistorico de $sourceHistorico");
            $this->warn("- HNotas faltantes: $missingHNotas de $sourceHNotas");
        }
    }
}