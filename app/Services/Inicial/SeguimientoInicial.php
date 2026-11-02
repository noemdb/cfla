<?php

namespace App\Services\Inicial;

use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Consultas del perfil de SEGUIMIENTO de Educación Inicial (solo lectura).
 *
 * Blueprint: blueprint/inicial · doc 11 · §7.2.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * QUÉ HACE Y QUÉ NO
 * ─────────────────────────────────────────────────────────────────────────────
 * Convierte los filtros de la UI en consultas paginadas, calcula el estado de
 * cada documento y resume los KPI. **No tiene ningún método de escritura**: no
 * crea, actualiza, elimina, clona, importa ni escribe observaciones.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * POR QUÉ NO USA `RegistroInicial::consulta()`
 * ─────────────────────────────────────────────────────────────────────────────
 * Esa consulta carga TODAS las relaciones, incluidas estrategias y resúmenes.
 * Es lo correcto para `show()`/`format()`, pero en una página de 15 filas
 * multiplicaría los modelos. Aquí el listado carga solo cabeceras y cuenta con
 * `withCount()`; el documento completo se busca en el detalle.
 */
final class SeguimientoInicial
{
    public function __construct(
        private readonly PerspectivaFiltros $filtros
    ) {}

    /**
     * Relaciones mínimas del listado (docente, nivel, sección, área, lapso).
     *
     * @var array<string, array<int, string>>
     */
    private const RELACIONES_LISTADO = [
        'eiplanningwks' => ['profesor', 'grado', 'seccion', 'pensum.asignatura'],
        'eiplanningbwks' => ['profesor', 'grado', 'seccion', 'pensum.asignatura', 'eiprojectk:id'],
        'eiprojectks' => ['profesor', 'grado', 'seccion', 'pensum.asignatura'],
        'eispecialks' => ['profesor', 'grado', 'seccion', 'pensum.asignatura'],
        'eievaluationks' => ['profesor', 'grado', 'seccion', 'lapso', 'pensum.asignatura'],
        'eifinalks' => [
            'pevaluacion.profesor',
            'pevaluacion.seccion',
            'pevaluacion.lapso',
            'pevaluacion.pensum.asignatura',
            'expectant',
        ],
    ];

    /**
     * Contadores por documento: alias => [relación, columna de contenido].
     *
     * `null` como columna cuenta todas las filas de la relación. En las
     * estrategias se cuenta solo `lunes` no vacío: el texto vive SIEMPRE en
     * `lunes` (quirk de persistencia, D3) y las filas legadas vacías no deben
     * inflar el progreso.
     *
     * @var array<string, array<string, array{0: string, 1: ?string}>>
     */
    private const CONTEO = [
        'eiplanningwks' => [
            'estrategias_count' => ['eiplanningwstrategies', 'lunes'],
            'resumenes_count' => ['eiplanningwsummaries', null],
        ],
        'eiplanningbwks' => [
            'estrategias_count' => ['eiplanningbwstrategies', 'lunes'],
            'resumenes_count' => ['eiplanningbwsummaries', null],
        ],
        'eiprojectks' => [
            'estrategias_count' => ['eiprojectkstrategies', 'lunes'],
            'resumenes_count' => ['eiprojectsummaries', null],
            'revisiones_count' => ['eiprojectreviews', null],
        ],
        'eispecialks' => [
            'estrategias_count' => ['eispecialstrategies', 'lunes'],
            'actividades_count' => ['activities', null],
        ],
        'eievaluationks' => [
            'posiciones_count' => ['eievaluationps', null],
        ],
        'eifinalks' => [
            'expectativas_count' => ['expectations', null],
        ],
    ];

    /**
     * Contadores que determinan si un documento tiene contenido, por entidad.
     *
     * El listado los obtiene de `withCount`; el modal y el detalle usan las
     * relaciones ya cargadas. Este mapa permite que `estado()` funcione en
     * ambos contextos sin volver a consultar.
     *
     * @var array<string, array<int, array{0: string, 1: string, 2: ?string}>>
     */
    private const CONTEO_CONTENIDO = [
        'eiplanningbwks' => [['estrategias_count', 'eiplanningbwstrategies', 'lunes']],
        'eiprojectks' => [['estrategias_count', 'eiprojectkstrategies', 'lunes']],
        'eispecialks' => [
            ['actividades_count', 'activities', null],
            ['estrategias_count', 'eispecialstrategies', 'lunes'],
        ],
        'eievaluationks' => [['posiciones_count', 'eievaluationps', null]],
        'eifinalks' => [['expectativas_count', 'expectations', null]],
    ];

    /** Columnas de búsqueda libre por documento. @var array<string, array<int, string>> */
    private const BUSQUEDA = [
        'eiplanningwks' => ['diagnostico', 'observacion'],
        'eiplanningbwks' => ['diagnostico', 'observacion'],
        'eiprojectks' => ['diagnostico', 'observacion'],
        'eispecialks' => ['justificacion', 'observacion'],
        'eievaluationks' => ['observaciones', 'recomendacion', 'asistencia'],
        'eifinalks' => ['title'],
    ];

    /** @return array<int, string> */
    public function pestanas(): array
    {
        return RegistroInicial::claves();
    }

    public function grados(): Collection
    {
        return $this->filtros->grados();
    }

    public function secciones(?int $gradoId): Collection
    {
        return $this->filtros->secciones($gradoId);
    }

    public function lapsos(): Collection
    {
        return $this->filtros->lapsos();
    }

    public function soportaFechas(string $entidad): bool
    {
        return $entidad !== 'eifinalks';
    }

    public function soportaLapso(string $entidad): bool
    {
        return in_array($entidad, ['eievaluationks', 'eifinalks'], true);
    }

    /**
     * Docentes del filtro: los que tienen el flag MÁS los que ya tienen
     * documentos (un flag retirado no debe ocultar el histórico a revisar).
     *
     * No se usa `PerspectivaFiltros::profesores()` porque cruza `pevaluacions`
     * y dejaría fuera a un docente con planes pero sin carga registrada.
     *
     * @return Collection<int, string>
     */
    public function profesores(): Collection
    {
        $conFlag = User::query()
            ->where('is_inicial', true)
            ->with('profesor:id,user_id,name,lastname')
            ->get()
            ->pluck('profesor')
            ->filter()
            ->mapWithKeys(fn (Profesor $profesor) => [
                $profesor->id => trim("{$profesor->lastname}, {$profesor->name}"),
            ]);

        $conDocumentos = Profesor::query()
            ->whereIn('id', $this->idsConDocumentos())
            ->get()
            ->mapWithKeys(fn (Profesor $profesor) => [
                $profesor->id => trim("{$profesor->lastname}, {$profesor->name}"),
            ]);

        // union() conserva las claves de la izquierda y añade las que falten;
        // sort() ordena por etiqueta sin perder el id.
        return $conFlag->union($conDocumentos)->sort();
    }

    /**
     * Ids de profesor con al menos un documento, en los seis catálogos.
     *
     * @return array<int, int>
     */
    private function idsConDocumentos(): array
    {
        return collect(RegistroInicial::claves())
            ->flatMap(function (string $entidad) {
                if ($entidad === 'eifinalks') {
                    // El informe final no tiene profesor_id: se hereda de la carga.
                    return Pevaluacion::query()
                        ->whereIn('id', $this->consultaBase($entidad)->select('pevaluacion_id'))
                        ->pluck('profesor_id');
                }

                return $this->consultaBase($entidad)->distinct()->pluck('profesor_id');
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /** @param array<string, mixed> $filtros */
    public function paginar(string $entidad, array $filtros, int $porPagina = 15): LengthAwarePaginator
    {
        return $this->aplicarFiltros($this->consultaListado($entidad), $entidad, $filtros)
            ->latest('id')
            ->paginate($porPagina)
            ->withQueryString();
    }

    /**
     * KPI de la entidad activa, con los mismos filtros que el listado.
     *
     * @param  array<string, mixed>  $filtros
     * @return array<string, mixed>
     */
    public function resumen(string $entidad, array $filtros): array
    {
        $query = $this->aplicarFiltros($this->consultaBase($entidad), $entidad, $filtros);
        $campoRevision = RegistroInicial::campoRevision($entidad);

        return [
            'total' => (clone $query)->count(),
            'docentes' => $this->contarDocentes(clone $query, $entidad),
            'sin_revision' => $campoRevision
                ? (clone $query)
                    ->where(fn ($q) => $q->whereNull($campoRevision)->orWhere($campoRevision, ''))
                    ->count()
                : null,
            'revisados' => $campoRevision
                ? (clone $query)->whereNotNull($campoRevision)->where($campoRevision, '<>', '')->count()
                : null,
            'completos' => $entidad === 'eiplanningwks'
                ? $this->contarCompletos(clone $query)
                : null,
            'ultima_actualizacion' => (clone $query)->max('updated_at'),
        ];
    }

    /**
     * Estado de un documento.
     *
     * @return array{clave: string, etiqueta: string, variante: string, detalle: string}
     */
    public function estado(string $entidad, Model $documento): array
    {
        if ($entidad === 'eiplanningwks') {
            $total = $this->totalCeldasSemanales();
            $escritas = $this->contar($documento, 'estrategias_count', 'eiplanningwstrategies', 'lunes');

            return match (true) {
                $escritas >= $total => [
                    'clave' => 'completo',
                    'etiqueta' => 'Completo',
                    'variante' => 'emerald',
                    'detalle' => "{$escritas}/{$total} estrategias",
                ],
                $escritas > 0 => [
                    'clave' => 'en_progreso',
                    'etiqueta' => 'En progreso',
                    'variante' => 'amber',
                    'detalle' => "{$escritas}/{$total} estrategias",
                ],
                default => [
                    'clave' => 'sin_iniciar',
                    'etiqueta' => 'Sin iniciar',
                    'variante' => 'gray',
                    'detalle' => "0/{$total} estrategias",
                ],
            };
        }

        if ($entidad === 'eifinalks') {
            return [
                'clave' => 'emitido',
                'etiqueta' => 'Emitido',
                'variante' => 'emerald',
                'detalle' => 'Informe final',
            ];
        }

        // El máximo de los contadores: `??` no sirve porque withCount devuelve
        // 0 (no null) y un plan especial puede tener actividades sin estrategias.
        $contenido = $this->contenidoDocumento($documento, $entidad);

        return [
            'clave' => $contenido > 0 ? 'en_progreso' : 'sin_iniciar',
            'etiqueta' => $contenido > 0 ? 'Con contenido' : 'Sin contenido',
            'variante' => $contenido > 0 ? 'cyan' : 'gray',
            'detalle' => $contenido.' ítem(s)',
        ];
    }

    /** @return array<string, string> */
    public function opcionesEstado(string $entidad): array
    {
        if ($entidad === 'eifinalks') {
            return []; // no tiene revisión ni estado de contenido en fase 1
        }

        $revision = [
            'sin_revision' => 'Sin revisión',
            'revisado' => 'Revisado',
        ];

        return $entidad === 'eiplanningwks'
            ? [
                'sin_iniciar' => 'Sin iniciar',
                'en_progreso' => 'En progreso',
                'completo' => 'Completo',
            ] + $revision
            : $revision;
    }

    // ─── Consultas ────────────────────────────────────────────────

    private function consultaBase(string $entidad): Builder
    {
        /** @var class-string<Model> $modelo */
        $modelo = RegistroInicial::modelo($entidad);

        return $modelo::query();
    }

    private function consultaListado(string $entidad): Builder
    {
        return $this->aplicarConteos(
            $this->consultaBase($entidad)->with(self::RELACIONES_LISTADO[$entidad]),
            $entidad
        );
    }

    private function aplicarConteos(Builder $query, string $entidad): Builder
    {
        foreach (self::CONTEO[$entidad] ?? [] as $alias => [$relacion, $columna]) {
            $query->withCount([
                $relacion.' as '.$alias => fn ($q) => $columna === null
                    ? $q
                    : $q->whereNotNull($columna)->where($columna, '<>', ''),
            ]);
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $filtros
     */
    private function aplicarFiltros(Builder $query, string $entidad, array $filtros): Builder
    {
        $final = $entidad === 'eifinalks';

        // Docente y sección: el informe final los hereda de la carga académica.
        foreach (['profesor_id', 'seccion_id'] as $columna) {
            if (! empty($filtros[$columna])) {
                $final
                    ? $query->whereHas('pevaluacion', fn ($q) => $q->where($columna, $filtros[$columna]))
                    : $query->where($columna, $filtros[$columna]);
            }
        }

        if (! empty($filtros['grado_id'])) {
            $final
                ? $query->whereHas('pevaluacion.pensum', fn ($q) => $q->where('grado_id', $filtros['grado_id']))
                : $query->where('grado_id', $filtros['grado_id']);
        }

        // Lapso: solo evaluación (columna propia) e informe final (vía carga).
        if (! empty($filtros['lapso_id'])) {
            if ($entidad === 'eievaluationks') {
                $query->where('lapso_id', $filtros['lapso_id']);
            } elseif ($final) {
                $query->whereHas('pevaluacion', fn ($q) => $q->where('lapso_id', $filtros['lapso_id']));
            }
        }

        // Fechas: solapamiento con el rango, no contención. El informe final no
        // tiene período propio: se usa created_at (fase 2 puede cambiarlo).
        $columnaInicio = $final ? 'created_at' : 'finicial';
        $columnaFin = $final ? 'created_at' : 'ffinal';

        if (! empty($filtros['desde'])) {
            $query->whereDate($columnaFin, '>=', $filtros['desde']);
        }

        if (! empty($filtros['hasta'])) {
            $query->whereDate($columnaInicio, '<=', $filtros['hasta']);
        }

        if (! empty($filtros['search'])) {
            $texto = '%'.$filtros['search'].'%';

            $query->where(function (Builder $q) use ($entidad, $texto) {
                foreach (self::BUSQUEDA[$entidad] as $columna) {
                    $q->orWhere($columna, 'like', $texto);
                }

                if ($entidad === 'eifinalks') {
                    // El estudiante guarda `name` y `lastname` (no `firstname`).
                    $q->orWhereHas('expectant', fn ($e) => $e
                        ->where('name', 'like', $texto)
                        ->orWhere('lastname', 'like', $texto));
                }
            });
        }

        if (! empty($filtros['estado'])) {
            $this->aplicarEstado($query, $entidad, (string) $filtros['estado']);
        }

        return $query;
    }

    private function aplicarEstado(Builder $query, string $entidad, string $estado): void
    {
        $campoRevision = RegistroInicial::campoRevision($entidad);

        // `completo` solo existe en la planificación semanal (rejilla de 50
        // celdas). Un query string manipulado no debe intentar `whereHas` de
        // una relación que otro catálogo no tiene.
        if ($estado === 'completo' && $entidad !== 'eiplanningwks') {
            return;
        }

        if ($campoRevision && $estado === 'sin_revision') {
            $query->where(fn ($q) => $q->whereNull($campoRevision)->orWhere($campoRevision, ''));

            return;
        }

        if ($campoRevision && $estado === 'revisado') {
            $query->whereNotNull($campoRevision)->where($campoRevision, '<>', '');

            return;
        }

        // Estados de CONTENIDO. Se resuelven con whereHas + umbral, no con
        // having() sobre el alias de withCount(): paginate() ejecuta un count(*)
        // que se rompe con un HAVING sin GROUP BY.
        //
        // Se miran TODAS las relaciones de contenido del documento (estrategias,
        // actividades, posiciones), no solo la primera: un plan especial puede
        // tener actividades sin estrategias y no por eso está "sin iniciar".
        $relevantes = array_values(array_intersect_key(
            self::CONTEO[$entidad] ?? [],
            array_flip(['estrategias_count', 'actividades_count', 'posiciones_count'])
        ));

        if ($relevantes === []) {
            return; // el informe final no ofrece filtro de estado
        }

        // Devuelve el closure de condición para cada relación de contenido.
        $conContenido = fn (?string $columna) => fn ($q) => $columna === null
            ? $q
            : $q->whereNotNull($columna)->where($columna, '<>', '');

        match ($estado) {
            // `completo` solo se ofrece en semanal: es el único documento con
            // una rejilla de celdas acotada (5 días × 10 momentos) en fase 1.
            'completo' => $query->whereHas(
                'eiplanningwstrategies',
                $conContenido('lunes'),
                '>=',
                $this->totalCeldasSemanales()
            ),
            // `en_progreso` es el complemento exacto de `sin_iniciar` y
            // `completo` en semanal: de 1 a 49 celdas. Sin el techo, el filtro
            // devolvía también las semanas completas, cuyo badge dice
            // «Completo»: el selector no particionaba el listado.
            //
            // `whereHas(..., '<', total)` y no `whereDoesntHave(..., '>=', ...)`:
            // whereDoesntHave no acepta operador ni umbral.
            'en_progreso' => $query
                ->where(function ($q) use ($relevantes, $conContenido) {
                    foreach ($relevantes as [$relacion, $columna]) {
                        $q->orWhereHas($relacion, $conContenido($columna));
                    }
                })
                ->when(
                    $entidad === 'eiplanningwks',
                    fn (Builder $q) => $q->whereHas(
                        'eiplanningwstrategies',
                        $conContenido('lunes'),
                        '<',
                        $this->totalCeldasSemanales()
                    )
                ),
            'sin_iniciar' => $query->where(function ($q) use ($relevantes, $conContenido) {
                foreach ($relevantes as [$relacion, $columna]) {
                    $q->whereDoesntHave($relacion, $conContenido($columna));
                }
            }),
            default => null,
        };
    }

    private function contarDocentes(Builder $query, string $entidad): int
    {
        // El informe final no tiene profesor_id: el docente se hereda de la
        // carga académica. Se cuenta el profesor distinto, no la carga: un
        // docente con varias secciones/lapsos es UN docente.
        if ($entidad === 'eifinalks') {
            return (int) $query
                ->join('pevaluacions', 'pevaluacions.id', '=', 'eifinalks.pevaluacion_id')
                ->distinct()
                ->count('pevaluacions.profesor_id');
        }

        return (int) $query->distinct()->count('profesor_id');
    }

    private function contarCompletos(Builder $query): int
    {
        // `whereHas` con umbral y no `having()` sobre el alias de withCount():
        // paginate() ejecuta un count(*) que se rompe con un HAVING sin GROUP BY.
        return $query->whereHas(
            'eiplanningwstrategies',
            fn ($q) => $q->whereNotNull('lunes')->where('lunes', '<>', ''),
            '>=',
            $this->totalCeldasSemanales()
        )->count();
    }

    private function totalCeldasSemanales(): int
    {
        return count(config('inicial.dias_semana')) * count(config('inicial.momentos_rutina_diaria'));
    }

    /**
     * Máximo de contadores de contenido del documento.
     *
     * Un plan especial puede tener actividades sin estrategias: por eso se
     * toma el máximo y no el primero que exista.
     */
    private function contenidoDocumento(Model $documento, string $entidad): int
    {
        $contenidos = array_map(
            fn (array $definicion) => $this->contar($documento, ...$definicion),
            self::CONTEO_CONTENIDO[$entidad] ?? []
        );

        return $contenidos === [] ? 0 : max($contenidos);
    }

    /**
     * Cuenta un contador ya calculado (`withCount`) o, si no existe, las filas
     * de la relación ya cargada.
     *
     * Es lo que permite reutilizar `estado()` en el listado (15 filas con
     * `withCount`) y en el modal/detalle (un documento con relaciones).
     */
    private function contar(Model $documento, string $atributo, string $relacion, ?string $columna): int
    {
        $valor = $documento->getAttribute($atributo);

        if ($valor !== null) {
            return (int) $valor;
        }

        if (! $documento->relationLoaded($relacion)) {
            return 0;
        }

        $items = $documento->getRelation($relacion);

        return $columna === null
            ? $items->count()
            : $items->filter(fn ($item) => filled($item->{$columna}))->count();
    }
}
