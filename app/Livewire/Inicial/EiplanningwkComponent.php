<?php

namespace App\Livewire\Inicial;

use App\Http\Requests\Inicial\EiplanningwkRequest;
use App\Http\Requests\Inicial\EiplanningwsummaryRequest;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Inicial\Eiplanningwk;
use App\Models\app\Inicial\Eiplanningwstrategy;
use App\Models\app\Inicial\Eiplanningwsummary;
use App\Models\app\Inicial\Eiprojectk;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

/**
 * CRUD de la planificación semanal de Educación Inicial (entidad P0).
 *
 * ─────────────────────────────────────────────────────────────────
 * ANATOMÍA DEL COMPONENTE (5 bloques, del doc 04 §2)
 * ─────────────────────────────────────────────────────────────────
 *   1. Listado en tarjetas + filtros (profesor propio / grado / sección)
 *   2. Modal único con `$modalType` (create · edit · view · strategy ·
 *      summary · edit-summary) — el legacy usaba 5 modales distintos con
 *      `include`, aquí hay uno solo
 *   3. Wizard de estrategias: pestañas de día + pills de momento
 *      (5 × 10 = 50 celdas)
 *   4. Gestor de resúmenes por área
 *   5. Acciones de cabecera (guardar, imprimir, eliminar con confirmación)
 *
 * ─────────────────────────────────────────────────────────────────
 * MIGRACIÓN DE LIVEWIRE 2 → 3
 * ─────────────────────────────────────────────────────────────────
 *   dispatchBrowserEvent('swal')  →  $this->notification() / $this->dialog()
 *   wire:model                     →  wire:model.live
 *   $paginationTheme = bootstrap-4 →  Tailwind (por defecto)
 *
 * ─────────────────────────────────────────────────────────────────
 * BUGS DEL LEGACY CORREGIDOS EN EL PUENTE (blueprint doc 03)
 * ─────────────────────────────────────────────────────────────────
 *   · `deleteStrategy($day, $moment)` con firma consistente — en el legacy el
 *     evento `delete-strategy` llamaba a `deleteStrategy($id)`, firma
 *     incompatible, y el borrado era un no-op silencioso.
 *   · La estrategia se reabre en su celda día/momento correcta: se localiza
 *     por `day_of_week` + `momento_rutina_diaria`, no por id suelto.
 *   · 403 explícito: si el usuario no es docente de Inicial, no entra en
 *     silencio sino con abort(403).
 */
class EiplanningwkComponent extends Component
{
    use WireUiActions, WithPagination;

    // ─── Estado del modal ─────────────────────────────────────────

    public bool $showModal = false;

    /**
     * create · edit · view · strategy · summary · edit-summary
     */
    public string $modalType = '';

    public ?int $editingId = null;

    public ?int $eiplanningwk_id = null;

    public ?int $eiplanningwsummary_id = null;

    // ─── Formulario de cabecera ──────────────────────────────────

    public ?int $profesor_id = null;

    public $eiplanningwk = [];

    public $eiplanningwsummary = [];

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
        // `setActiveMoment()` espera el ÍNDICE, no el nombre: pasarle el nombre
        // hacía `momentForIndex('Recibimiento')` → '' → la celda nunca se
        // enfocaba y el docente caía siempre en el primer momento del día.
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
     * columnas de la tabla; el orden es el de la semana escolar.
     *
     * Proviene de la constante del modelo para que la rejilla de 50 celdas, la
     * vista imprimible (`format()`) y la BD no puedan divergir: son la MISMA
     * fuente. Si mañana se añade un sexto día, cambia en un solo sitio.
     */
    public array $weekDays = Eiplanningwstrategy::WEEK_DAYS;

    /**
     * Los 10 momentos de la rutina diaria, EN SU ORDEN REAL (no alfabético):
     * el wizard los recorre en este secuencia.
     *
     * Mismo motivo que `$weekDays`: única fuente de verdad, compartida con
     * `Eiplanningwstrategy::LIST_MOMENT` (que es lo que imprime el formato).
     */
    public array $moments = Eiplanningwstrategy::LIST_MOMENT;

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
        $query = Eiplanningwk::where('profesor_id', $this->profesor_id)
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

        $eiplanningwks = $query->orderByDesc('created_at')->paginate($this->paginate);

        // Conteos para el encabezado del wizard (estrategias ya escritas).
        $planEstrategias = $this->modalType === 'strategy' && $this->eiplanningwk_id
            ? Eiplanningwstrategy::where('eiplanningwk_id', $this->eiplanningwk_id)->count()
            : 0;

        return view('livewire.inicial.eiplanningwk.index', [
            'eiplanningwks' => $eiplanningwks,
            'planEstrategias' => $planEstrategias,
            // Etiquetas reindexadas 0..9: `$moments` tiene por clave el NOMBRE
            // del momento (con espacios y dos puntos), pero la rejilla de
            // `$strategies` y el `wire:model` usan el índice. Sin este array la
            // vista tendría que cruzar dos sistemas de claves a mano.
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
            // 404/403 (plan inexistente o de otro docente) NO se tragan: se
            // convierten en un "Error al cargar los datos" silencioso y el
            // docente no sabe por qué su plan no abre. Se cierra el modal y se
            // propaga.
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
        $this->eiplanningwk_id = null;
        $this->eiplanningwsummary_id = null;
        $this->activeDay = 'lunes';
        $this->activeMomentIndex = 0;
        $this->activeMoment = 'Recibimiento';
        $this->resetValidation();
    }

    // ─── Carga de datos ──────────────────────────────────────────

    private function loadPlan(?int $id): void
    {
        $plan = $this->findPlan($id);

        $this->eiplanningwk_id = $plan->id;
        $this->eiplanningwk = $plan->only([
            'profesor_id', 'grado_id', 'seccion_id', 'eiprojectk_id',
            'finicial', 'ffinal', 'tiempo_ejecucion', 'diagnostico', 'observacion',
        ]);

        $this->listSeccion = $this->seccionesDe($plan->grado_id);
    }

    private function loadPlanForStrategy(?int $id): void
    {
        $plan = $this->findPlan($id);
        $this->eiplanningwk_id = $plan->id;
        $this->loadStrategiesForPlan($plan->id);
    }

    private function loadPlanForSummary(?int $id): void
    {
        $plan = $this->findPlan($id);
        $this->eiplanningwk_id = $plan->id;
        // `collect()` a la fuerza: `$listPevaluacion` está tipeada como
        // Collection y cualquier retorno array provocaba un TypeError dentro
        // del `catch` de `openModal()`, cerrando el modal en silencio.
        $this->listPevaluacion = collect($plan->getPevaluacionsList($this->profesor_id));
        $this->resetModelSummary();
    }

    /**
     * Rellena la rejilla completa 5 × 10 con lo que haya en BD, dejando las
     * celdas vacías listas para escribir.
     *
     * ⚠️ Las claves de `$strategies` son ÍNDICES (0..9), no los nombres de los
     * momentos. Los momentos contienen espacios y dos puntos ("Periodo:
     * Planificación"), y Livewire 3 resuelve `wire:model` por ruta con puntos:
     * usarlos como clave haría que `strategies.lunes.Periodo: Planificación.
     * estrategia` se interpretara como una clave anidada rota.
     *
     * El índice es estable porque `$this->moments` no se reordena; la
     * traducción índice → momento real ocurre en {@see momentForIndex()}.
     */
    private function loadStrategiesForPlan(int $planId): void
    {
        $existentes = Eiplanningwstrategy::where('eiplanningwk_id', $planId)
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

        // Con alcance por `findSummary()`: `Eiplanningwsummary::find($id)` —como
        // en el legacy— cargaba el resumen de otro docente con solo conocer su
        // id, y `saveSummary()` lo escribía encima.
        $summary = $this->findSummary($id);

        $this->eiplanningwsummary_id = $summary->id;
        $this->eiplanningwk_id = $summary->eiplanningwk_id;
        $this->eiplanningwsummary = $summary->only([
            'pevaluacion_id', 'componente', 'objetivo', 'aprendizaje_esperado',
            'indicadores', 'linea_investigacion', 'enfasis_curriculares', 'order',
        ]);
        $this->listPevaluacion = collect($summary->eiplanningwk->getPevaluacionsList($this->profesor_id));
    }

    /**
     * Busca un plan por id garantizando que sea del docente autenticado.
     *
     * Sin esta comprobación, un `id` ajeno traería el plan de otro docente
     * tanto al listado de estrategias como a los resúmenes.
     */
    private function findPlan(?int $id): Eiplanningwk
    {
        abort_if(! $id, 404, 'Plan no especificado.');

        // `abort(404)` explícito en vez de `findOrFail()`: este último lanza
        // `ModelNotFoundException`, que NO es un `HttpExceptionInterface` y
        // caería en el `catch (\Throwable)` genérico de `openModal()`,
        // convirtiéndose en una notificación silenciosa en lugar de un 404.
        $plan = Eiplanningwk::where('profesor_id', $this->profesor_id)
            ->whereKey($id)
            ->first();

        abort_if(! $plan, 404, 'Este plan no existe o pertenece a otro docente.');

        return $plan;
    }

    /**
     * Busca un resumen garantizando que su plan sea del docente autenticado.
     *
     * El alcance se aplica en la query, así que la respuesta es 404 en vez de
     * un 403 que confirmaría que el id existe. Es el mismo Arrangement que en
     * {@see EiplanningbwkComponent::findSummary()}.
     */
    private function findSummary(?int $id): Eiplanningwsummary
    {
        abort_if(! $id, 404, 'Resumen no especificado.');

        $summary = Eiplanningwsummary::query()
            ->whereKey($id)
            ->whereHas('eiplanningwk', fn ($q) => $q->where('profesor_id', $this->profesor_id))
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
     *
     * La UI nunca manda el nombre del momento: los nombres tienen espacios y
     * dos puntos, que rompen las rutas de `wire:model`. Se guarda el nombre
     * real en `$activeMoment` (que solo se usa para mostrar y persistir) y el
     * índice en `$activeMomentIndex` (que es lo que usa la vista).
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

        // Las claves son índices, no nombres de momento (ver loadStrategiesForPlan).
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

    public function updatedEiplanningwkGradoId($value): void
    {
        $this->listSeccion = $value ? $this->seccionesDe($value) : collect();
        $this->eiplanningwk['seccion_id'] = null;
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
        if (! $this->eiplanningwk_id) {
            $this->listPevaluacion = collect();

            return;
        }

        $plan = Eiplanningwk::find($this->eiplanningwk_id);
        $this->listPevaluacion = collect(
            $plan ? $plan->getPevaluacionsList($this->profesor_id, $lapso_id) : []
        );
    }

    // ─── CRUD cabecera ───────────────────────────────────────────

    public function save(): void
    {
        $this->resetValidation();

        // El profesor es siempre el autenticado: nunca se toma del formulario.
        $this->eiplanningwk['profesor_id'] = $this->profesor_id;

        // `fromInput()` en lugar de `createFrom($this)`: este último está
        // tipeado con `self` (un Request) y un componente Livewire no lo es.
        // Ver InicialRequest::fromInput().
        //
        // `validateResolved()` devuelve void (no `$this`): hay que encadenar
        // sobre el objeto, no sobre el resultado.
        $request = EiplanningwkRequest::fromInput($this->eiplanningwk);
        $request->validateResolved();
        $validated = $request->planData();

        $plan = $this->eiplanningwk_id
            ? $this->findPlan($this->eiplanningwk_id)
            : new Eiplanningwk;

        $plan->fill($validated);
        $plan->save();

        $this->notification()->success(
            title: 'Plan semanal guardado',
            description: 'El plan se guardó correctamente.'
        );

        $this->resetModels();
        $this->closeModal();
    }

    public function saveSummary(): void
    {
        if (! $this->eiplanningwk_id) {
            $this->notification()->error(
                title: 'Error',
                description: 'No se ha seleccionado un plan válido.'
            );

            return;
        }

        $this->resetValidation();

        $this->eiplanningwsummary['eiplanningwk_id'] = $this->eiplanningwk_id;

        $request = EiplanningwsummaryRequest::fromInput($this->eiplanningwsummary);
        $request->validateResolved();
        $data = $request->summaryData();

        $summary = $this->eiplanningwsummary_id
            ? $this->findSummary($this->eiplanningwsummary_id)
            : new Eiplanningwsummary;

        $summary->fill($data);
        $summary->save();

        $this->notification()->success(
            title: 'Resumen guardado',
            description: 'El resumen por área se guardó correctamente.'
        );

        // Se vuelve al gestor de resúmenes con la lista ya fresca.
        $this->resetModelSummary();
        $this->openModal('summary', $this->eiplanningwk_id);
    }

    // ─── CRUD estrategias ────────────────────────────────────────

    /**
     * Guarda la celda activa y avanza al momento siguiente (flujo del docente).
     */
    public function saveCurrentStrategy(): void
    {
        if (! $this->eiplanningwk_id) {
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
        if (! $this->eiplanningwk_id) {
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
     * modelo translates a la columna `lunes` (quirk D3). NUNCA se escribe
     * `lunes`…`viernes` directamente.
     *
     * Recibe el ÍNDICE de momento (0..9), igual que la rejilla `$strategies`.
     * Escribir de vuelta con el NOMBRE crearía una clave nueva —
     * `$strategies['lunes']['Periodo: Planificación']` — en lugar de actualizar
     * la celda, y el id no se propagaría: cada guardado duplicaría la fila y
     * el borrado posterior no la encontraría.
     */
    private function persistStrategy(string $day, int|string $momentIndex, array $celda): void
    {
        $index = (int) $momentIndex;
        $moment = $this->momentForIndex($index);

        if ($moment === '' || ! array_key_exists($day, $this->weekDays)) {
            return;
        }

        if (! $celda['id']) {
            $strategy = new Eiplanningwstrategy;
            $strategy->eiplanningwk_id = $this->eiplanningwk_id;
            $strategy->day_of_week = $day;
            $strategy->momento_rutina_diaria = $moment;
        } else {
            $strategy = Eiplanningwstrategy::where('eiplanningwk_id', $this->eiplanningwk_id)
                ->findOrFail($celda['id']);
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
     * Recibe el ÍNDICE del momento (no su nombre) porque así lo emite la UI;
     * se traduce antes de consultar la BD. La firma es coherente en toda la
     * UI — el bug del legacy era que el evento `delete-strategy` pasaba un id a
     * un método que esperaba (día, momento), dejando el borrado como no-op.
     */
    public function deleteStrategy(string $day, int|string $momentIndex): void
    {
        if (! $this->eiplanningwk_id) {
            return;
        }

        if (! array_key_exists($day, $this->weekDays)) {
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

        Eiplanningwstrategy::where('eiplanningwk_id', $this->eiplanningwk_id)
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
     * Borrado de una celda con confirmación uniforme (el legacy no confirmaba
     * nada; el `confirm()` inline tampoco alcanza en Livewire 3).
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
            'title' => 'Eliminar plan semanal',
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

        // Las hijas caen por ON DELETE CASCADE del DDL clonado; los resúmenes
        // y estrategias de la semanal no tienen FK declarada, se borran aquí.
        $plan->eiplanningwstrategies()->delete();
        $plan->eiplanningwsummaries()->delete();
        $plan->delete();

        $this->notification()->success(
            title: 'Plan eliminado',
            description: 'El plan semanal se eliminó correctamente.'
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
        // Mismo alcance que en `findPlan()`/`findSummary()`: `abort(404)`
        // explícito para que el 404 llegue como HttpException y no se coma en
        // el catch genérico de `openModal()`.
        $summary = $this->findSummary($id);

        abort_if(
            (int) $summary->eiplanningwk_id !== (int) $this->eiplanningwk_id,
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
        $this->eiplanningwk = [
            'profesor_id' => $this->profesor_id,
            'grado_id' => null,
            'seccion_id' => null,
            'eiprojectk_id' => null,
            'finicial' => null,
            'ffinal' => null,
            'tiempo_ejecucion' => 1,
            'diagnostico' => null,
            'observacion' => null,
        ];

        $this->eiplanningwk_id = null;
        $this->resetModelSummary();
        $this->resetModelStrategies();
    }

    private function resetModelSummary(): void
    {
        $this->eiplanningwsummary = [
            'pevaluacion_id' => null,
            'componente' => null,
            'objetivo' => null,
            'aprendizaje_esperado' => null,
            'indicadores' => null,
            'linea_investigacion' => null,
            'enfasis_curriculares' => null,
            'order' => null,
        ];

        $this->eiplanningwsummary_id = null;
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
