<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AnalyzeTeachingWords extends Command
{

    /*
     # Todos los registros
        php artisan activities:analyze-teaching-words

        # Solo un plan de estudio
        php artisan activities:analyze-teaching-words --pestudio=1

        # Solo un lapso
        php artisan activities:analyze-teaching-words --lapso=3

        # Combinados (AND)
        php artisan activities:analyze-teaching-words --pestudio=1 --lapso=3
    */
    protected $signature = 'activities:analyze-teaching-words
                            {--pestudio= : Filtrar por pestudio_id}
                            {--lapso=    : Filtrar por lapso_id}';

    protected $description = 'Calcula el promedio de palabras (>3 letras) en el campo teaching de activities';

    public function handle(): int
    {
        $pestudioId = $this->option('pestudio');
        $lapsoId    = $this->option('lapso');

        // ── Validación de opciones ──────────────────────────────────────
        if ($pestudioId !== null && ! ctype_digit((string) $pestudioId)) {
            $this->error('  --pestudio debe ser un entero positivo.');
            return self::FAILURE;
        }
        if ($lapsoId !== null && ! ctype_digit((string) $lapsoId)) {
            $this->error('  --lapso debe ser un entero positivo.');
            return self::FAILURE;
        }

        // ── Cabecera informativa ────────────────────────────────────────
        $this->info('');
        $this->info('  Analizando campo <teaching> — tabla activities');

        $filtros = [];
        if ($pestudioId) $filtros[] = "pestudio_id = {$pestudioId}";
        if ($lapsoId)    $filtros[] = "lapso_id    = {$lapsoId}";

        if ($filtros) {
            $this->line('  <fg=yellow>Filtros activos: ' . implode('  |  ', $filtros) . '</>');
        } else {
            $this->line('  <fg=yellow>Sin filtros — se analizan todos los registros.</>');
        }
        $this->info('');

        // ── Construcción dinámica del WHERE ─────────────────────────────
        // Los bindings se pasan como array a DB::selectOne para evitar
        // interpolación directa (aunque los valores ya se validaron arriba
        // como dígitos, es buena práctica usar prepared statements).
        $whereClauses = [
            "a.teaching IS NOT NULL",
            "TRIM(a.teaching) != ''",
        ];
        $bindings = [];

        if ($pestudioId) {
            $whereClauses[] = "ps.pestudio_id = ?";
            $bindings[]     = (int) $pestudioId;
        }
        if ($lapsoId) {
            $whereClauses[] = "pe.lapso_id = ?";
            $bindings[]     = (int) $lapsoId;
        }

        $where = implode("\n                  AND  ", $whereClauses);

        // ── Consulta SQL ────────────────────────────────────────────────
        // El JOIN activities → pevaluacions → pensums es necesario para
        // poder filtrar por pestudio_id y lapso_id.
        // Si no hay filtros el JOIN sigue presente pero no penaliza mucho
        // porque MariaDB lo optimiza al no existir condición sobre él.
        $sql = "
            WITH RECURSIVE

            nums AS (
                SELECT 1 AS n
                UNION ALL
                SELECT n + 1 FROM nums WHERE n < 1400
            ),

            -- Universo de activities que pasan el filtro.
            -- Se materializa aquí para que el CROSS JOIN con nums
            -- solo opere sobre las filas relevantes.
            activities_filtradas AS (
                SELECT
                    a.id,
                    a.teaching
                FROM       activities    a
                INNER JOIN pevaluacions  pe ON pe.id        = a.pevaluacion_id
                INNER JOIN pensums       ps ON ps.id        = pe.pensum_id
                WHERE  {$where}
            ),

            palabras_raw AS (
                SELECT
                    af.id,
                    LENGTH(TRIM(af.teaching))
                        - LENGTH(REPLACE(TRIM(af.teaching), ' ', ''))
                        + 1                                              AS total_palabras,
                    TRIM(
                        SUBSTRING_INDEX(
                            SUBSTRING_INDEX(
                                REGEXP_REPLACE(TRIM(af.teaching), '[[:space:]]+', ' '),
                                ' ', nums.n
                            ),
                            ' ', -1
                        )
                    )                                                    AS palabra
                FROM       activities_filtradas af
                CROSS JOIN nums
                WHERE  nums.n <= (
                           LENGTH(TRIM(af.teaching))
                           - LENGTH(REPLACE(TRIM(af.teaching), ' ', ''))
                           + 1
                       )
            ),

            palabras_limpias AS (
                SELECT
                    id,
                    total_palabras,
                    REGEXP_REPLACE(
                        LOWER(palabra),
                        '[^a-záéíóúüñàèìòùâêîôûäëïöü]',
                        ''
                    ) AS palabra_limpia
                FROM  palabras_raw
                WHERE palabra != ''
            ),

            conteo AS (
                SELECT
                    id,
                    total_palabras,
                    SUM(
                        CASE
                            WHEN CHAR_LENGTH(palabra_limpia) > 3
                            THEN 1
                            ELSE 0
                        END
                    ) AS palabras_gt3
                FROM  palabras_limpias
                GROUP BY id, total_palabras
            )

            SELECT
                ROUND(AVG(palabras_gt3),           4) AS promedio_palabras_gt3,
                ROUND(AVG(total_palabras),         4) AS promedio_total_palabras,
                COUNT(*)                              AS actividades_analizadas,
                SUM(palabras_gt3)                     AS total_global_palabras_gt3,
                ROUND(
                    AVG(palabras_gt3) / NULLIF(AVG(total_palabras), 0) * 100
                , 2)                                  AS pct_promedio_cobertura
            FROM conteo
        ";

        // ── Ejecución ───────────────────────────────────────────────────
        $this->line('  Ejecutando consulta...');
        $this->info('');

        try {
            $start   = microtime(true);
            //DB::statement('SET SESSION max_execution_time = 120000');
            $result  = DB::selectOne($sql, $bindings);
            $elapsed = round((microtime(true) - $start) * 1000, 1);
        } catch (\Exception $e) {
            $this->error('  Error al ejecutar la consulta:');
            $this->error('  ' . $e->getMessage());
            return self::FAILURE;
        }

        if (! $result || $result->actividades_analizadas === 0) {
            $this->warn('  No se encontraron registros con el criterio indicado.');
            return self::SUCCESS;
        }

        // ── Salida ──────────────────────────────────────────────────────
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Promedio palabras >3 letras por actividad', $result->promedio_palabras_gt3],
                ['Promedio total de palabras por actividad',  $result->promedio_total_palabras],
                ['Actividades analizadas',                    number_format($result->actividades_analizadas)],
                ['Total global palabras >3 letras',           number_format($result->total_global_palabras_gt3)],
                ['Cobertura léxica promedio (%)',             $result->pct_promedio_cobertura . ' %'],
            ]
        );

        $this->info('');
        $this->line("  <fg=gray>Tiempo de ejecución: {$elapsed} ms</>");
        $this->info('');

        return self::SUCCESS;
    }
}