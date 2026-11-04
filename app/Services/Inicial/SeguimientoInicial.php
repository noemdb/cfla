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
     * Conteos para los accesos rápidos del seguimiento semanal.
     *
     * Los conteos conservan los filtros generales y deliberadamente ignoran
     * avance/revisión para que los botones no desaparezcan al seleccionarlos.
     *
     * @param  array<string, mixed>  $filtros
     * @return array<string, int>
     */
    public function conteosRapidos(string $entidad, array $filtros): array
    {
        unset($filtros['estado'], $filtros['revision'], $filtros['orden']);

        $base = fn () => $this->aplicarFiltros($this->consultaBase($entidad), $entidad, $filtros);
        $conteos = [];

        foreach ($this->opcionesEstado($entidad) as $estado => $etiqueta) {
            $query = $base();
            $this->aplicarEstado($query, $entidad, $estado);
            $conteos[$estado] = $query->count();
        }

        $total = $conteos === [] ? $base()->count() : array_sum($conteos);

        if ($campoRevision = $this->campoRevisionSeguimiento($entidad)) {
            $conteos['sin_revision'] = $base()
                ->where(fn ($query) => $query
                    ->whereNull($campoRevision)
                    ->orWhere($campoRevision, ''))
                ->count();
            $conteos['revisado'] = max(0, $total - $conteos['sin_revision']);
        }

        return $conteos;
    }

    /**
     * Consolida los documentos semanales por docente, manteniendo los filtros
     * generales activos y calculando pendientes fuera de los filtros de estado.
     *
     * @param  array<string, mixed>  $filtros
     * @return Collection<int, array{profesor_id: int, nombre: string, total: int, incompletos: int, sin_revision: int}>
     */
    public function resumenDocentes(string $entidad, array $filtros): Collection
    {
        unset($filtros['estado'], $filtros['revision'], $filtros['orden']);

        $base = $this->aplicarFiltros($this->consultaBase($entidad), $entidad, $filtros);
        $totales = $this->contarPorDocente(clone $base, $entidad);
        $completos = [];

        if ($this->opcionesEstado($entidad) !== []) {
            $queryCompletos = clone $base;
            $this->aplicarEstado($queryCompletos, $entidad, 'completo');
            $completos = $this->contarPorDocente($queryCompletos, $entidad);
        }

        $sinRevision = [];
        if ($campoRevision = $this->campoRevisionSeguimiento($entidad)) {
            $querySinRevision = clone $base;
            $querySinRevision->where(fn ($query) => $query
                ->whereNull($campoRevision)
                ->orWhere($campoRevision, ''));
            $sinRevision = $this->contarPorDocente($querySinRevision, $entidad);
        }

        $ids = $totales->keys()->map(fn ($id) => (int) $id);
        $nombres = Profesor::query()
            ->whereIn('id', $ids)
            ->get(['id', 'name', 'lastname'])
            ->mapWithKeys(fn (Profesor $profesor) => [
                $profesor->id => trim("{$profesor->lastname}, {$profesor->name}"),
            ]);

        return $totales
            ->map(fn (int $total, int|string $profesorId) => [
                'profesor_id' => (int) $profesorId,
                'nombre' => $nombres->get($profesorId, 'Docente sin identificar'),
                'total' => $total,
                'incompletos' => max(0, $total - (int) ($completos[$profesorId] ?? 0)),
                'sin_revision' => (int) ($sinRevision[$profesorId] ?? 0),
            ])
            ->sortBy('nombre', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
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
                $resumenes = $this->contar(
                    $documento,
                    'resumenes_count',
                    'eiplanningwsummaries',
                    null
                );
                $resumenesCompletos = $resumenes > 0;
                $pendientes = [];

                if ($estrategias < $total) {
                    $pendientes[] = ($total - $estrategias).' '.(($total - $estrategias) === 1
                        ? 'estrategia pendiente'
                        : 'estrategias pendientes');
                }

                if (! $resumenesCompletos) {
                    $pendientes[] = 'el resumen requerido';
                }

                $detalle = match (count($pendientes)) {
                    0 => "{$estrategias}/{$total} estrategias · resumen registrado",
                    1 => $pendientes[0] === 'el resumen requerido'
                        ? 'Falta el resumen requerido'
                        : 'Faltan '.$pendientes[0],
                    default => 'Faltan '.$pendientes[0].'; falta el resumen requerido',
                };
                $tieneResumenes = $resumenesCompletos;
            } else {
                $resumenesCompletos = true;
                $faltantes = max(0, $total - $estrategias);
                $detalle = $faltantes === 0
                    ? "{$estrategias}/{$total} estrategias completas"
                    : 'Faltan '.$faltantes.' '.($faltantes === 1 ? 'estrategia' : 'estrategias');
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

            $faltantes = max(0, $posiciones - $completas);

            return $this->estadoDeProgreso(
                $completo,
                $posiciones > 0,
                $posiciones === 0
                    ? 'Sin posiciones de aprendizaje registradas'
                    : ($faltantes > 0
                        ? "Faltan {$faltantes} ".($faltantes === 1 ? 'aprendizaje por registrar' : 'aprendizajes por registrar')
                        : "{$completas}/{$posiciones} aprendizajes registrados")
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
            $opciones['pendientes'] = $entidad === 'eispecialks'
                ? 'Pendientes: sin revisión y menos estrategias'
                : 'Pendientes primero';
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

        if (in_array($entidad, ['eiplanningwks', 'eispecialks'], true)) {
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

                if ($entidad === 'eispecialks') {
                    $q->orWhereHas('activities', fn (Builder $activities) => $activities
                        ->where(function (Builder $activity) use ($texto) {
                            foreach ([
                                'componente',
                                'objetivo',
                                'aprendizaje_esperado',
                                'indicadores',
                                'linea_investigacion',
                                'enfasis_curriculares',
                            ] as $columna) {
                                $activity->orWhere($columna, 'like', $texto);
                            }
                        }));
                    $q->orWhereHas('eispecialstrategies', fn (Builder $strategies) => $strategies
                        ->where('lunes', 'like', $texto));
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
                    $query->whereHas('eiplanningwsummaries');
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
                        $q->orWhereDoesntHave('eiplanningwsummaries');
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

    /**
     * @return Collection<int|string, int>
     */
    private function contarPorDocente(Builder $query, string $entidad): Collection
    {
        if ($entidad === 'eifinalks') {
            return $query
                ->join('pevaluacions', 'pevaluacions.id', '=', 'eifinalks.pevaluacion_id')
                ->selectRaw('pevaluacions.profesor_id as profesor_id, COUNT(*) as total')
                ->groupBy('pevaluacions.profesor_id')
                ->pluck('total', 'profesor_id')
                ->map(fn ($total) => (int) $total);
        }

        return $query
            ->selectRaw($entidad.'.profesor_id as profesor_id, COUNT(*) as total')
            ->groupBy($entidad.'.profesor_id')
            ->pluck('total', 'profesor_id')
            ->map(fn ($total) => (int) $total);
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
                $query->whereHas('eiplanningwsummaries');
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
