<?php

namespace App\Livewire\Inicial;

use App\Models\app\Academy\Profesor;
use App\Notifications\InicialSeguimientoNotification;
use App\Services\Inicial\RegistroInicial;
use App\Services\Inicial\SeguimientoInicial;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

/**
 * Listado de seguimiento de Educación Inicial.
 *
 * Blueprint: blueprint/inicial · doc 11 · §8.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * CONSULTA + REVISIÓN, NADA MÁS
 * ─────────────────────────────────────────────────────────────────────────────
 * La escritura de seguimiento se limita a la revisión y, en planificación
 * semanal, a la observación existente del documento.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * POR QUÉ LA ENTIDAD ES `Locked`
 * ─────────────────────────────────────────────────────────────────────────────
 * La fija la ruta (`/seguimiento/{entidad}`), no el usuario. Si se pudiera
 * cambiar desde el cliente, un snapshot manipulado podría pedir la entidad de
 * otro catálogo; `Locked` lo impide antes de tocar la base de datos.
 */
class SeguimientoInicialComponent extends Component
{
    use WireUiActions, WithPagination;

    /** Documento activo; lo fija la ruta y no se puede cambiar desde el cliente. */
    #[Locked]
    public string $entidad;

    #[Url(as: 'docente', except: '')]
    public $profesor_id = '';

    #[Url(as: 'nivel', except: '')]
    public $grado_id = '';

    #[Url(as: 'seccion', except: '')]
    public $seccion_id = '';

    #[Url(as: 'lapso', except: '')]
    public $lapso_id = '';

    #[Url(as: 'desde', except: '')]
    public $desde = '';

    #[Url(as: 'hasta', except: '')]
    public $hasta = '';

    #[Url(as: 'estado', except: '')]
    public $estado = '';

    #[Url(as: 'revision', except: '')]
    public string $revisionEstado = '';

    #[Url(as: 'orden', except: 'actualizacion')]
    public string $orden = 'actualizacion';

    #[Url(as: 'buscar', except: '')]
    public string $search = '';

    #[Url(as: 'vista', except: 'grid')]
    public string $viewMode = 'grid';

    /** Tamaño de página. No se expone en la URL: la vista no lo ofrece en fase 1. */
    public int $paginate = 15;

    /** Documento abierto en el modal de detalle. */
    #[Locked]
    public ?int $detalleId = null;

    /** El modal muestra los formularios de seguimiento en lugar del detalle. */
    public bool $revisionAbierta = false;

    /** El resumen agregado se carga solo cuando el usuario lo despliega. */
    public bool $resumenDocentesAbierto = false;

    /** Los filtros empiezan plegados; los criterios activos siguen visibles como chips. */
    public bool $filtrosAbiertos = false;

    /** Modal de calendario para vista global de planificaciones. */
    public bool $showCalendarModal = false;

    /** Texto de revisión del manager; se persiste en `revision_seguimiento`. */
    public string $revision = '';

    /** Observación del plan semanal; se persiste en el campo existente. */
    public string $observacion = '';

    /** Pestaña activa del formulario de seguimiento. */
    public string $revisionPestana = 'revision';

    /** Catálogos del panel de filtros. */
    public Collection $listProfesores;

    public Collection $listGrados;

    public Collection $listSecciones;

    public Collection $listLapsos;

    public function mount(string $entidad): void
    {
        // Defensa en profundidad: la ruta ya exige el middleware, pero el
        // componente no debe ser usable si alguien lo invoca de otra forma.
        abort_unless(auth()->user()?->isInicialManager(), 403);
        abort_unless(RegistroInicial::existe($entidad), 404);

        $this->entidad = $entidad;
        $this->cargarCatalogos();
    }

    public function render()
    {
        $servicio = app(SeguimientoInicial::class);
        $filtros = $this->filtros();

        $documentos = $servicio->paginar($this->entidad, $filtros, $this->paginate);
        $conteosRapidos = in_array($this->entidad, ['eiplanningwks', 'eispecialks'], true)
            ? $servicio->conteosRapidos($this->entidad, $filtros)
            : [];
        $resumenDocentes = $this->entidad === 'eiplanningwks' && $this->resumenDocentesAbierto
            ? $servicio->resumenDocentes($this->entidad, $filtros)
            : collect();

        return view('livewire.inicial.seguimiento.index', [
            'documentos' => $documentos,
            'conteosRapidos' => $conteosRapidos,
            'resumenDocentes' => $resumenDocentes,
            'resumen' => $servicio->resumen($this->entidad, $filtros),
            'opcionesEstado' => $servicio->opcionesEstado($this->entidad),
            'opcionesRevision' => $servicio->opcionesRevision($this->entidad),
            'opcionesOrden' => $servicio->opcionesOrden($this->entidad),
            'filtrosActivos' => $this->filtrosActivos(),
            'soportaFechas' => $servicio->soportaFechas($this->entidad),
            'soportaLapso' => $servicio->soportaLapso($this->entidad),
            // `consulta()->first()` y no `buscar()`: dentro de Livewire un 404
            // por un registro borrado mientras el modal estaba abierto rompe
            // la respuesta; aquí el modal simplemente se queda sin cuerpo.
            'detalle' => $this->detalleId
                ? RegistroInicial::consulta($this->entidad)->whereKey($this->detalleId)->first()
                : null,
            'detalleParcial' => RegistroInicial::parcialDetalle($this->entidad),
            'pestanas' => $servicio->pestanas(),
        ]);
    }

    /** Catálogos del panel de filtros. Una sola vez por request. */
    private function cargarCatalogos(): void
    {
        $servicio = app(SeguimientoInicial::class);

        $this->listProfesores = $servicio->profesores();
        $this->listGrados = $servicio->grados();
        $this->listLapsos = $servicio->lapsos();
        $this->listSecciones = $servicio->secciones(
            $this->grado_id === '' ? null : (int) $this->grado_id
        );
    }

    /**
     * Filtros no vacíos que consumen el servicio y la query string.
     *
     * @return array<string, mixed>
     */
    private function filtros(): array
    {
        return array_filter([
            'profesor_id' => $this->profesor_id,
            'grado_id' => $this->grado_id,
            'seccion_id' => $this->seccion_id,
            'lapso_id' => $this->lapso_id,
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'estado' => in_array($this->estado, ['sin_revision', 'revisado'], true) ? '' : $this->estado,
            'revision' => $this->revisionEstado !== ''
                ? $this->revisionEstado
                : (in_array($this->estado, ['sin_revision', 'revisado'], true) ? $this->estado : ''),
            'orden' => $this->orden,
            'search' => $this->search,
        ], fn ($valor) => $valor !== null && $valor !== '');
    }

    public function updatedGradoId(): void
    {
        // Al cambiar (o limpiar) el nivel, la sección deja de ser válida: se
        // limpia y sus opciones se recalculan para el nivel nuevo. Sin nivel,
        // el selector de sección queda vacío y deshabilitado en la vista.
        $this->seccion_id = '';
        $this->listSecciones = app(SeguimientoInicial::class)
            ->secciones($this->grado_id === '' ? null : (int) $this->grado_id);

        $this->resetPage();
    }

    public function limpiarFiltros(): void
    {
        $this->reset([
            'profesor_id', 'grado_id', 'seccion_id', 'lapso_id',
            'desde', 'hasta', 'estado', 'revisionEstado', 'search', 'orden',
        ]);

        $this->listSecciones = app(SeguimientoInicial::class)->secciones(null);
        $this->resetPage();
    }

    public function updated(string $name): void
    {
        if (in_array($name, [
            'profesor_id', 'grado_id', 'seccion_id', 'lapso_id',
            'desde', 'hasta', 'estado', 'revisionEstado', 'orden', 'search',
        ], true)) {
            $this->resetPage();
        }
    }

    /** @return array<int, array{campo: string, etiqueta: string, valor: string}> */
    public function filtrosActivos(): array
    {
        $servicio = app(SeguimientoInicial::class);
        $opcionesEstado = $servicio->opcionesEstado($this->entidad);
        $opcionesRevision = $servicio->opcionesRevision($this->entidad);
        $estado = in_array($this->estado, ['sin_revision', 'revisado'], true) ? '' : $this->estado;
        $revisionEstado = $this->revisionEstado !== ''
            ? $this->revisionEstado
            : (in_array($this->estado, ['sin_revision', 'revisado'], true) ? $this->estado : '');

        $filtros = [
            'profesor_id' => ['Docente', $this->listProfesores->get((int) $this->profesor_id, 'ID '.$this->profesor_id)],
            'grado_id' => ['Nivel', $this->listGrados->get((int) $this->grado_id, 'ID '.$this->grado_id)],
            'seccion_id' => ['Sección', $this->listSecciones->get((int) $this->seccion_id, 'ID '.$this->seccion_id)],
            'lapso_id' => ['Lapso', $this->listLapsos->get((int) $this->lapso_id, 'ID '.$this->lapso_id)],
            'desde' => ['Desde', $this->desde !== '' ? \Illuminate\Support\Carbon::parse($this->desde)->format('d/m/Y') : null],
            'hasta' => ['Hasta', $this->hasta !== '' ? \Illuminate\Support\Carbon::parse($this->hasta)->format('d/m/Y') : null],
            'estado' => ['Avance', $opcionesEstado[$estado] ?? null],
            'revisionEstado' => ['Revisión', $opcionesRevision[$revisionEstado] ?? null],
            'search' => ['Búsqueda', $this->search],
        ];
        $valores = [
            'profesor_id' => $this->profesor_id,
            'grado_id' => $this->grado_id,
            'seccion_id' => $this->seccion_id,
            'lapso_id' => $this->lapso_id,
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'estado' => $estado,
            'revisionEstado' => $revisionEstado,
            'search' => $this->search,
        ];

        return collect($filtros)
            ->filter(fn ($filtro, $campo) => filled($valores[$campo]) && filled($filtro[1]))
            ->map(fn ($filtro, $campo) => [
                'campo' => $campo,
                'etiqueta' => $filtro[0],
                'valor' => (string) $filtro[1],
            ])
            ->values()
            ->all();
    }

    public function quitarFiltro(string $campo): void
    {
        if (! in_array($campo, [
            'profesor_id', 'grado_id', 'seccion_id', 'lapso_id',
            'desde', 'hasta', 'estado', 'revisionEstado', 'search',
        ], true)) {
            return;
        }

        $this->{$campo} = '';

        if ($campo === 'grado_id') {
            $this->seccion_id = '';
            $this->listSecciones = app(SeguimientoInicial::class)->secciones(null);
        }

        if ($campo === 'revisionEstado' && in_array($this->estado, ['sin_revision', 'revisado'], true)) {
            $this->estado = '';
        }

        $this->resetPage();
    }

    public function aplicarFiltroRapido(string $campo, string $valor): void
    {
        $servicio = app(SeguimientoInicial::class);

        if ($campo === 'estado' && array_key_exists($valor, $servicio->opcionesEstado($this->entidad))) {
            if ($revisionLegacy = in_array($this->estado, ['sin_revision', 'revisado'], true)) {
                $this->revisionEstado = $this->revisionEstado !== '' ? $this->revisionEstado : $this->estado;
            }

            $this->estado = $this->estado === $valor ? '' : $valor;
            if ($revisionLegacy) {
                $this->estado = $valor;
            }
        } elseif ($campo === 'revisionEstado'
            && array_key_exists($valor, $servicio->opcionesRevision($this->entidad))) {
            $revisionLegacy = in_array($this->estado, ['sin_revision', 'revisado'], true);
            $actual = $this->revisionEstado !== ''
                ? $this->revisionEstado
                : ($revisionLegacy ? $this->estado : '');

            $this->revisionEstado = $actual === $valor ? '' : $valor;
            if ($revisionLegacy) {
                $this->estado = '';
            }
        } else {
            return;
        }

        $this->resetPage();
    }

    public function filtrarDocente(int $profesorId): void
    {
        if (! $this->listProfesores->has($profesorId)) {
            return;
        }

        $this->profesor_id = $this->profesor_id === (string) $profesorId
            ? ''
            : (string) $profesorId;
        $this->resetPage();
    }

    public function toggleResumenDocentes(): void
    {
        $this->resumenDocentesAbierto = ! $this->resumenDocentesAbierto;
    }

    public function toggleFiltros(): void
    {
        $this->filtrosAbiertos = ! $this->filtrosAbiertos;
    }

    public function abrirDetalle(int $id): void
    {
        $existe = RegistroInicial::modelo($this->entidad)::query()->whereKey($id)->exists();

        if (! $existe) {
            $this->cerrarDetalle();

            return;
        }

        $this->detalleId = $id;
        $this->limpiarRevision();
    }

    public function cerrarDetalle(): void
    {
        $this->detalleId = null;
        $this->limpiarRevision();
    }

    /**
     * Abre el modal ya en modo revisión desde el botón de la tarjeta o la tabla.
     *
     * La escritura sigue ocurriendo dentro del modal: este método solo evita el
     * paso intermedio de abrir el detalle y pulsar «Registrar revisión».
     */
    public function revisarDetalle(int $id): void
    {
        $this->abrirDetalle($id);

        if ($this->detalleId) {
            $this->abrirRevision();
        }
    }

    /**
     * Abre el formulario de revisión con el texto actual.
     *
     * El informe final no tiene campo revisable: 404 y no un formulario vacío
     * que al guardar no escribiría nada.
     */
    public function abrirRevision(): void
    {
        $documento = $this->documentoDetalle();
        $campo = $this->campoRevisionSeguimiento();

        abort_if(! $campo || ! $documento, 404);

        $this->revision = (string) $documento->{$campo};
        $this->observacion = $this->entidad === 'eiplanningwks'
            ? (string) $documento->observacion
            : '';
        $this->revisionPestana = 'revision';
        $this->revisionAbierta = true;

        $this->resetValidation();
    }

    public function seleccionarPestanaRevision(string $pestana): void
    {
        abort_unless(in_array($pestana, ['revision', 'observacion'], true), 404);
        abort_if($pestana === 'observacion' && $this->entidad !== 'eiplanningwks', 404);

        $this->revisionPestana = $pestana;
    }

    public function cerrarRevision(): void
    {
        // Cancelar el formulario debe cerrar el modal completo. En particular,
        // `revisarDetalle()` abre directamente el formulario; volver al detalle
        // aquí mostraría de golpe todo el contenido del documento.
        $this->cerrarDetalle();

        $this->resetValidation();
    }

    /**
     * Guarda la revisión de seguimiento del manager.
     */
    public function guardarRevision(): void
    {
        // Defensa en profundidad: el componente solo se monta para managers,
        // pero una actualización de Livewire no vuelve a ejecutar `mount()`.
        abort_unless(auth()->user()?->isInicialManager(), 403);

        $campo = $this->campoRevisionSeguimiento();
        abort_if(! $campo, 404);

        $rotulo = 'Revisión de seguimiento';
        $minuscula = mb_strtolower($rotulo);
        $this->revision = trim($this->revision);

        $this->validate([
            'revision' => ['required', 'string', 'min:5'],
        ], [
            'revision.required' => "Escribe la {$minuscula} de la Coordinación.",
            'revision.min' => "La {$minuscula} debe tener al menos 5 caracteres.",
        ]);

        $documento = $this->documentoDetalle();
        abort_if(! $documento, 404);

        $actualizacion = filled($documento->{$campo});
        $documento->{$campo} = trim($this->revision);
        $documento->save();
        $this->notificarDocente($documento, $campo, $actualizacion);

        $this->limpiarRevision();
        $this->detalleId = null;
        $this->resetValidation();
        $this->notification()->success(
            "{$rotulo} guardada",
            'La revisión independiente se actualizó correctamente.'
        );
    }

    public function guardarObservacion(): void
    {
        abort_unless(auth()->user()?->isInicialManager(), 403);
        abort_unless($this->entidad === 'eiplanningwks', 404);

        $this->observacion = trim($this->observacion);
        $this->validate([
            'observacion' => ['required', 'string', 'min:5'],
        ], [
            'observacion.required' => 'Escribe la observación del plan.',
            'observacion.min' => 'La observación debe tener al menos 5 caracteres.',
        ]);

        $documento = $this->documentoDetalle();
        abort_if(! $documento, 404);

        $actualizacion = filled($documento->observacion);
        $documento->observacion = $this->observacion;
        $documento->save();
        $this->notificarDocente($documento, 'observacion', $actualizacion);

        $this->limpiarRevision();
        $this->detalleId = null;
        $this->resetValidation();
        $this->notification()->success(
            'Observación guardada',
            'La observación del plan semanal se actualizó correctamente.'
        );
    }

    /** Documento del modal, sin relaciones: solo se usa para leer/escribir el campo. */
    private function documentoDetalle(): ?Model
    {
        if (! $this->detalleId) {
            return null;
        }

        return RegistroInicial::modelo($this->entidad)::query()
            ->whereKey($this->detalleId)
            ->first();
    }

    private function campoRevisionSeguimiento(): ?string
    {
        return RegistroInicial::campoRevision($this->entidad) ? 'revision_seguimiento' : null;
    }

    private function notificarDocente(Model $documento, string $campo, bool $actualizacion): void
    {
        $profesor = Profesor::query()
            ->with('user')
            ->find($documento->profesor_id);

        if (! $profesor?->user) {
            Log::warning('No se pudo notificar el seguimiento de Inicial: el profesor no tiene usuario asociado.', [
                'entidad' => $this->entidad,
                'documento_id' => $documento->getKey(),
                'profesor_id' => $documento->profesor_id,
                'campo' => $campo,
            ]);

            return;
        }

        $esObservacion = $campo === 'observacion';
        $tipo = $esObservacion ? 'inicial_weekly_observation' : 'inicial_followup_review';
        $accion = $actualizacion ? 'actualizó' : 'registró';
        $contenido = $esObservacion ? 'una observación' : 'una revisión de seguimiento';
        $tituloDocumento = RegistroInicial::titulo($this->entidad);

        app(NotificationService::class)->notifyUsers(
            [$profesor->user],
            new InicialSeguimientoNotification(
                type: $tipo,
                message: "Coordinación {$accion} {$contenido} en tu {$tituloDocumento}.",
                url: route('inicials.'.$this->entidad.'.index'),
                entidad: $this->entidad,
                documentoId: (int) $documento->getKey(),
                accion: $actualizacion ? 'actualizado' : 'registrado',
                campo: $campo,
            )
        );
    }

    private function limpiarRevision(): void
    {
        $this->revisionAbierta = false;
        $this->revision = '';
        $this->observacion = '';
        $this->revisionPestana = 'revision';
    }

    public function alternarVista(): void
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'table' : 'grid';
    }

    // ─── Calendario ──────────────────────────────────────────────

    public function openCalendar(): void
    {
        $this->showCalendarModal = true;
    }

    public function closeCalendar(): void
    {
        $this->showCalendarModal = false;
    }

    /**
     * Obtiene los planes para el calendario, agrupados por fecha.
     * Considera los filtros aplicados en el seguimiento.
     */
    public function getCalendarPlans(): array
    {
        $servicio = app(SeguimientoInicial::class);
        $filtros = $this->filtros();

        // Obtener todos los planes sin paginación
        $plans = RegistroInicial::documentos($this->entidad, $filtros);

        $calendarData = [];

        foreach ($plans as $plan) {
            // Determinar estado del plan
            $estado = $this->determinarEstadoPlan($plan);

            // Color según estado
            $color = match ($estado['clave']) {
                'completo' => 'bg-emerald-500/20 border-emerald-500/30 text-emerald-300',
                'en_progreso' => 'bg-amber-500/20 border-amber-500/30 text-amber-300',
                default => 'bg-gray-500/20 border-gray-500/30 text-gray-400',
            };

            // Agregar evento para cada día del rango (solo para eiplanningwks)
            if ($this->entidad === 'eiplanningwks' || $this->entidad === 'eiplanningbwks') {
                $fechaInicio = $plan->finicial;
                $fechaFin = $plan->ffinal;

                if ($fechaInicio && $fechaFin) {
                    $currentDate = $fechaInicio->copy();

                    while ($currentDate->lte($fechaFin)) {
                        $calendarData[] = [
                            'date' => $currentDate->format('Y-m-d'),
                            'plan' => $plan,
                            'color' => $color,
                            'estado' => $estado,
                            'esPrimerDia' => $currentDate->isSameDay($fechaInicio),
                            'esUltimoDia' => $currentDate->isSameDay($fechaFin),
                        ];
                        $currentDate->addDay();
                    }
                }
            } else {
                // Para otros documentos, mostrar solo la fecha de creación/actualización
                $fecha = $plan->created_at ?? now();
                $calendarData[] = [
                    'date' => $fecha->format('Y-m-d'),
                    'plan' => $plan,
                    'color' => $color,
                    'estado' => $estado,
                    'esPrimerDia' => true,
                    'esUltimoDia' => true,
                ];
            }
        }

        return $calendarData;
    }

    /**
     * Determina el estado de un plan para la visualización en calendario.
     */
    private function determinarEstadoPlan($plan): array
    {
        // Estado basado en revisión de seguimiento
        $revisado = filled($plan->revision_seguimiento ?? $plan->observacion ?? null);

        if ($revisado) {
            return [
                'clave' => 'completo',
                'etiqueta' => 'Revisado',
                'detalle' => 'Documento revisado',
            ];
        }

        // Para planificaciones, verificar estrategias y resúmenes
        if (property_exists($plan, 'estrategias_count') || property_exists($plan, 'eiplanningwstrategies_count')) {
            $estrategiasCount = $plan->estrategias_count ?? $plan->eiplanningwstrategies_count ?? 0;
            $resumenesCount = $plan->resumenes_count ?? $plan->eiplanningwsummaries_count ?? 0;

            if ($estrategiasCount >= 40 && $resumenesCount >= 1) {
                return [
                    'clave' => 'completo',
                    'etiqueta' => 'Completo',
                    'detalle' => 'Planificación completa',
                ];
            } elseif ($estrategiasCount > 0 || $resumenesCount > 0) {
                return [
                    'clave' => 'en_progreso',
                    'etiqueta' => 'En progreso',
                    'detalle' => sprintf('%d estrategias, %d resúmenes', $estrategiasCount, $resumenesCount),
                ];
            }
        }

        return [
            'clave' => 'sin_iniciar',
            'etiqueta' => 'Sin revisar',
            'detalle' => 'Documento pendiente de revisión',
        ];
    }

    /**
     * Filtros activos con los nombres públicos de la URL.
     *
     * Las pestañas viven DENTRO del componente para no conservar filtros
     * viejos cuando Livewire actualiza la query string.
     *
     * @return array<string, mixed>
     */
    public function queryFiltros(): array
    {
        return array_filter([
            'docente' => $this->profesor_id,
            'nivel' => $this->grado_id,
            'seccion' => $this->seccion_id,
            'lapso' => $this->lapso_id,
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'estado' => in_array($this->estado, ['sin_revision', 'revisado'], true) ? null : $this->estado,
            'revision' => $this->revisionEstado !== ''
                ? $this->revisionEstado
                : (in_array($this->estado, ['sin_revision', 'revisado'], true) ? $this->estado : null),
            'buscar' => $this->search,
            'orden' => $this->orden === 'actualizacion' ? null : $this->orden,
            'vista' => $this->viewMode === 'grid' ? null : $this->viewMode,
        ], fn ($valor) => $valor !== null && $valor !== '');
    }
}
