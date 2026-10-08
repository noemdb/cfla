<?php

namespace App\Livewire\Inicial;

use App\Http\Requests\Inicial\EispecialactRequest;
use App\Http\Requests\Inicial\EispecialkRequest;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Seccion;
use App\Models\app\Inicial\Eiprojectk;
use App\Models\app\Inicial\Eispecialact;
use App\Models\app\Inicial\Eispecialk;
use App\Models\app\Inicial\Eispecialstrategy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

/**
 * CRUD del PLAN ESPECIAL de Educación Inicial.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * GEMELO DE `EiplanningbwkComponent` (quincenal), NO UNA COPIA
 * ─────────────────────────────────────────────────────────────────────────────
 * El plan especial comparte formulario, rejilla de estrategias y gestor por
 * área con la quincenal, así que este componente reproduce su estructura EXACTA
 * —mismos nombres de propiedad y de acción—. Eso es lo que permite compartir el
 * partial del wizard (`livewire/inicial/shared/strategy-wizard`) y el membrete
 * imprimible sin indirecciones ni variables variables.
 *
 * Lo que SÍ es propio de este documento:
 *  · la cabecera justifica con `justificacion` (no con `diagnostico`): el plan
 *    especial no parte de un diagnóstico del grupo, sino de una razón
 *    pedagogógica que hay que argumentar;
 *  · las hijas se llaman **actividades** (`eispecialacts`), no resúmenes, y sin
 *    columna `estrategias` (esa solo existe en `eiprojectsummaries`);
 *  · no tiene bloque de revisión (esa solo existe en el proyecto de aula).
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * BUGS DEL LEGACY CORREGIDOS AQUÍ (doc 03)
 * ─────────────────────────────────────────────────────────────────────────────
 *  · IDOR en `loadPlan`/`loadActivity`/`delete`/`deleteActivity`: los cuatro
 *    usaban `findOrFail($id)` a secas. El listado sí filtraba por
 *    `profesor_id`, pero llegar al modal con un id ajeno cargaba, guardaba o
 *    BORRABA el plan de otro docente. Aquí todo pasa por `findPlan()` /
 *    `findActivity()`.
 *  · `getPevaluacionsList()` devolvía `[]` → `TypeError` al asignarlo a una
 *    propiedad tipada `Collection` (ver el modelo).
 *  · Quirk D3: el texto de la estrategia vive SIEMPRE en la columna `lunes`.
 *  · 403 explícito en `mount()`: el legacy dejaba `profesor_id = null` y el
 *    listado acababa mostrando planes huérfanos en vez de rechazar.
 *  · Momentos indexados 0..9 en `wire:model` (los nombres tienen espacios y dos
 *    puntos, que Livewire 3 lee como separadores de ruta).
 *  · Fechas con datepicker nativo (el legacy usaba `type="text"` y obligaba a
 *    escribirlas a mano).
 */
class EispecialkComponent extends Component
{
    use WireUiActions, WithPagination;

    // ─── Estado del modal ─────────────────────────────────────────

    public bool $showModal = false;

    /**
     * create · edit · view · strategy · summary · edit-activity
     */
    public string $modalType = '';

    public ?int $editingId = null;

    public ?int $eispecialk_id = null;

    public ?int $eispecialact_id = null;

    // ─── Formulario de cabecera ──────────────────────────────────

    public ?int $profesor_id = null;

    public $eispecialk = [];

    public $eispecialact = [];

    // ─── Wizard de estrategias (rejilla 5 días × 10 momentos) ─────

    public array $strategies = [];

    public string $activeDay = 'lunes';

    /**
     * Nombre real del momento activo. Solo para mostrar y persistir.
     */
    public string $activeMoment = 'Recibimiento';

    /**
     * Índice del momento activo dentro de `$moments` (0..9).
     *
     * La vista usa el índice en `wire:model` porque los nombres de momento
     * tienen espacios y dos puntos ("Periodo: Planificación"), que Livewire 3
     * interpretaría como separadores de ruta.
     */
    public int $activeMomentIndex = 0;

    /**
     * Búsqueda de una celda concreta desde la tarjeta (día + momento).
     */
    public function openStrategyCell(int $planId, string $day, string $moment): void
    {
        $this->openModal('strategy', $planId);
        $this->setActiveDay($day);
        $this->setActiveMoment($this->indexForMoment($moment));
    }

    // ─── Filtros del listado ─────────────────────────────────────

    public string $search = '';

    public $filterGrado = '';

    public $filterSeccion = '';

    public int $paginate = 10;

    // ─── Listas para los selects ─────────────────────────────────

    public Collection $listGrado;

    public Collection $listSeccion;

    public Collection $listLapso;

    public Collection $listPevaluacion;

    public Collection $listEiprojectk;

    public ?int $lapso_id = null;

    /**
     * Días de la semana. Las claves coinciden con `day_of_week` y con las
     * columnas de la tabla.
     *
     * Proviene de la constante del modelo para que la rejilla de 50 celdas, la
     * vista imprimible (`format()`) y la BD no puedan divergir: son la MISMA
     * fuente.
     */
    public array $weekDays = Eispecialstrategy::WEEK_DAYS;

    /**
     * Los 10 momentos de la rutina diaria, EN SU ORDEN REAL (no alfabético):
     * el wizard los recorre en esta secuencia.
     */
    public array $moments = Eispecialstrategy::LIST_MOMENT;

    public function mount(): void
    {
        $this->authorizeInicial();

        $profesor = Auth::user()->profesor;
        $this->profesor_id = $profesor?->id;

        $this->listGrado = $this->loadGrados();
        $this->listSeccion = collect();
        $this->listLapso = $this->loadLapsos();
        $this->listPevaluacion = collect();
        $this->listEiprojectk = $this->loadProyectos();

        $this->resetModels();
    }

    public function render()
    {
        $query = Eispecialk::where('profesor_id', $this->profesor_id)
            // Sin relación a un plan: `eispecialks` no tiene columna
            // `eiprojectk_id`, a diferencia de la semanal y la quincenal.
            ->with(['grado', 'seccion']);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('justificacion', 'like', '%'.$this->search.'%')
                    ->orWhere('observacion', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->filterGrado) {
            $query->where('grado_id', $this->filterGrado);
        }

        if ($this->filterSeccion) {
            $query->where('seccion_id', $this->filterSeccion);
        }

        $eispecialks = $query->orderByDesc('created_at')->paginate($this->paginate);

        // Conteo para el encabezado del wizard (estrategias ya escritas).
        $planEstrategias = $this->modalType === 'strategy' && $this->eispecialk_id
            ? Eispecialstrategy::where('eispecialk_id', $this->eispecialk_id)->count()
            : 0;

        return view('livewire.inicial.eispecialk.index', [
            'eispecialks' => $eispecialks,
            'planEstrategias' => $planEstrategias,
            // Etiquetas reindexadas 0..9: `$moments` tiene por clave el NOMBRE
            // del momento, pero la rejilla de `$strategies` y el `wire:model`
            // usan el índice.
            'momentLabels' => array_values($this->moments),
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
                'edit' => $this->loadPlan($id),
                'view' => $this->loadPlan($id),
                'strategy' => $this->loadPlanForStrategy($id),
                'activity' => $this->loadPlanForActivity($id),
                'edit-activity' => $this->loadActivity($id),
                default => $this->resetModels(),
            };
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            // 404/403 (plan o actividad ajeno) NO se tragan: se convertirían en
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
        $this->eispecialk_id = null;
        $this->eispecialact_id = null;
        $this->activeDay = 'lunes';
        $this->activeMomentIndex = 0;
        $this->activeMoment = 'Recibimiento';
        $this->resetValidation();
    }

    // ─── Carga de datos ──────────────────────────────────────────

    private function loadPlan(?int $id): void
    {
        $plan = $this->findPlan($id);

        $this->eispecialk_id = $plan->id;
        $this->eispecialk = $plan->only([
            'profesor_id', 'grado_id', 'seccion_id',
            'finicial', 'ffinal', 'tiempo_ejecucion', 'justificacion', 'observacion',
        ]);

        $this->listSeccion = $this->seccionesDe($plan->grado_id);
    }

    private function loadPlanForStrategy(?int $id): void
    {
        $plan = $this->findPlan($id);
        $this->eispecialk_id = $plan->id;
        $this->loadStrategiesForPlan($plan->id);
    }

    private function loadPlanForActivity(?int $id): void
    {
        $plan = $this->findPlan($id);
        $this->eispecialk_id = $plan->id;
        // `collect()` a la fuerza: `$listPevaluacion` está tipeada como
        // Collection y cualquier retorno array lanzaría un TypeError.
        $this->listPevaluacion = collect($plan->getPevaluacionsList($this->profesor_id));
        $this->resetModelActivity();
    }

    /**
     * Rellena la rejilla completa 5 × 10 con lo que haya en BD.
     *
     * ⚠️ Las claves de `$strategies` son ÍNDICES (0..9), no los nombres de los
     * momentos: los nombres contienen espacios y dos puntos, y Livewire 3
     * resuelve `wire:model` por ruta con puntos.
     */
    private function loadStrategiesForPlan(int $planId): void
    {
        $existentes = Eispecialstrategy::where('eispecialk_id', $planId)
            ->get()
            ->keyBy(fn ($s) => $s->day_of_week.'|'.$s->momento_rutina_diaria);

        $this->strategies = [];

        foreach (array_keys($this->weekDays) as $day) {
            foreach (array_keys($this->moments) as $i => $moment) {
                $existente = $existentes->get($day.'|'.$moment);

                $this->strategies[$day][$i] = [
                    'id' => $existente?->id,
                    // El texto vive en la columna `lunes` (quirk D3); el
                    // accessor `estrategia` lo normaliza en lectura.
                    'estrategia' => $existente?->estrategia ?? '',
                    'order' => $existente?->order,
                ];
            }
        }
    }

    /**
     * Nombre real del momento a partir de su índice en `$this->moments`.
     */
    public function momentForIndex(int|string $index): string
    {
        return array_keys($this->moments)[(int) $index] ?? '';
    }

    /**
     * Índice del momento real. La UI manda el índice (seguro para `wire:model`)
     * y el componente lo traduce antes de tocar la BD.
     */
    public function indexForMoment(string $moment): int
    {
        return (int) array_search($moment, array_keys($this->moments), true);
    }

    private function loadActivity(?int $id): void
    {
        if (! $id) {
            $this->resetModelActivity();

            return;
        }

        $activity = $this->findActivity($id);

        $this->eispecialact_id = $activity->id;
        $this->eispecialk_id = $activity->eispecialk_id;
        $this->eispecialact = $activity->only([
            'pevaluacion_id', 'componente', 'objetivo', 'aprendizaje_esperado',
            'indicadores', 'linea_investigacion', 'enfasis_curriculares', 'order',
        ]);
        $this->listPevaluacion = collect($activity->eispecialk->getPevaluacionsList($this->profesor_id));
    }

    /**
     * Busca un plan por id garantizando que sea del docente autenticado.
     *
     * Sin esta comprobación, un `id` ajeno traería el plan de otro docente tanto
     * al listado de estrategias como a los resúmenes.
     */
    private function findPlan(?int $id): Eispecialk
    {
        abort_if(! $id, 404, 'Plan no especificado.');

        // `abort(404)` explícito en vez de `findOrFail()`: este último lanza
        // `ModelNotFoundException`, que NO es un `HttpExceptionInterface` y
        // caería en el `catch (\Throwable)` genérico de `openModal()`.
        $plan = Eispecialk::where('profesor_id', $this->profesor_id)
            ->whereKey($id)
            ->first();

        abort_if(! $plan, 404, 'Este plan no existe o pertenece a otro docente.');

        return $plan;
    }

    /**
     * Busca un actividad garantizando que su plan sea del docente autenticado.
     *
     * El legacy hacía `Eispecialact::find($id)` sin filtro: abrir
     * `edit-activity` con el id de otro docente cargaba su actividad y `saveActivity`
     * lo escribía; `deleteActivity` lo borraba. Aquí el alcance se aplica en la
     * query, así que la respuesta es 404 en vez de un 403 que confirmaría que el
     * id existe.
     */
    private function findActivity(?int $id): Eispecialact
    {
        abort_if(! $id, 404, 'Actividad no especificado.');

        $activity = Eispecialact::query()
            ->whereKey($id)
            ->whereHas('eispecialk', fn ($q) => $q->where('profesor_id', $this->profesor_id))
            ->first();

        abort_if(! $activity, 404, 'Este actividad no existe o pertenece a otro docente.');

        return $activity;
    }

    // ─── Navegación del wizard ───────────────────────────────────

    public function setActiveDay(string $day): void
    {
        if (! array_key_exists($day, $this->weekDays)) {
            return;
        }

        $this->activeDay = $day;
        $this->activeMomentIndex = 0;
        $this->activeMoment = array_key_first($this->moments);
    }

    /**
     * Selecciona un momento por su ÍNDICE en `$this->moments`.
     */
    public function setActiveMoment(int|string $index): void
    {
        $moment = $this->momentForIndex($index);

        if ($moment === '') {
            return;
        }

        $this->activeMomentIndex = (int) $index;
        $this->activeMoment = $moment;
    }

    public function nextMoment(): void
    {
        if ($this->activeMomentIndex < count($this->moments) - 1) {
            $this->setActiveMoment($this->activeMomentIndex + 1);
        }
    }

    public function previousMoment(): void
    {
        if ($this->activeMomentIndex > 0) {
            $this->setActiveMoment($this->activeMomentIndex - 1);
        }
    }

    /**
     * Progreso de un día: cuántas de las 10 celdas tienen texto.
     *
     * R4: la regla documentada de "≥3 días con contenido" NO bloquea el
     * guardado; este contador la muestra como advertencia.
     *
     * @return array{completed:int,total:int,percentage:int}
     */
    public function getDayProgress(string $day): array
    {
        $completed = 0;
        $total = count($this->moments);

        // Las claves son índices, no nombres de momento.
        foreach (range(0, $total - 1) as $i) {
            if (trim((string) ($this->strategies[$day][$i]['estrategia'] ?? '')) !== '') {
                $completed++;
            }
        }

        return [
            'completed' => $completed,
            'total' => $total,
            'percentage' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
        ];
    }

    /**
     * Días con al menos una celda escrita (para el actividad del wizard).
     *
     * @return array<int, string>
     */
    public function getDaysWithContent(): array
    {
        $dias = [];

        foreach (array_keys($this->weekDays) as $day) {
            if ($this->getDayProgress($day)['completed'] > 0) {
                $dias[] = $day;
            }
        }

        return $dias;
    }

    // ─── Cambios en cascada de los selects ───────────────────────

    public function updatedEispecialkGradoId($value): void
    {
        $this->listSeccion = $value ? $this->seccionesDe($value) : collect();
        $this->eispecialk['seccion_id'] = null;
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

    public function updatedLapsoId($lapso_id): void
    {
        if (! $this->eispecialk_id) {
            $this->listPevaluacion = collect();

            return;
        }

        $plan = Eispecialk::find($this->eispecialk_id);
        $this->listPevaluacion = collect(
            $plan ? $plan->getPevaluacionsList($this->profesor_id, $lapso_id) : []
        );
    }

    // ─── CRUD cabecera ───────────────────────────────────────────

    public function save(): void
    {
        $this->resetValidation();

        // El profesor es siempre el autenticado: nunca se toma del formulario.
        $this->eispecialk['profesor_id'] = $this->profesor_id;

        // `fromInput()` en lugar de `createFrom($this)`: este último está
        // tipeado con `self` (un Request) y un componente Livewire no lo es.
        // `validateResolved()` devuelve void: hay que encadenar sobre el objeto.
        $request = EispecialkRequest::fromInput($this->eispecialk);
        $request->validateResolved();
        $validated = $request->planData();

        $plan = $this->eispecialk_id
            ? $this->findPlan($this->eispecialk_id)
            : new Eispecialk;

        $plan->fill($validated);
        $plan->save();

        $this->notification()->success(
            title: 'Plan especial guardado',
            description: 'El plan se guardó correctamente.'
        );

        $this->resetModels();
        $this->closeModal();
    }

    public function saveActivity(): void
    {
        if (! $this->eispecialk_id) {
            $this->notification()->error(
                title: 'Error',
                description: 'No se ha seleccionado un plan válido.'
            );

            return;
        }

        $this->resetValidation();

        $this->eispecialact['eispecialk_id'] = $this->eispecialk_id;

        $request = EispecialactRequest::fromInput($this->eispecialact);
        $request->validateResolved();
        $data = $request->activityData();

        // En edición se resuelve con `findActivity()` (que aplica el alcance del
        // docente). Un `find()` a secas —como el legacy— dejaba que un id ajeno
        // escribiera sobre el actividad de otro docente.
        $activity = $this->eispecialact_id
            ? $this->findActivity($this->eispecialact_id)
            : new Eispecialact;

        $activity->fill($data);
        $activity->save();

        $this->notification()->success(
            title: 'Actividad guardado',
            description: 'El actividad por área se guardó correctamente.'
        );

        // Se vuelve al gestor de resúmenes con la lista ya fresca.
        $this->resetModelActivity();
        $this->openModal('activity', $this->eispecialk_id);
    }

    // ─── CRUD estrategias ────────────────────────────────────────

    /**
     * Guarda la celda activa y avanza al momento siguiente (flujo del docente).
     */
    public function saveCurrentStrategy(): void
    {
        if (! $this->eispecialk_id) {
            $this->notification()->error(
                title: 'Error',
                description: 'No se ha seleccionado un plan válido.'
            );

            return;
        }

        $i = $this->activeMomentIndex;
        $celda = $this->strategies[$this->activeDay][$i] ?? null;

        if ($celda === null || trim((string) $celda['estrategia']) === '') {
            $this->notification()->error(
                title: 'Estrategia vacía',
                description: 'Escribe la estrategia antes de guardar.'
            );

            return;
        }

        $this->persistStrategy($this->activeDay, $i, $celda);

        $this->notification()->success(
            title: 'Estrategia guardada',
            description: $this->weekDays[$this->activeDay].' · '.$this->activeMoment
        );

        $this->nextMoment();
    }

    /**
     * Guarda las 50 celdas que tengan texto.
     *
     * R4: basta con que UNA celda tenga contenido.
     */
    public function saveStrategies(): void
    {
        if (! $this->eispecialk_id) {
            $this->notification()->error(
                title: 'Error',
                description: 'No se ha seleccionado un plan válido.'
            );

            return;
        }

        $guardadas = 0;

        foreach ($this->strategies as $day => $momentos) {
            foreach ($momentos as $i => $celda) {
                if (trim((string) ($celda['estrategia'] ?? '')) === '') {
                    continue;
                }

                $this->persistStrategy($day, $i, $celda);
                $guardadas++;
            }
        }

        if ($guardadas === 0) {
            $this->notification()->error(
                title: 'Nada que guardar',
                description: 'Debes completar al menos una estrategia.'
            );

            return;
        }

        $this->notification()->success(
            title: 'Estrategias guardadas',
            description: "Se guardaron {$guardadas} celdas."
        );
    }

    /**
     * Escribe una celda (día × momento).
     *
     * El texto se asigna al atributo virtual `estrategia`, que el mutador del
     * modelo traduce a la columna `lunes` (quirk D3). NUNCA se escribe
     * `lunes`…`viernes` directamente.
     *
     * Recibe el ÍNDICE de momento (0..9), igual que la rejilla `$strategies`:
     * escribir de vuelta con el NOMBRE crearía una clave nueva en lugar de
     * actualizar la celda, y el id no se propagaría (cada guardado duplicaría
     * la fila y el borrado posterior no la encontraría).
     */
    private function persistStrategy(string $day, int|string $momentIndex, array $celda): void
    {
        $index = (int) $momentIndex;
        $moment = $this->momentForIndex($index);

        if ($moment === '' || ! array_key_exists($day, $this->weekDays)) {
            return;
        }

        if (! $celda['id']) {
            $strategy = new Eispecialstrategy;
            $strategy->eispecialk_id = $this->eispecialk_id;
            $strategy->day_of_week = $day;
            $strategy->momento_rutina_diaria = $moment;
        } else {
            $strategy = Eispecialstrategy::where('eispecialk_id', $this->eispecialk_id)
                ->whereKey($celda['id'])
                ->first();

            abort_if(! $strategy, 404, 'Esta estrategia ya no existe.');
        }

        $strategy->estrategia = trim((string) $celda['estrategia']);
        $strategy->order = $celda['order'] ?: null;
        $strategy->save();

        // Se actualiza el id en la rejilla para que el siguiente guardado
        // actualice en vez de duplicar.
        $this->strategies[$day][$index]['id'] = $strategy->id;
    }

    /**
     * Borra la estrategia de una celda.
     *
     * Recibe el ÍNDICE del momento (no su nombre) porque así lo emite la UI.
     */
    public function deleteStrategy(string $day, int|string $momentIndex): void
    {
        if (! $this->eispecialk_id || ! array_key_exists($day, $this->weekDays)) {
            return;
        }

        $moment = $this->momentForIndex($momentIndex);

        if ($moment === '') {
            return;
        }

        $celda = $this->strategies[$day][(int) $momentIndex] ?? null;

        if (! $celda || ! $celda['id']) {
            return;
        }

        Eispecialstrategy::where('eispecialk_id', $this->eispecialk_id)
            ->where('day_of_week', $day)
            ->where('momento_rutina_diaria', $moment)
            ->delete();

        $this->strategies[$day][(int) $momentIndex] = [
            'id' => null,
            'estrategia' => '',
            'order' => null,
        ];

        $this->notification()->success(
            title: 'Estrategia eliminada',
            description: $this->weekDays[$day].' · '.$moment
        );
    }

    /**
     * Borrado de una celda con confirmación uniforme.
     */
    public function confirmDeleteStrategy(string $day, int|string $momentIndex): void
    {
        $celda = $this->strategies[$day][(int) $momentIndex] ?? null;

        if (! $celda || ! $celda['id']) {
            return;
        }

        $moment = $this->momentForIndex($momentIndex);

        $this->dialog()->confirm([
            'title' => 'Eliminar estrategia',
            'description' => "Se eliminará la estrategia de {$this->weekDays[$day]} · {$moment}. Esta acción no se puede deshacer.",
            'icon' => 'warning',
            'accept' => [
                'label' => 'Eliminar',
                'method' => 'deleteStrategy',
                'params' => [$day, (int) $momentIndex],
                'color' => 'red',
            ],
            'reject' => ['label' => 'Cancelar'],
        ]);
    }

    // ─── Borrados ────────────────────────────────────────────────

    public function confirmDeletePlan(int $id): void
    {
        $this->dialog()->confirm([
            'title' => 'Eliminar plan especial',
            'description' => 'Se eliminará el plan junto con sus estrategias y actividades. Esta acción no se puede deshacer.',
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
        $plan = $this->findPlan($id);

        // Sin FK declarada hacia las hijas, se borran explícitamente.
        $plan->eispecialstrategies()->delete();
        $plan->activities()->delete();
        $plan->delete();

        $this->notification()->success(
            title: 'Plan eliminado',
            description: 'El plan especial se eliminó correctamente.'
        );
    }

    public function confirmDeleteActivity(int $id): void
    {
        $this->dialog()->confirm([
            'title' => 'Eliminar actividad',
            'description' => 'Se eliminará el actividad de este área de aprendizaje.',
            'icon' => 'warning',
            'accept' => [
                'label' => 'Eliminar',
                'method' => 'deleteActivity',
                'params' => [$id],
                'color' => 'red',
            ],
            'reject' => ['label' => 'Cancelar'],
        ]);
    }

    public function deleteActivity(int $id): void
    {
        // Mismo alcance que en `findPlan()`: el actividad tiene que ser de un plan
        // del docente autenticado.
        $activity = $this->findActivity($id);

        abort_if(
            (int) $activity->eispecialk_id !== (int) $this->eispecialk_id,
            404,
            'Este actividad no pertenece al plan abierto.'
        );

        $activity->delete();

        $this->notification()->success(
            title: 'Actividad eliminado',
            description: 'El actividad se eliminó correctamente.'
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

    private function loadProyectos(): Collection
    {
        return Eiprojectk::getForProfesorIdList($this->profesor_id);
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
        $this->eispecialk = [
            'profesor_id' => $this->profesor_id,
            'grado_id' => null,
            'seccion_id' => null,
            'finicial' => null,
            'ffinal' => null,
            'tiempo_ejecucion' => 1,
            'justificacion' => null,
            'observacion' => null,
        ];

        $this->eispecialk_id = null;
        $this->resetModelActivity();
        $this->resetModelStrategies();
    }

    private function resetModelActivity(): void
    {
        $this->eispecialact = [
            'pevaluacion_id' => null,
            'componente' => null,
            'objetivo' => null,
            'aprendizaje_esperado' => null,
            'indicadores' => null,
            'linea_investigacion' => null,
            'enfasis_curriculares' => null,
            'order' => null,
        ];

        $this->eispecialact_id = null;
    }

    private function resetModelStrategies(): void
    {
        $this->strategies = [];

        foreach (array_keys($this->weekDays) as $day) {
            // Claves por ÍNDICE de momento (ver loadStrategiesForPlan).
            foreach (range(0, count($this->moments) - 1) as $i) {
                $this->strategies[$day][$i] = [
                    'id' => null,
                    'estrategia' => '',
                    'order' => null,
                ];
            }
        }
    }

    /** Id del lapso en curso, para filtrar la carga del docente. */
    private function lapsoActivoId(): ?int
    {
        return \App\Models\app\Academy\Lapso::current()?->id;
    }
}
