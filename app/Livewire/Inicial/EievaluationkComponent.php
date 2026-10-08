<?php

namespace App\Livewire\Inicial;

use App\Http\Requests\Inicial\EievaluationkRequest;
use App\Http\Requests\Inicial\EievaluationpRequest;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Seccion;
use App\Models\app\Inicial\Eievaluationk;
use App\Models\app\Inicial\Eievaluationp;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

/**
 * CRUD del PLAN DE EVALUACIÓN de Educación Inicial.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * EL ÚNICO DOCUMENTO SIN REJILLA DE ESTRATEGIAS
 * ─────────────────────────────────────────────────────────────────────────────
 * Los otros cuatro (semanal, quincenal, proyecto, plan especial) comparten la
 * cabecera, la rejilla día × momento y un gestor por área. Este no: su cabecera
 * está anclada a un `lapso_id` y se documenta con `observaciones` +
 * `asistencia` + `recomendacion`, y sus hijas son POSICIONES —el registro de lo
 * que hizo un grupo de niños en una actividad de un área—, no celdas.
 *
 * Por eso NO reutiliza el partial compartido del wizard: no hay wizard que
 * mostrar. Reutiliza en cambio el esqueleto de modal único (`$modalType`), los
 * filtros y el patrón de `findX()` con alcance, que sí son comunes.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * BUGS DEL LEGACY CORREGIDOS AQUÍ (doc 03)
 * ─────────────────────────────────────────────────────────────────────────────
 *  · IDOR en `loadEvaluation`/`loadPosition`/`delete`/`deletePosition`: los
 *    cuatro usaban `findOrFail($id)` a secas. El listado sí filtraba por
 *    `profesor_id`, pero llegar al modal con un id ajeno cargaba, guardaba o
 *    BORRABA datos de otro docente. Aquí todo pasa por `findEvaluation()` /
 *    `findPosition()`.
 *  · `getPevaluacionsList()` devolvía `[]` → `TypeError` al asignarlo a una
 *    propiedad tipada `Collection` (ver el modelo).
 *  · `fecha` se validaba como `string` sobre una columna DATE: con SQL en modo
 *    permisivo el texto se guardaba como NULL con un warning silencioso.
 *  · 403 explícito en `mount()`: el legacy dejaba `profesor_id = null` y el
 *    listado acababa mostrando planes huérfanos en vez de rechazar.
 *  · Fechas con datepicker nativo (el legacy usaba `type="text"`).
 */
class EievaluationkComponent extends Component
{
    use WireUiActions, WithPagination;

    // ─── Estado del modal ─────────────────────────────────────────

    public bool $showModal = false;

    /**
     * create · edit · view · position · edit-position
     */
    public string $modalType = '';

    public ?int $editingId = null;

    public ?int $eievaluationk_id = null;

    public ?int $eievaluationp_id = null;

    // ─── Formularios ─────────────────────────────────────────────

    public ?int $profesor_id = null;

    public $eievaluationk = [];

    public $eievaluationp = [];

    // ─── Filtros del listado ─────────────────────────────────────

    public string $search = '';

    public $filterGrado = '';

    public $filterSeccion = '';

    public $filterLapso = '';

    public int $paginate = 10;

    // ─── Listas para los selects ─────────────────────────────────

    public Collection $listGrado;

    public Collection $listSeccion;

    public Collection $listLapso;

    public Collection $listPevaluacion;

    public function mount(): void
    {
        $this->authorizeInicial();

        $profesor = Auth::user()->profesor;
        $this->profesor_id = $profesor?->id;

        $this->listGrado = $this->loadGrados();
        $this->listSeccion = collect();
        $this->listLapso = $this->loadLapsos();
        $this->listPevaluacion = collect();

        $this->resetModels();
    }

    public function render()
    {
        $query = Eievaluationk::where('profesor_id', $this->profesor_id)
            ->with(['grado', 'seccion', 'lapso', 'profesor']);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('observaciones', 'like', '%'.$this->search.'%')
                    ->orWhere('recomendacion', 'like', '%'.$this->search.'%')
                    ->orWhere('asistencia', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->filterGrado) {
            $query->where('grado_id', $this->filterGrado);
        }

        if ($this->filterSeccion) {
            $query->where('seccion_id', $this->filterSeccion);
        }

        if ($this->filterLapso) {
            $query->where('lapso_id', $this->filterLapso);
        }

        $eievaluationks = $query->orderByDesc('created_at')->paginate($this->paginate);

        return view('livewire.inicial.eievaluationk.index', [
            'eievaluationks' => $eievaluationks,
        ]);
    }

    // ─── Modal ───────────────────────────────────────────────────

    public function openModal(string $type, ?int $id = null): void
    {
        $this->resetValidation();
        $this->modalType = $type;
        $this->editingId = $id;
        $this->showModal = true;

        try {
            match ($type) {
                'create' => $this->resetModels(),
                'edit' => $this->loadEvaluation($id),
                'view' => $this->loadEvaluation($id),
                'position' => $this->loadEvaluationForPosition($id),
                'edit-position' => $this->loadPosition($id),
                default => $this->resetModels(),
            };
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            // 404/403 (plan o posición ajena) NO se tragan: se convertirían en
            // un "Error al cargar los datos" silencioso.
            $this->closeModal();

            throw $e;
        } catch (\Throwable $e) {
            $this->closeModal();
            $this->notification()->error(
                title: 'Error al cargar los datos',
                description: $e->getMessage()
            );
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->modalType = '';
        $this->editingId = null;
        $this->eievaluationk_id = null;
        $this->eievaluationp_id = null;
        $this->resetValidation();
    }

    // ─── Carga de datos ──────────────────────────────────────────

    private function loadEvaluation(?int $id): void
    {
        $plan = $this->findEvaluation($id);

        $this->eievaluationk_id = $plan->id;
        $this->eievaluationk = $plan->only([
            'profesor_id', 'grado_id', 'lapso_id', 'seccion_id',
            'finicial', 'ffinal', 'observaciones', 'recomendacion',
            'asistencia', 'observacion',
        ]);

        $this->listSeccion = $this->seccionesDe($plan->grado_id);
    }

    private function loadEvaluationForPosition(?int $id): void
    {
        $plan = $this->findEvaluation($id);
        $this->eievaluationk_id = $plan->id;

        // El legacy filtraba por el `$lapso_id` del plan solo al cambiar el
        // select; al abrir el gestor traía TODAS las pevaluaciones del docente.
        // Aquí se acota al lapso del plan, que es la única pevaluación que
        // pertenece a ese periodo de evaluación.
        $this->listPevaluacion = collect($plan->getPevaluacionsList($this->profesor_id, $plan->lapso_id));
        $this->resetModelPosition();
    }

    private function loadPosition(?int $id): void
    {
        if (! $id) {
            $this->resetModelPosition();

            return;
        }

        // Con alcance por `findPosition()`: el legacy hacía
        // `Eievaluationp::find($id)` sin filtro, de modo que un id ajeno cargaba
        // la posición de otro docente y `savePosition()` la escribía.
        $position = $this->findPosition($id);

        $this->eievaluationp_id = $position->id;
        $this->eievaluationk_id = $position->eievaluationk_id;
        $this->eievaluationp = $position->only([
            'pevaluacion_id', 'fecha', 'nombre_ninos', 'aprendizaje_alcanzado',
            'componente', 'indicadores', 'instrumento', 'observacion', 'order',
        ]);
        $this->listPevaluacion = collect(
            $position->eievaluationk->getPevaluacionsList($this->profesor_id, $position->eievaluationk->lapso_id)
        );
    }

    /**
     * Busca un plan por id garantizando que sea del docente autenticado.
     */
    private function findEvaluation(?int $id): Eievaluationk
    {
        abort_if(! $id, 404, 'Plan no especificado.');

        // `abort(404)` explícito en vez de `findOrFail()`: este último lanza
        // `ModelNotFoundException`, que NO es un `HttpExceptionInterface` y
        // caería en el `catch (\Throwable)` genérico de `openModal()`.
        $plan = Eievaluationk::where('profesor_id', $this->profesor_id)
            ->whereKey($id)
            ->first();

        abort_if(! $plan, 404, 'Este plan no existe o pertenece a otro docente.');

        return $plan;
    }

    /**
     * Busca una posición garantizando que su plan sea del docente autenticado.
     *
     * El alcance se aplica en la query, así que la respuesta es 404 en vez de
     * un 403 que confirmaría que el id existe.
     */
    private function findPosition(?int $id): Eievaluationp
    {
        abort_if(! $id, 404, 'Posición no especificada.');

        $position = Eievaluationp::query()
            ->whereKey($id)
            ->whereHas('eievaluationk', fn ($q) => $q->where('profesor_id', $this->profesor_id))
            ->first();

        abort_if(! $position, 404, 'Esta posición no existe o pertenece a otro docente.');

        return $position;
    }

    // ─── Cambios en cascada de los selects ───────────────────────

    public function updatedEievaluationkGradoId($value): void
    {
        $this->listSeccion = $value ? $this->seccionesDe($value) : collect();
        $this->eievaluationk['seccion_id'] = null;
    }

    public function updatedEievaluationkLapsoId($value): void
    {
        // Cambiar el lapso del plan cambia a qué áreas puede apuntar la
        // posición, así que se recarga la lista.
        if (! $this->eievaluationk_id) {
            $this->listPevaluacion = collect();

            return;
        }

        $plan = Eievaluationk::find($this->eievaluationk_id);
        $this->listPevaluacion = collect(
            $plan ? $plan->getPevaluacionsList($this->profesor_id, $value) : []
        );
    }

    public function updatedFilterGrado(): void
    {
        $this->resetPage();
        $this->filterSeccion = null;
        $this->listSeccion = $this->filterGrado ? $this->seccionesDe($this->filterGrado) : collect();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // ─── CRUD cabecera ───────────────────────────────────────────

    public function save(): void
    {
        $this->resetValidation();

        // El profesor es siempre el autenticado: nunca se toma del formulario.
        $this->eievaluationk['profesor_id'] = $this->profesor_id;

        // `fromInput()` en lugar de `createFrom($this)`: este último está
        // tipeado con `self` (un Request) y un componente Livewire no lo es.
        // `validateResolved()` devuelve void: hay que encadenar sobre el objeto.
        $request = EievaluationkRequest::fromInput($this->eievaluationk);
        $request->validateResolved();
        $validated = $request->planData();

        $plan = $this->eievaluationk_id
            ? $this->findEvaluation($this->eievaluationk_id)
            : new Eievaluationk;

        $plan->fill($validated);
        $plan->save();

        $this->notification()->success(
            title: 'Plan de evaluación guardado',
            description: 'El plan se guardó correctamente.'
        );

        $this->resetModels();
        $this->closeModal();
    }

    public function savePosition(): void
    {
        if (! $this->eievaluationk_id) {
            $this->notification()->error(
                title: 'Error',
                description: 'No se ha seleccionado un plan válido.'
            );

            return;
        }

        $this->resetValidation();

        $this->eievaluationp['eievaluationk_id'] = $this->eievaluationk_id;

        $request = EievaluationpRequest::fromInput($this->eievaluationp);
        $request->validateResolved();
        $data = $request->positionData();

        // En edición se resuelve con `findPosition()` (que aplica el alcance del
        // docente). Un `find()` a secas —como el legacy— dejaba que un id ajeno
        // escribiera sobre la posición de otro docente.
        $position = $this->eievaluationp_id
            ? $this->findPosition($this->eievaluationp_id)
            : new Eievaluationp;

        $position->fill($data);
        $position->save();

        $this->notification()->success(
            title: 'Posición guardada',
            description: 'La posición de evaluación se guardó correctamente.'
        );

        // Se vuelve al gestor con la lista ya fresca.
        $this->resetModelPosition();
        $this->openModal('position', $this->eievaluationk_id);
    }

    // ─── Borrados ────────────────────────────────────────────────

    public function confirmDeletePlan(int $id): void
    {
        $this->dialog()->confirm([
            'title' => 'Eliminar plan de evaluación',
            'description' => 'Se eliminará el plan junto con sus posiciones. Esta acción no se puede deshacer.',
            'icon' => 'warning',
            'accept' => [
                'label' => 'Eliminar',
                'method' => 'deletePlan',
                'params' => [$id],
                'color' => 'red',
            ],
            'reject' => ['label' => 'Cancelar'],
        ]);
    }

    public function deletePlan(int $id): void
    {
        $plan = $this->findEvaluation($id);

        // Sin FK declarada hacia las hijas, se borran explícitamente.
        $plan->eievaluationps()->delete();
        $plan->delete();

        $this->notification()->success(
            title: 'Plan eliminado',
            description: 'El plan de evaluación se eliminó correctamente.'
        );
    }

    public function confirmDeletePosition(int $id): void
    {
        $this->dialog()->confirm([
            'title' => 'Eliminar posición',
            'description' => 'Se eliminará el registro de esta actividad de evaluación.',
            'icon' => 'warning',
            'accept' => [
                'label' => 'Eliminar',
                'method' => 'deletePosition',
                'params' => [$id],
                'color' => 'red',
            ],
            'reject' => ['label' => 'Cancelar'],
        ]);
    }

    public function deletePosition(int $id): void
    {
        // Mismo alcance que en `findEvaluation()`/`findPosition()`.
        $position = $this->findPosition($id);

        abort_if(
            (int) $position->eievaluationk_id !== (int) $this->eievaluationk_id,
            404,
            'Esta posición no pertenece al plan abierto.'
        );

        $position->delete();

        $this->notification()->success(
            title: 'Posición eliminada',
            description: 'La posición se eliminó correctamente.'
        );
    }

    // ─── Resets y utilidades ─────────────────────────────────────

    private function authorizeInicial(): void
    {
        $user = Auth::user();

        abort_if(! $user || (! $user->isInicial() && ! $user->is_admin), 403, 'Acceso denegado al módulo de Educación Inicial.');
    }

    private function loadGrados(): Collection
    {
        // Solo los grados de Educación Inicial (pestudio 6) en los que el
        // docente tiene carga académica.
        if (! $this->profesor_id) {
            return collect();
        }

        return Grado::select('grados.id', 'grados.name')
            ->join('pensums', 'pensums.grado_id', '=', 'grados.id')
            ->join('pevaluacions', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->where('pevaluacions.profesor_id', $this->profesor_id)
            // Solo la carga del LAPSO ACTUAL: las pevaluaciones de lapso > 1 son
            // registros vacíos sin actividades y mostraban grados que el docente
            // no tiene realmente asignados en el periodo en curso.
            ->where('pevaluacions.lapso_id', $this->lapsoActivoId())
            ->where('grados.pestudio_id', config('inicial.pestudio_id'))
            ->whereNull('pevaluacions.deleted_at')
            ->whereNull('pensums.deleted_at')
            ->orderBy('grados.name')
            ->distinct()
            ->get()
            ->pluck('name', 'id');
    }

    private function loadLapsos(): Collection
    {
        return Lapso::orderBy('id')->pluck('name', 'id');
    }

    private function seccionesDe($gradoId): Collection
    {
        if (! $gradoId || ! $this->profesor_id) {
            return collect();
        }

        // Solo las secciones donde el docente tiene CARGA ACADÉMICA (pevaluacion)
        // en ese grado. El legacy mostraba todas las secciones del grado: un
        // docente de Inicial veía la sección de un compañero en su desplegable.
        return Seccion::select('seccions.id', 'seccions.name')
            ->join('pevaluacions', 'pevaluacions.seccion_id', '=', 'seccions.id')
            ->where('pevaluacions.profesor_id', $this->profesor_id)
            ->where('pevaluacions.lapso_id', $this->lapsoActivoId())
            ->where('seccions.grado_id', $gradoId)
            ->whereNull('pevaluacions.deleted_at')
            ->orderBy('seccions.name')
            ->distinct()
            ->pluck('seccions.name', 'seccions.id');
    }

    private function resetModels(): void
    {
        $this->eievaluationk = [
            'profesor_id' => $this->profesor_id,
            'grado_id' => null,
            'lapso_id' => null,
            'seccion_id' => null,
            'finicial' => null,
            'ffinal' => null,
            'observaciones' => null,
            'recomendacion' => null,
            'asistencia' => null,
            'observacion' => null,
        ];

        $this->eievaluationk_id = null;
        $this->resetModelPosition();
    }

    private function resetModelPosition(): void
    {
        $this->eievaluationp = [
            'pevaluacion_id' => null,
            'fecha' => null,
            'nombre_ninos' => null,
            'aprendizaje_alcanzado' => null,
            'componente' => null,
            'indicadores' => null,
            'instrumento' => null,
            'observacion' => null,
            'order' => null,
        ];

        $this->eievaluationp_id = null;
    }

    /** Id del lapso en curso, para filtrar la carga del docente. */
    private function lapsoActivoId(): ?int
    {
        return \App\Models\app\Academy\Lapso::current()?->id;
    }
}
