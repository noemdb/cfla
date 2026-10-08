<?php

namespace App\Livewire\Inicial;

use App\Http\Requests\Inicial\EiplanningbwkRequest;
use App\Http\Requests\Inicial\EiplanningbwsummaryRequest;
use App\Livewire\Inicial\Concerns\ImportaDocumento;
use App\Livewire\Inicial\Concerns\PensumCabecera;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Seccion;
use App\Models\app\Inicial\Eiplanningbwk;
use App\Models\app\Inicial\Eiplanningbwstrategy;
use App\Models\app\Inicial\Eiplanningbwsummary;
use App\Models\app\Inicial\Eiprojectk;
use App\Services\Inicial\ImportadorEiplanningbwk;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

/**
 * CRUD de la planificación QUINCENAL de Educación Inicial (entidad P0).
 *
 * ─────────────────────────────────────────────────────────────────
 * GEMELO DE `EiplanningwkComponent`, NO UNA COPIA
 * ─────────────────────────────────────────────────────────────────
 * El documento quincenal repite formulario, rejilla de estrategias y resúmenes
 * de la semanal, así que este componente reproduce su estructura EXACTA —
 * mismos nombres de propiedad y de acción—. Eso es lo que permite compartir el
 * partial del wizard (`livewire/inicial/shared/strategy-wizard`) y el membrete
 * imprimible sin indirecciones ni variables variables.
 *
 * Cuándo DEBEN divergir (no heredar):
 *  · la quincenal tiene columna `description` en su estrategia; la semanal no;
 *  · cada documento tiene su FormRequest, para endurecer uno sin arrastrar al
 *    otro;
 *  · los mensajes y títulos dicen "quincenal", no "semanal".
 *
 * ─────────────────────────────────────────────────────────────────
 * BUGS DEL LEGACY CORREGIDOS AQUÍ (doc 03)
 * ─────────────────────────────────────────────────────────────────
 *  · IDOR en `loadPlan`/`loadSummary`/`delete`/`deleteSummary`: los cuatro
 *    usaban `findOrFail($id)` a secas. El listado sí filtraba por
 *    `profesor_id`, pero llegar al modal con un id ajeno cargaba, guardaba o
 *    BORRABA el plan de otro docente. Aquí todo pasa por `findPlan()` /
 *    `findSummary()`.
 *  · `getPevaluacionsList()` devolvía `[]` y reventaba con `TypeError` al
 *    asignarse a una propiedad tipada `Collection` (ver el modelo).
 *  · Quirk D3: el texto de la estrategia vive SIEMPRE en la columna `lunes`.
 *  · 403 explícito en `mount()`: el legacy dejaba `profesor_id = null` y el
 *    listado acababa mostrando planes huérfanos en vez de rechazar.
 *  · Momentos indexados 0..9 en `wire:model` (los nombres tienen espacios y
 *    dos puntos).
 */
class EiplanningbwkComponent extends Component
{
    use ImportaDocumento, PensumCabecera, WireUiActions, WithPagination;

    /** Modo de vista del listado: `grid` (tarjetas) o `table` (tabla). */
    public string $viewMode = 'grid';

    /** Alterna tarjetas ↔ tabla. */
    public function toggleView(): void
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'table' : 'grid';
    }

    /** Instancia del asistente de importación del documento. */
    protected function importador(): ImportadorEiplanningbwk
    {
        return new ImportadorEiplanningbwk;
    }

    // ─── Estado del modal ─────────────────────────────────────────

    public bool $showModal = false;

    /**
     * create · edit · view · strategy · summary · edit-summary
     */
    public string $modalType = '';

    public ?int $editingId = null;

    public ?int $eiplanningbwk_id = null;

    public ?int $eiplanningbwsummary_id = null;

    // ─── Formulario de cabecera ──────────────────────────────────

    public ?int $profesor_id = null;

    public $eiplanningbwk = [];

    public $eiplanningbwsummary = [];

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

    public $filterPensum = '';

    /** Áreas para el FILTRO del listado (no confundir con `listPensum` del form). */
    public Collection $listPensumFiltro;

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
    public array $weekDays = Eiplanningbwstrategy::WEEK_DAYS;

    /**
     * Los 10 momentos de la rutina diaria, EN SU ORDEN REAL (no alfabético):
     * el wizard los recorre en esta secuencia.
     */
    public array $moments = Eiplanningbwstrategy::LIST_MOMENT;

    public function mount(): void
    {
        $this->authorizeInicial();

        $profesor = Auth::user()->profesor;
        $this->profesor_id = $profesor?->id;

        $this->listGrado = $this->loadGrados();
        $this->listSeccion = collect();
        $this->listLapso = $this->loadLapsos();
        $this->listPevaluacion = collect();
        $this->listPensum = $this->loadPensums();
        $this->listPensumFiltro = $this->loadPensums();
        $this->listEiprojectk = $this->loadProyectos();

        $this->importCandidatos = collect();

        $this->resetModels();
    }

    public function render()
    {
        $query = Eiplanningbwk::where('profesor_id', $this->profesor_id)
            ->with(['grado', 'seccion', 'eiprojectk']);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('diagnostico', 'like', '%'.$this->search.'%')
                    ->orWhere('observacion', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->filterGrado) {
            $query->where('grado_id', $this->filterGrado);
        }

        if ($this->filterSeccion) {
            $query->where('seccion_id', $this->filterSeccion);
        }

        if ($this->filterPensum) {
            $query->where('pensum_id', $this->filterPensum);
        }

        $eiplanningbwks = $query->orderByDesc('created_at')->paginate($this->paginate);

        // Conteo para el encabezado del wizard (estrategias ya escritas).
        $planEstrategias = $this->modalType === 'strategy' && $this->eiplanningbwk_id
            ? Eiplanningbwstrategy::where('eiplanningbwk_id', $this->eiplanningbwk_id)->count()
            : 0;

        return view('livewire.inicial.eiplanningbwk.index', [
            'eiplanningbwks' => $eiplanningbwks,
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
                'summary' => $this->loadPlanForSummary($id),
                'edit-summary' => $this->loadSummary($id),
                default => $this->resetModels(),
            };
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            // 404/403 (plan o resumen ajeno) NO se tragan: se convertirían en
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
        $this->eiplanningbwk_id = null;
        $this->eiplanningbwsummary_id = null;
        $this->activeDay = 'lunes';
        $this->activeMomentIndex = 0;
        $this->activeMoment = 'Recibimiento';
        $this->resetValidation();
    }

    // ─── Carga de datos ──────────────────────────────────────────

    private function loadPlan(?int $id): void
    {
        $plan = $this->findPlan($id);

        $this->eiplanningbwk_id = $plan->id;
        $this->eiplanningbwk = $plan->only([
            'profesor_id', 'grado_id', 'seccion_id', 'pensum_id', 'eiprojectk_id',
            'finicial', 'ffinal', 'tiempo_ejecucion', 'diagnostico', 'observacion',
        ]);
        // Los `date` del modelo llegan como Carbon (`2026-10-05 00:00:00`):
        // el `input[type=date]` lo rechaza como valor inválido, muestra vacío
        // y en el siguiente roundtrip vuelve `null`. Se normaliza a `Y-m-d`.
        $this->eiplanningbwk['finicial'] = $this->fechaInput($this->eiplanningbwk['finicial'] ?? null);
        $this->eiplanningbwk['ffinal'] = $this->fechaInput($this->eiplanningbwk['ffinal'] ?? null);

        $this->listSeccion = $this->seccionesDe($plan->grado_id);
        $this->listPensum = $this->loadPensums($plan->grado_id);
    }

    private function loadPlanForStrategy(?int $id): void
    {
        $plan = $this->findPlan($id);
        $this->eiplanningbwk_id = $plan->id;
        $this->loadStrategiesForPlan($plan->id);
    }

    private function loadPlanForSummary(?int $id): void
    {
        $plan = $this->findPlan($id);
        $this->eiplanningbwk_id = $plan->id;
        // `collect()` a la fuerza: `$listPevaluacion` está tipeada como
        // Collection y cualquier retorno array lanzaría un TypeError.
        $this->listPevaluacion = collect($plan->getPevaluacionsList($this->profesor_id));
        $this->resetModelSummary();
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
        $existentes = Eiplanningbwstrategy::where('eiplanningbwk_id', $planId)
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

    private function loadSummary(?int $id): void
    {
        if (! $id) {
            $this->resetModelSummary();

            return;
        }

        $summary = $this->findSummary($id);

        $this->eiplanningbwsummary_id = $summary->id;
        $this->eiplanningbwk_id = $summary->eiplanningbwk_id;
        $this->eiplanningbwsummary = $summary->only([
            'pevaluacion_id', 'componente', 'objetivo', 'aprendizaje_esperado',
            'indicadores', 'linea_investigacion', 'enfasis_curriculares', 'order',
        ]);
        $this->listPevaluacion = collect($summary->eiplanningbwk->getPevaluacionsList($this->profesor_id));
    }

    /**
     * Busca un plan por id garantizando que sea del docente autenticado.
     *
     * Sin esta comprobación, un `id` ajeno traería el plan de otro docente tanto
     * al listado de estrategias como a los resúmenes.
     */
    private function findPlan(?int $id): Eiplanningbwk
    {
        abort_if(! $id, 404, 'Plan no especificado.');

        // `abort(404)` explícito en vez de `findOrFail()`: este último lanza
        // `ModelNotFoundException`, que NO es un `HttpExceptionInterface` y
        // caería en el `catch (\Throwable)` genérico de `openModal()`.
        $plan = Eiplanningbwk::where('profesor_id', $this->profesor_id)
            ->whereKey($id)
            ->first();

        abort_if(! $plan, 404, 'Este plan no existe o pertenece a otro docente.');

        return $plan;
    }

    /**
     * Busca un resumen garantizando que su plan sea del docente autenticado.
     *
     * El legacy hacía `Eiplanningbwsummary::find($id)` sin filtro: abrir
     * `edit-summary` con el id de otro docente cargaba su resumen y `saveSummary`
     * lo escribía; `deleteSummary` lo borraba. Aquí el alcance se aplica en la
     * query, así que la respuesta es 404 en vez de un 403 que confirmaría que el
     * id existe.
     */
    private function findSummary(?int $id): Eiplanningbwsummary
    {
        abort_if(! $id, 404, 'Resumen no especificado.');

        $summary = Eiplanningbwsummary::query()
            ->whereKey($id)
            ->whereHas('eiplanningbwk', fn ($q) => $q->where('profesor_id', $this->profesor_id))
            ->first();

        abort_if(! $summary, 404, 'Este resumen no existe o pertenece a otro docente.');

        return $summary;
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
     * Días con al menos una celda escrita (para el resumen del wizard).
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

    public function updatedEiplanningbwkGradoId($value): void
    {
        $this->listSeccion = $value ? $this->seccionesDe($value) : collect();
        $this->eiplanningbwk['seccion_id'] = null;
        $this->listPensum = $this->loadPensums($value);
        $this->eiplanningbwk['pensum_id'] = null;
    }

    public function updatedFilterGrado(): void
    {
        $this->resetPage();
        $this->filterSeccion = null;
        $this->listSeccion = $this->filterGrado ? $this->seccionesDe($this->filterGrado) : collect();
        $this->filterPensum = '';
        $this->listPensumFiltro = $this->loadPensums($this->filterGrado ?: null);
    }

    public function updatedFilterPensum(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLapsoId($lapso_id): void
    {
        if (! $this->eiplanningbwk_id) {
            $this->listPevaluacion = collect();

            return;
        }

        $plan = Eiplanningbwk::find($this->eiplanningbwk_id);
        $this->listPevaluacion = collect(
            $plan ? $plan->getPevaluacionsList($this->profesor_id, $lapso_id) : []
        );
    }

    // ─── CRUD cabecera ───────────────────────────────────────────

    public function save(): void
    {
        $this->resetValidation();

        // El profesor es siempre el autenticado: nunca se toma del formulario.
        $this->eiplanningbwk['profesor_id'] = $this->profesor_id;

        // `fromInput()` en lugar de `createFrom($this)`: este último está
        // tipeado con `self` (un Request) y un componente Livewire no lo es.
        // `validateResolved()` devuelve void: hay que encadenar sobre el objeto.
        $request = EiplanningbwkRequest::fromInput($this->eiplanningbwk);
        $request->validateResolved();
        $validated = $request->planData();

        // El área (pensum) elegida debe ser de una carga del docente.
        if (! empty($validated['pensum_id']) && ! $this->loadPensums($validated['grado_id'] ?? null)->has($validated['pensum_id'])) {
            $this->addError('eiplanningbwk.pensum_id', 'El área de aprendizaje elegida no pertenece a este docente.');

            return;
        }

        $plan = $this->eiplanningbwk_id
            ? $this->findPlan($this->eiplanningbwk_id)
            : new Eiplanningbwk;

        $plan->fill($validated);
        $plan->save();

        $this->notification()->success(
            title: 'Plan quincenal guardado',
            description: 'El plan se guardó correctamente.'
        );

        $this->resetModels();
        $this->closeModal();
    }

    public function saveSummary(): void
    {
        if (! $this->eiplanningbwk_id) {
            $this->notification()->error(
                title: 'Error',
                description: 'No se ha seleccionado un plan válido.'
            );

            return;
        }

        $this->resetValidation();

        $this->eiplanningbwsummary['eiplanningbwk_id'] = $this->eiplanningbwk_id;

        $request = EiplanningbwsummaryRequest::fromInput($this->eiplanningbwsummary);
        $request->validateResolved();
        $data = $request->summaryData();

        // En edición se resuelve con `findSummary()` (que aplica el alcance del
        // docente). Un `find()` a secas —como el legacy— dejaba que un id ajeno
        // escribiera sobre el resumen de otro docente.
        $summary = $this->eiplanningbwsummary_id
            ? $this->findSummary($this->eiplanningbwsummary_id)
            : new Eiplanningbwsummary;

        $summary->fill($data);
        $summary->save();

        $this->notification()->success(
            title: 'Resumen guardado',
            description: 'El resumen por área se guardó correctamente.'
        );

        // Se vuelve al gestor de resúmenes con la lista ya fresca.
        $this->resetModelSummary();
        $this->openModal('summary', $this->eiplanningbwk_id);
    }

    // ─── CRUD estrategias ────────────────────────────────────────

    /**
     * Guarda la celda activa y avanza al momento siguiente (flujo del docente).
     */
    public function saveCurrentStrategy(): void
    {
        if (! $this->eiplanningbwk_id) {
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
        if (! $this->eiplanningbwk_id) {
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
            $strategy = new Eiplanningbwstrategy;
            $strategy->eiplanningbwk_id = $this->eiplanningbwk_id;
            $strategy->day_of_week = $day;
            $strategy->momento_rutina_diaria = $moment;
        } else {
            $strategy = Eiplanningbwstrategy::where('eiplanningbwk_id', $this->eiplanningbwk_id)
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
        if (! $this->eiplanningbwk_id || ! array_key_exists($day, $this->weekDays)) {
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

        Eiplanningbwstrategy::where('eiplanningbwk_id', $this->eiplanningbwk_id)
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
            'title' => 'Eliminar plan quincenal',
            'description' => 'Se eliminará el plan junto con sus estrategias y resúmenes. Esta acción no se puede deshacer.',
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
        $plan->eiplanningbwstrategies()->delete();
        $plan->eiplanningbwsummaries()->delete();
        $plan->delete();

        $this->notification()->success(
            title: 'Plan eliminado',
            description: 'El plan quincenal se eliminó correctamente.'
        );
    }

    public function confirmDeleteSummary(int $id): void
    {
        $this->dialog()->confirm([
            'title' => 'Eliminar resumen',
            'description' => 'Se eliminará el resumen de este área de aprendizaje.',
            'icon' => 'warning',
            'accept' => [
                'label' => 'Eliminar',
                'method' => 'deleteSummary',
                'params' => [$id],
                'color' => 'red',
            ],
            'reject' => ['label' => 'Cancelar'],
        ]);
    }

    public function deleteSummary(int $id): void
    {
        // Mismo alcance que en `findPlan()`: el resumen tiene que ser de un plan
        // del docente autenticado.
        $summary = $this->findSummary($id);

        abort_if(
            (int) $summary->eiplanningbwk_id !== (int) $this->eiplanningbwk_id,
            404,
            'Este resumen no pertenece al plan abierto.'
        );

        $summary->delete();

        $this->notification()->success(
            title: 'Resumen eliminado',
            description: 'El resumen se eliminó correctamente.'
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
        $this->eiplanningbwk = [
            'profesor_id' => $this->profesor_id,
            'grado_id' => null,
            'seccion_id' => null,
            'pensum_id' => null,
            'eiprojectk_id' => null,
            'finicial' => null,
            'ffinal' => null,
            'tiempo_ejecucion' => 1,
            'diagnostico' => null,
            'observacion' => null,
        ];

        $this->eiplanningbwk_id = null;
        $this->resetModelSummary();
        $this->resetModelStrategies();
    }

    private function resetModelSummary(): void
    {
        $this->eiplanningbwsummary = [
            'pevaluacion_id' => null,
            'componente' => null,
            'objetivo' => null,
            'aprendizaje_esperado' => null,
            'indicadores' => null,
            'linea_investigacion' => null,
            'enfasis_curriculares' => null,
            'order' => null,
        ];

        $this->eiplanningbwsummary_id = null;
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

    /**
     * Normaliza un valor de fecha para `input[type=date]` (`Y-m-d` o null).
     */
    private function fechaInput(mixed $valor): ?string
    {
        if ($valor instanceof \DateTimeInterface) {
            return $valor->format('Y-m-d');
        }

        if (is_string($valor) && substr($valor, 0, 10) !== '') {
            return substr($valor, 0, 10);
        }

        return null;
    }
}
