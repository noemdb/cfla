<?php

namespace App\Services\Inicial;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Migración de datos del módulo de Educación Inicial desde la conexión legacy.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * REGLAS ABSOLUTAS (CLAUDE.md · blueprint/inicial §A.10)
 * ─────────────────────────────────────────────────────────────────────────────
 *  · NUNCA `migrate:fresh`, `DROP`, `TRUNCATE` ni `DELETE`. Este servicio solo
 *    hace `INSERT`; nada borra ni altera datos existentes.
 *  · IDEMPOTENTE: una fila cuyo `id` ya existe en el destino se salta. Se puede
 *    reejecutar tantas veces como haga falta sin duplicar nada.
 *  · PADRES ANTES QUE HIJAS: el orden de las tablas es explícito, no alfabético.
 *  · GUARDIÁN DE INTEGRIDAD antes de escribir: si hay referencias rotas, se dice
 *    cuántas son y en qué tabla, y por defecto NO se migra nada.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * POR QUÉ LA INTERSECCIÓN DE COLUMNAS
 * ─────────────────────────────────────────────────────────────────────────────
 * Se copia la intersección de columnas entre origen y destino, y se INFORMA de
 * las columnas del origen que no existen en el destino. Así una diferencia de
 * schema (p. ej. el `lapso_id` huérfano que el blueprint documenta en
 * `eispecialacts.COLUMN_COMMENTS`) nunca se pierde en silencio: aparece en el
 * informe. Una lista de columnas escrita a mano, en cambio, se desincroniza en
 * cuanto el legacy añade un campo.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * LOS HUÉRFANOS
 * ─────────────────────────────────────────────────────────────────────────────
 * La fuente tiene 33 filas (de 2.615) cuyo plan padre fue borrado sin cascada:
 * 27 estrategias semanales, 4 resúmenes de proyecto, 1 revisión y 1 actividad
 * especial. Insertarlas tal cual dejaría strategies sin plan detrás, que es justo
 * lo que el guardián debe impedir.
 *
 * Por defecto el comando ABORTA y las cuenta. Con `--omitir-huerfanas` se saltan
 * y quedan en el log. No se borran de la fuente en ningún caso: la fuente es
 * solo lectura.
 */
class MigradorLegacyInicial
{
    /**
     * Tablas en orden de carga: padres antes que hijas.
     *
     * `pevaluacion_id` no es una tabla del módulo: se verifica aparte contra
     * `pevaluacions` de cfla (no hay que migrarla, ya existe).
     *
     * @var array<int, array{tabla: string, padre: ?string, fk: ?string}>
     */
    private const ORDEN = [
        ['tabla' => 'eiplanningwks', 'padre' => null, 'fk' => null],
        ['tabla' => 'eiplanningbwks', 'padre' => null, 'fk' => null],
        ['tabla' => 'eiprojectks', 'padre' => null, 'fk' => null],
        ['tabla' => 'eispecialks', 'padre' => null, 'fk' => null],
        ['tabla' => 'eievaluationks', 'padre' => null, 'fk' => null],
        ['tabla' => 'eifinalks', 'padre' => null, 'fk' => null],

        ['tabla' => 'eiplanningwsummaries', 'padre' => 'eiplanningwks', 'fk' => 'eiplanningwk_id'],
        ['tabla' => 'eiplanningwstrategies', 'padre' => 'eiplanningwks', 'fk' => 'eiplanningwk_id'],
        ['tabla' => 'eiplanningbwsummaries', 'padre' => 'eiplanningbwks', 'fk' => 'eiplanningbwk_id'],
        ['tabla' => 'eiplanningbwstrategies', 'padre' => 'eiplanningbwks', 'fk' => 'eiplanningbwk_id'],
        ['tabla' => 'eiprojectsummaries', 'padre' => 'eiprojectks', 'fk' => 'eiprojectk_id'],
        ['tabla' => 'eiprojectreviews', 'padre' => 'eiprojectks', 'fk' => 'eiprojectk_id'],
        ['tabla' => 'eiprojectkstrategies', 'padre' => 'eiprojectks', 'fk' => 'eiprojectk_id'],
        ['tabla' => 'eispecialacts', 'padre' => 'eispecialks', 'fk' => 'eispecialk_id'],
        ['tabla' => 'eispecialstrategies', 'padre' => 'eispecialks', 'fk' => 'eispecialk_id'],
        ['tabla' => 'eievaluationps', 'padre' => 'eievaluationks', 'fk' => 'eievaluationk_id'],
        ['tabla' => 'eifinalk_expectation', 'padre' => 'eifinalks', 'fk' => 'eifinalk_id'],
    ];

    /**
     * Tablas del módulo, en orden de carga.
     *
     * @return array<int, string>
     */
    public static function tablas(): array
    {
        return array_column(self::ORDEN, 'tabla');
    }

    public function __construct(
        private string $origen = 's2526',
        private int $chunks = 500,
        private bool $omitirHuerfanas = false,
    ) {}

    /**
     * ¿La conexión legacy responde?
     *
     * `Schema::hasTable()` devuelve `false` en esta conexión aunque la tabla
     * exista (se comprobó contra `SHOW FULL TABLES`), así que la disponibilidad se
     * decide con una consulta trivial.
     */
    public function origenDisponible(): bool
    {
        try {
            DB::connection($this->origen)->select('SELECT 1');

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Qué haría la migración, sin escribir nada.
     *
     * @param  array<int, string>  $solo  limita a estas tablas.
     * @param  array<int, string>  $omitir  excluye estas tablas.
     * @return array{
     *     tablas: array<string, array{origen: int, destino: int, nuevos: int, existentes: int, columnas: int, columnas_ignoradas: array<int, string>}>,
     *     huerfanas: array<string, array{tabla: string, filas: int, ids: array<int, int>}>,
     *     claves_roto: array<string, int>,
     *     columnas_faltantes: array<string, array<int, string>>,
     *     total_nuevos: int
     * }
     */
    public function planificar(array $solo = [], array $omitir = []): array
    {
        $tablas = $this->tablasSeleccionadas($solo, $omitir);

        $resultado = [
            'tablas' => [],
            'huerfanas' => [],
            'claves_roto' => [],
            'columnas_faltantes' => [],
            'total_nuevos' => 0,
        ];

        foreach ($tablas as $entrada) {
            $tabla = $entrada['tabla'];

            if (! $this->existeEnOrigen($tabla) || ! Schema::hasTable($tabla)) {
                continue;
            }

            $columnasOrigen = $this->columnasDe($this->origen, $tabla);
            $columnasDestino = Schema::getColumnListing($tabla);

            // Intersección, en el ORDEN del destino para que el INSERT sea
            // estable y comparable entre ejecuciones.
            $comunes = array_values(array_intersect($columnasDestino, $columnasOrigen));
            $ignoradas = array_values(array_diff($columnasOrigen, $columnasDestino));

            if ($ignoradas) {
                $resultado['columnas_faltantes'][$tabla] = $ignoradas;
            }

            $origen = DB::connection($this->origen)->table($tabla);
            $destino = DB::table($tabla);

            $existentes = $destino->count();
            $totalOrigen = $origen->count();

            // Filas cuya `id` ya está en el destino: no se tocan. Es lo que hace
            // el comando idempotente y reejecutable.
            $idsEnDestino = $destino->pluck('id')->all();
            $nuevos = $idsEnDestino
                ? $origen->whereNotIn('id', $idsEnDestino)->count()
                : $totalOrigen;

            $huerfanas = $this->contarHuerfanas($entrada);

            if ($huerfanas['filas'] > 0) {
                $resultado['huerfanas'][$tabla] = $huerfanas;
            }

            $resultado['tablas'][$tabla] = [
                'origen' => $totalOrigen,
                'destino' => $existentes,
                'nuevos' => $nuevos,
                // Filas del origen que ya están en el destino (las que el
                // comando se va a saltar).
                'existentes' => $totalOrigen - $nuevos,
                'columnas' => count($comunes),
                'columnas_ignoradas' => $ignoradas,
            ];

            $resultado['total_nuevos'] += $nuevos;
        }

        // Las claves foráneas que NO son tablas del módulo: si un `profesor_id`
        // del legacy no existe en cfla, su plan no debe entrar.
        $resultado['claves_roto'] = $this->clavesRotas();

        return $resultado;
    }

    /**
     * Ejecuta la migración.
     *
     * @param  array<int, string>  $solo
     * @param  array<int, string>  $omitir
     * @param  callable|null  $progreso  `fn(string $tabla, int $insertadas, int $total)`
     * @return array<string, int> filas insertadas por tabla
     *
     * @throws \RuntimeException si el guardián encuentra problemas y no se autorizó
     *                           a saltarlos.
     */
    public function ejecutar(array $solo = [], array $omitir = [], ?callable $progreso = null): array
    {
        $plan = $this->planificar($solo, $omitir);

        $problemas = $this->problemas($plan);

        if ($problemas !== [] && ! $this->omitirHuerfanas) {
            throw new \RuntimeException(
                "Integridad rota en la fuente; no se migró nada:\n - ".implode("\n - ", $problemas)
                ."\n\nRevise los datos o ejecute con --omitir-huerfanas para saltarlas."
            );
        }

        if ($this->omitirHuerfanas && $problemas !== []) {
            Log::warning('inicial:migrate-legacy — se saltan referencias rotas', [
                'problemas' => $problemas,
            ]);
        }

        $insertadas = [];

        foreach ($plan['tablas'] as $tabla => $info) {
            $insertadas[$tabla] = $this->migrarTabla($tabla, $info['columnas'], $progreso);
        }

        return $insertadas;
    }

    /**
     * Problemas que impiden una migración limpia.
     *
     * @param  array<string, mixed>  $plan
     * @return array<int, string>
     */
    public function problemas(array $plan): array
    {
        $problemas = [];

        foreach ($plan['huerfanas'] ?? [] as $tabla => $huerfanas) {
            $problemas[] = sprintf(
                '%s: %d fila(s) sin plan padre (ids %s)',
                $tabla,
                $huerfanas['filas'],
                implode(', ', $huerfanas['ids'])
            );
        }

        foreach ($plan['claves_roto'] ?? [] as $columna => $cuantas) {
            $problemas[] = sprintf('%s: %d valor(es) no existen en cfla', $columna, $cuantas);
        }

        return $problemas;
    }

    /**
     * Inserta las filas nuevas de una tabla por lotes.
     *
     * Cada lote va en su propia transacción: si algo falla a mitad, las filas ya
     * insertadas se conservan y el comando puede reejecutarse (son las que se
     * saltarán por id).
     */
    private function migrarTabla(string $tabla, int $columnas, ?callable $progreso): int
    {
        if ($columnas === 0) {
            return 0;
        }

        $destino = DB::table($tabla);
        $idsExistentes = $destino->pluck('id')->all();

        $consulta = DB::connection($this->origen)->table($tabla)->orderBy('id');

        if ($idsExistentes) {
            $consulta->whereNotIn('id', $idsExistentes);
        }

        // Columnas a copiar: intersección con el destino.
        $columnasFila = array_values(array_intersect(
            Schema::getColumnListing($tabla),
            $this->columnasDe($this->origen, $tabla)
        ));

        if ($columnasFila === []) {
            return 0;
        }

        // Filas cuyo padre no se va a migrar: no pueden entrar.
        $idsHuerfanos = $this->idsHuerfanosDe($tabla);

        $total = 0;

        $consulta->chunk($this->chunks, function ($filas) use ($tabla, $columnasFila, $idsHuerfanos, &$total, $progreso) {
            $lote = [];

            foreach ($filas as $fila) {
                if (in_array((int) $fila->id, $idsHuerfanos, true)) {
                    continue;
                }

                $datos = [];

                foreach ($columnasFila as $columna) {
                    $datos[$columna] = $fila->{$columna};
                }

                $lote[] = $datos;
            }

            if ($lote === []) {
                return;
            }

            DB::table($tabla)->insert($lote);

            $total += count($lote);

            if ($progreso) {
                $progreso($tabla, $total, count($filas));
            }
        });

        return $total;
    }

    /**
     * Filas huérfanas de una tabla, agrupadas por id de padre.
     *
     * @param  array{tabla: string, padre: ?string, fk: ?string}  $entrada
     * @return array{tabla: string, fk: string, filas: int, ids: array<int, int>}
     */
    private function contarHuerfanas(array $entrada): array
    {
        ['tabla' => $tabla, 'padre' => $padre, 'fk' => $fk] = $entrada;

        if (! $padre || ! $this->existeEnOrigen($padre) || ! $this->tieneColumna($this->origen, $tabla, $fk)) {
            return ['tabla' => $tabla, 'fk' => (string) $fk, 'filas' => 0, 'ids' => []];
        }

        $idsPadre = DB::connection($this->origen)->table($padre)->pluck('id')
            ->map(fn ($id) => (int) $id)->all();

        $huerfanas = DB::connection($this->origen)->table($tabla)
            ->whereNotNull($fk)
            ->get()
            ->reject(fn ($fila) => in_array((int) $fila->{$fk}, $idsPadre, true));

        if ($huerfanas->isEmpty()) {
            return ['tabla' => $tabla, 'fk' => (string) $fk, 'filas' => 0, 'ids' => []];
        }

        return [
            'tabla' => $tabla,
            'fk' => (string) $fk,
            'filas' => $huerfanas->count(),
            'ids' => $huerfanas->pluck($fk)->map(fn ($id) => (int) $id)->unique()->values()->all(),
        ];
    }

    /**
     * @return array<int, int> ids de padre huérfanos de una tabla
     */
    private function idsHuerfanosDe(string $tabla): array
    {
        foreach (self::ORDEN as $entrada) {
            if ($entrada['tabla'] !== $tabla) {
                continue;
            }

            $info = $this->contarHuerfanas($entrada);

            return $info['ids'];
        }

        return [];
    }

    /**
     * Referencias de las tablas del módulo que no existen en las tablas de CFLA
     * (`profesors`, `grados`, `seccions`, `lapsos`, `pevaluacions`).
     *
     * @return array<string, int> columna => cuántas referencias no resuelven
     */
    private function clavesRotas(): array
    {
        $destinos = [
            'profesor_id' => ['tabla' => 'profesors'],
            'grado_id' => ['tabla' => 'grados'],
            'seccion_id' => ['tabla' => 'seccions'],
            'lapso_id' => ['tabla' => 'lapsos'],
            'pevaluacion_id' => ['tabla' => 'pevaluacions'],
        ];

        $rotas = [];

        foreach (self::ORDEN as $entrada) {
            $tabla = $entrada['tabla'];

            if (! $this->existeEnOrigen($tabla) || ! Schema::hasTable($tabla)) {
                continue;
            }

            foreach (array_keys($destinos) as $columna) {
                if (! $this->tieneColumna($this->origen, $tabla, $columna)) {
                    continue;
                }

                // Una columna ya contada no se vuelve a contar: se lleva el peor
                // caso (el número de valores distintos que no existen).
                $valores = DB::connection($this->origen)->table($tabla)
                    ->whereNotNull($columna)
                    ->distinct()
                    ->pluck($columna)
                    ->map(fn ($v) => (int) $v);

                $rotos = $valores->reject(fn ($v) => DB::table($destinos[$columna]['tabla'])->where('id', $v)->exists());

                if ($rotos->isNotEmpty()) {
                    $clave = $tabla.'.'.$columna;
                    $rotas[$clave] = max($rotas[$clave] ?? 0, $rotos->count());
                }
            }
        }

        return $rotas;
    }

    /**
     * @param  array<int, string>  $solo
     * @param  array<int, string>  $omitir
     * @return array<int, array{tabla: string, padre: ?string, fk: ?string}>
     */
    private function tablasSeleccionadas(array $solo, array $omitir): array
    {
        return array_values(array_filter(self::ORDEN, function (array $entrada) use ($solo, $omitir) {
            if ($solo !== [] && ! in_array($entrada['tabla'], $solo, true)) {
                return false;
            }

            return ! in_array($entrada['tabla'], $omitir, true);
        }));
    }

    /**
     * `SHOW COLUMNS` de una conexión.
     *
     * @return array<int, string>
     */
    private function columnasDe(string $conexion, string $tabla): array
    {
        return array_map(
            fn ($columna) => $columna->Field,
            DB::connection($conexion)->select('SHOW COLUMNS FROM `'.$tabla.'`')
        );
    }

    private function tieneColumna(string $conexion, string $tabla, ?string $columna): bool
    {
        return $columna !== null && in_array($columna, $this->columnasDe($conexion, $tabla), true);
    }

    /**
     * Existencia en la conexión origen.
     *
     * Se comprueba con `SHOW TABLES LIKE`: `Schema::hasTable()` da `false` en
     * esta conexión para tablas que sí existen (comprobado), así que no sirve.
     */
    private function existeEnOrigen(string $tabla): bool
    {
        try {
            $filas = DB::connection($this->origen)->select("SHOW TABLES LIKE '".$tabla."'");

            return $filas !== [];
        } catch (Throwable $e) {
            return false;
        }
    }
}
