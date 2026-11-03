<?php

namespace App\Services\Inicial;

use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
            'posiciones_completas_count' => ['eievaluationps', 'aprendizaje_alcanzado'],
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

    /**
     * Entidades cuyo avance se mide con la rejilla estándar de 50 estrategias.
     *
     * @var array<string, string>
     */
    private const RELACION_ESTRATEGIAS = [
        'eiplanningwks' => 'eiplanningwstrategies',
        'eiplanningbwks' => 'eiplanningbwstrategies',
        'eiprojectks' => 'eiprojectkstrategies',
        'eispecialks' => 'eispecialstrategies',
    ];

    /** Columnas de búsqueda libre por documento. @var array<string, array<int, string>> */
    private const BUSQUEDA = [
        'eiplanningwks' => ['diagnostico', 'observacion', 'revision_seguimiento'],
        'eiplanningbwks' => ['diagnostico', 'observacion', 'revision_seguimiento'],
        'eiprojectks' => ['diagnostico', 'observacion', 'revision_seguimiento'],
        'eispecialks' => ['justificacion', 'observacion', 'revision_seguimiento'],
        'eievaluationks' => ['observaciones', 'recomendacion', 'asistencia', 'revision_seguimiento'],
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
        $query = $this->aplicarFiltros($this->consultaListado($entidad), $entidad, $filtros);

        match ($filtros['orden'] ?? 'actualizacion') {
            'periodo' => $query
                ->orderByDesc($entidad === 'eifinalks' ? 'created_at' : 'finicial')
                ->orderByDesc('id'),
            'pendientes' => $this->ordenarPendientes($query, $entidad),
            default => $query->orderByDesc('updated_at')->orderByDesc('id'),
        };

        $paginator = $query
            ->paginate($porPagina)
            ->withQueryString();

        if ($entidad === 'eiplanningwks') {
            $this->adjuntarResumenesEsperados($paginator);
        }

        return $paginator;
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
        $campoRevision = $this->campoRevisionSeguimiento($entidad);

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
            'completos' => $entidad !== 'eifinalks'
                ? $this->contarCompletos(clone $query, $entidad)
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
        if (isset(self::RELACION_ESTRATEGIAS[$entidad])) {
            $relacion = self::RELACION_ESTRATEGIAS[$entidad];
            $estrategias = $this->contar($documento, 'estrategias_count', $relacion, 'lunes');
            $total = $this->totalEstrategiasEsperadas();
            $actividadAdicional = $entidad === 'eispecialks'
                ? $this->contar($documento, 'actividades_count', 'activities', null)
                : 0;

            if ($entidad === 'eiplanningwks') {
                $resumenes = $this->resumenesRegistrados($documento);
                $esperados = $this->resumenesEsperados($documento);
                $resumenesCompletos = $resumenes >= $esperados;
                $detalle = "{$estrategias}/{$total} estrategias · {$resumenes}/{$esperados} resúmenes";
                $tieneResumenes = $this->contar(
                    $documento,
                    'resumenes_count',
                    'eiplanningwsummaries',
                    null
                ) > 0;
            } else {
                $resumenesCompletos = true;
                $detalle = "{$estrategias}/{$total} estrategias";
                $tieneResumenes = false;
            }

            $completo = $estrategias >= $total && $resumenesCompletos;

            return $this->estadoDeProgreso(
                $completo,
                $estrategias > 0 || $actividadAdicional > 0 || $tieneResumenes,
                $detalle
            );
        }

        if ($entidad === 'eievaluationks') {
            $posiciones = $this->contar($documento, 'posiciones_count', 'eievaluationps', null);
            $completas = $this->contar(
                $documento,
                'posiciones_completas_count',
                'eievaluationps',
                'aprendizaje_alcanzado'
            );
            $completo = $posiciones > 0 && $completas >= $posiciones;

            return $this->estadoDeProgreso(
                $completo,
                $posiciones > 0,
                "{$completas}/{$posiciones} posiciones con aprendizaje registrado"
            );
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
        return $this->estadoDeProgreso(
            false,
            $this->contenidoDocumento($documento, $entidad) > 0,
            $this->contenidoDocumento($documento, $entidad).' ítem(s) registrados'
        );
    }

    /** @return array<string, string> */
    public function opcionesEstado(string $entidad): array
    {
        if ($entidad === 'eifinalks') {
            return [];
        }

        return isset(self::RELACION_ESTRATEGIAS[$entidad]) || $entidad === 'eievaluationks'
            ? [
                'sin_iniciar' => 'Sin iniciar',
                'en_progreso' => 'En progreso',
                'completo' => 'Completo',
            ]
            : [];
    }

    /** @return array<string, string> */
    public function opcionesRevision(string $entidad): array
    {
        return $this->campoRevisionSeguimiento($entidad)
            ? [
                'sin_revision' => 'Sin revisión',
                'revisado' => 'Revisado',
            ]
            : [];
    }

    /** @return array<string, string> */
    public function opcionesOrden(string $entidad): array
    {
        $opciones = [
            'actualizacion' => 'Última actualización',
            'periodo' => 'Período más reciente',
        ];

        if ($this->campoRevisionSeguimiento($entidad)) {
            $opciones['pendientes'] = 'Pendientes primero';
        }

        return $opciones;
    }

    // ─── Consultas ────────────────────────────────────────────────

    private function campoRevisionSeguimiento(string $entidad): ?string
    {
        return RegistroInicial::campoRevision($entidad) ? 'revision_seguimiento' : null;
    }

    private function ordenarPendientes(Builder $query, string $entidad): Builder
    {
        $query->orderByRaw("CASE WHEN TRIM(COALESCE(revision_seguimiento, '')) = '' THEN 0 ELSE 1 END");

        if ($entidad === 'eiplanningwks') {
            return $query->orderBy('estrategias_count')->orderBy('updated_at')->orderBy('id');
        }

        return $query->orderBy('updated_at')->orderBy('id');
    }

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
                    : $q->whereRaw("TRIM(COALESCE(`{$columna}`, '')) <> ''"),
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

        $estado = (string) ($filtros['estado'] ?? '');
        $revision = (string) ($filtros['revision'] ?? '');

        // Compatibilidad con enlaces guardados antes de separar ambos filtros.
        if ($revision === '' && in_array($estado, ['sin_revision', 'revisado'], true)) {
            $revision = $estado;
            $estado = '';
        }

        if ($estado !== '') {
            $this->aplicarEstado($query, $entidad, $estado);
        }

        if ($revision !== '' && $this->campoRevisionSeguimiento($entidad)) {
            if ($revision === 'sin_revision') {
                $query->where(fn ($q) => $q
                    ->whereNull('revision_seguimiento')
                    ->orWhere('revision_seguimiento', ''));
            } elseif ($revision === 'revisado') {
                $query->whereNotNull('revision_seguimiento')
                    ->where('revision_seguimiento', '<>', '');
            }
        }

        return $query;
    }

    private function aplicarEstado(Builder $query, string $entidad, string $estado): void
    {
        if (isset(self::RELACION_ESTRATEGIAS[$entidad])) {
            $relacion = self::RELACION_ESTRATEGIAS[$entidad];
            $conTexto = fn ($q) => $q->whereRaw("TRIM(COALESCE(`lunes`, '')) <> ''");
            $total = $this->totalEstrategiasEsperadas();

            if ($estado === 'completo') {
                $query->whereHas($relacion, $conTexto, '>=', $total);

                if ($entidad === 'eiplanningwks') {
                    $query->whereRaw($this->sqlResumenesSemanalesCompletos());
                }

                return;
            }

            if ($estado === 'sin_iniciar') {
                $query->whereDoesntHave($relacion, $conTexto);

                if ($entidad === 'eispecialks') {
                    $query->whereDoesntHave('activities');
                }

                if ($entidad === 'eiplanningwks') {
                    $query->whereDoesntHave('eiplanningwsummaries');
                }

                return;
            }

            if ($estado === 'en_progreso') {
                $query->where(function ($q) use ($entidad, $relacion, $conTexto) {
                    $q->whereHas($relacion, $conTexto);

                    if ($entidad === 'eispecialks') {
                        $q->orWhereHas('activities');
                    }

                    if ($entidad === 'eiplanningwks') {
                        $q->orWhereHas('eiplanningwsummaries');
                    }
                })->where(function ($q) use ($entidad, $relacion, $conTexto, $total) {
                    $q->whereHas($relacion, $conTexto, '<', $total);

                    if ($entidad === 'eiplanningwks') {
                        $q->orWhereRaw($this->sqlResumenesSemanalesIncompletos());
                    }
                });
            }

            return;
        }

        if ($entidad !== 'eievaluationks') {
            return;
        }

        $posicionIncompleta = fn ($q) => $q->where(function ($q) {
            $q->whereNull('aprendizaje_alcanzado')
                ->orWhereRaw("TRIM(COALESCE(`aprendizaje_alcanzado`, '')) = ''");
        });

        match ($estado) {
            'sin_iniciar' => $query->whereDoesntHave('eievaluationps'),
            'en_progreso' => $query
                ->whereHas('eievaluationps', $posicionIncompleta),
            'completo' => $query
                ->whereHas('eievaluationps')
                ->whereDoesntHave('eievaluationps', $posicionIncompleta),
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

    private function contarCompletos(Builder $query, string $entidad): int
    {
        if (isset(self::RELACION_ESTRATEGIAS[$entidad])) {
            $query->whereHas(
                self::RELACION_ESTRATEGIAS[$entidad],
                fn ($q) => $q->whereRaw("TRIM(COALESCE(`lunes`, '')) <> ''"),
                '>=',
                $this->totalEstrategiasEsperadas()
            );

            if ($entidad === 'eiplanningwks') {
                $query->whereRaw($this->sqlResumenesSemanalesCompletos());
            }

            return $query->count();
        }

        $posicionIncompleta = fn ($q) => $q->where(function ($q) {
            $q->whereNull('aprendizaje_alcanzado')
                ->orWhereRaw("TRIM(COALESCE(`aprendizaje_alcanzado`, '')) = ''");
        });

        return $query
            ->whereHas('eievaluationps')
            ->whereDoesntHave('eievaluationps', $posicionIncompleta)
            ->count();
    }

    private function totalEstrategiasEsperadas(): int
    {
        return count(config('inicial.dias_semana')) * count(config('inicial.momentos_rutina_diaria'));
    }

    /**
     * The weekly plan is complete only when its strategy grid and expected
     * summaries are both complete. Area expectations are teacher-specific.
     */
    private function sqlResumenesSemanalesCompletos(): string
    {
        return 'NOT EXISTS ('
            .'SELECT 1 FROM pevaluacions AS expected_evaluations '
            .'INNER JOIN pensums AS expected_pensums '
            .'ON expected_pensums.id = expected_evaluations.pensum_id '
            .'WHERE expected_evaluations.profesor_id = eiplanningwks.profesor_id '
            .'AND expected_evaluations.seccion_id = eiplanningwks.seccion_id '
            .'AND expected_evaluations.deleted_at IS NULL '
            .'AND expected_pensums.deleted_at IS NULL '
            .'AND NOT EXISTS ('
            .'SELECT 1 FROM eiplanningwsummaries AS weekly_summaries '
            .'INNER JOIN pevaluacions AS summary_evaluations '
            .'ON summary_evaluations.id = weekly_summaries.pevaluacion_id '
            .'WHERE weekly_summaries.eiplanningwk_id = eiplanningwks.id '
            .'AND summary_evaluations.profesor_id = eiplanningwks.profesor_id '
            .'AND summary_evaluations.seccion_id = eiplanningwks.seccion_id '
            .'AND summary_evaluations.pensum_id = expected_evaluations.pensum_id '
            .'AND summary_evaluations.deleted_at IS NULL'
            .'))';
    }

    private function sqlResumenesSemanalesIncompletos(): string
    {
        return 'NOT ('.$this->sqlResumenesSemanalesCompletos().')';
    }

    private function resumenesEsperados(Model $documento): int
    {
        $esperados = $documento->getAttribute('resumenes_esperados');

        if ($esperados !== null) {
            return (int) $esperados;
        }

        return (int) Pevaluacion::query()
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->where('pevaluacions.profesor_id', $documento->profesor_id)
            ->where('pevaluacions.seccion_id', $documento->seccion_id)
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->distinct()
            ->count('pevaluacions.pensum_id');
    }

    private function resumenesRegistrados(Model $documento): int
    {
        $registrados = $documento->getAttribute('resumenes_areas_completas_count');

        if ($registrados !== null) {
            return (int) $registrados;
        }

        return (int) DB::table('eiplanningwsummaries as weekly_summaries')
            ->join('pevaluacions as summary_evaluations', 'summary_evaluations.id', '=', 'weekly_summaries.pevaluacion_id')
            ->join('pensums as summary_pensums', 'summary_pensums.id', '=', 'summary_evaluations.pensum_id')
            ->where('weekly_summaries.eiplanningwk_id', $documento->getKey())
            ->where('summary_evaluations.profesor_id', $documento->profesor_id)
            ->where('summary_evaluations.seccion_id', $documento->seccion_id)
            ->whereNull('summary_evaluations.deleted_at')
            ->whereNull('summary_pensums.deleted_at')
            ->distinct()
            ->count('summary_evaluations.pensum_id');
    }

    private function adjuntarResumenesEsperados(LengthAwarePaginator $paginator): void
    {
        $planes = $paginator->getCollection();

        if ($planes->isEmpty()) {
            return;
        }

        $pares = $planes
            ->map(fn (Model $plan) => [
                'profesor_id' => (int) $plan->profesor_id,
                'seccion_id' => (int) $plan->seccion_id,
            ])
            ->unique(fn (array $par) => $par['profesor_id'].':'.$par['seccion_id'])
            ->values();

        $totales = Pevaluacion::query()
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->where(function ($query) use ($pares) {
                foreach ($pares as $par) {
                    $query->orWhere(fn ($query) => $query
                        ->where('pevaluacions.profesor_id', $par['profesor_id'])
                        ->where('pevaluacions.seccion_id', $par['seccion_id']));
                }
            })
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->selectRaw('pevaluacions.profesor_id, pevaluacions.seccion_id, COUNT(DISTINCT pevaluacions.pensum_id) as total')
            ->groupBy('pevaluacions.profesor_id', 'pevaluacions.seccion_id')
            ->get()
            ->mapWithKeys(fn ($row) => [
                $row->profesor_id.':'.$row->seccion_id => (int) $row->total,
            ]);

        $resumenesPorPlan = DB::table('eiplanningwsummaries as weekly_summaries')
            ->join('pevaluacions as summary_evaluations', 'summary_evaluations.id', '=', 'weekly_summaries.pevaluacion_id')
            ->join('pensums as summary_pensums', 'summary_pensums.id', '=', 'summary_evaluations.pensum_id')
            ->join('eiplanningwks as weekly_plans', 'weekly_plans.id', '=', 'weekly_summaries.eiplanningwk_id')
            ->whereIn('weekly_plans.id', $planes->modelKeys())
            ->whereColumn('summary_evaluations.profesor_id', 'weekly_plans.profesor_id')
            ->whereColumn('summary_evaluations.seccion_id', 'weekly_plans.seccion_id')
            ->whereNull('summary_evaluations.deleted_at')
            ->whereNull('summary_pensums.deleted_at')
            ->selectRaw('weekly_plans.id as plan_id, COUNT(DISTINCT summary_evaluations.pensum_id) as total')
            ->groupBy('weekly_plans.id')
            ->pluck('total', 'plan_id');

        foreach ($planes as $plan) {
            $clave = $plan->profesor_id.':'.$plan->seccion_id;
            $plan->setAttribute('resumenes_esperados', $totales->get($clave, 0));
            $plan->setAttribute('resumenes_areas_completas_count', (int) ($resumenesPorPlan[$plan->id] ?? 0));
        }
    }

    /**
     * @return array{clave: string, etiqueta: string, variante: string, detalle: string}
     */
    private function estadoDeProgreso(bool $completo, bool $tieneContenido, string $detalle): array
    {
        if ($completo) {
            return [
                'clave' => 'completo',
                'etiqueta' => 'Completo',
                'variante' => 'emerald',
                'detalle' => $detalle,
            ];
        }

        return [
            'clave' => $tieneContenido ? 'en_progreso' : 'sin_iniciar',
            'etiqueta' => $tieneContenido ? 'En progreso' : 'Sin iniciar',
            'variante' => $tieneContenido ? 'amber' : 'gray',
            'detalle' => $detalle,
        ];
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
