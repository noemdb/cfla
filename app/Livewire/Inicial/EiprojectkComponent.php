<?php

namespace App\Livewire\Inicial;

use App\Http\Requests\Inicial\EiprojectkRequest;
use App\Http\Requests\Inicial\EiprojectreviewRequest;
use App\Http\Requests\Inicial\EiprojectsummaryRequest;
use App\Livewire\Inicial\Concerns\ImportaDocumento;
use App\Livewire\Inicial\Concerns\PensumCabecera;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Seccion;
use App\Models\app\Inicial\Eiprojectk;
use App\Models\app\Inicial\Eiprojectkstrategy;
use App\Models\app\Inicial\Eiprojectreview;
use App\Models\app\Inicial\Eiprojectsummary;
use App\Services\Inicial\ImportadorEiprojectk;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

/**
 * CRUD del PROYECTO DE AULA de Educación Inicial (entidad P0).
 *
 * ─────────────────────────────────────────────────────────────────
 * GEMELO DE `EiplanningbwkComponent`, MÁS EL BLOQUE DE REVISIÓN
 * ─────────────────────────────────────────────────────────────────
 * Comparte con la semanal y la quincenal la estructura exacta —mismos nombres de
 * propiedad y de acción—, y eso es lo que permite compartir el partial del
 * wizard (`livewire/inicial/shared/strategy-wizard`).
 *
 * Lo que NO es gemelo: el proyecto tiene una cuarta hija, `eiprojectreviews`
 * (la revisión: temas de interés, qué sabe el grupo, qué desea aprender…),
 * que no existe en los otros dos documentos y que el legacy exigía completa.
 *
 * ─────────────────────────────────────────────────────────────────
 * BUGS DEL LEGACY CORREGIDOS AQUÍ (doc 03)
 * ─────────────────────────────────────────────────────────────────
 *  · IDOR en `loadProject`/`loadReview`/`loadSummary`/`delete*`: los cinco
 *    usaban `findOrFail($id)` a secas. El listado sí filtraba por
 *    `profesor_id`, pero llegar al modal con un id ajeno cargaba, guardaba o
 *    BORRABA datos de otro docente. Aquí todo pasa por `findProject()` /
 *    `findSummary()` / `findReview()`.
 *  · `getPevaluacionsList()` devolvía `[]` → `TypeError` al asignarlo a una
 *    propiedad tipada `Collection` (ver el modelo).
 *  · Quirk D3: el texto de la estrategia vive SIEMPRE en la columna `lunes`.
 *  · 403 explícito en `mount()`: el legacy dejaba `profesor_id = null` y el
 *    listado acababa mostrando planes huérfanos en vez de rechazar.
 *  · Momentos indexados 0..9 en `wire:model` (los nombres tienen espacios y
 *    dos puntos, que Livewire 3 lee como separadores de ruta).
 */
class EiprojectkComponent extends Component
{
    use ImportaDocumento, PensumCabecera, WireUiActions, WithPagination;

    /** Instancia del asistente de importación del documento. */
    protected function importador(): ImportadorEiprojectk
    {
        return new ImportadorEiprojectk;
    }

    // ─── Estado del modal ─────────────────────────────────────────

    public bool $showModal = false;

    /**
     * create · edit · view · strategy · summary · review · edit-summary ·
     * edit-review
     */
    public string $modalType = '';

    public ?int $editingId = null;

    public ?int $eiprojectk_id = null;

    public ?int $eiprojectsummary_id = null;

    public ?int $eiprojectreview_id = null;

    // ─── Formularios ─────────────────────────────────────────────

    public ?int $profesor_id = null;

    public $eiprojectk = [];

    public $eiprojectsummary = [];

    public $eiprojectreview = [];

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

    public ?int $lapso_id = null;

    /**
     * Días de la semana. Las claves coinciden con `day_of_week` y con las
     * columnas de la tabla.
     *
     * Proviene de la constante del modelo para que la rejilla de 50 celdas, la
     * vista imprimible (`format()`) y la BD no puedan divergir: son la MISMA
     * fuente.
     */
    public array $weekDays = Eiprojectkstrategy::WEEK_DAYS;

    /**
     * Los 10 momentos de la rutina diaria, EN SU ORDEN REAL (no alfabético).
     */
    public array $moments = Eiprojectkstrategy::LIST_MOMENT;

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

        $this->importCandidatos = collect();

        $this->resetModels();
    }

    public function render()
    {
        $query = Eiprojectk::where('profesor_id', $this->profesor_id)
            ->with(['grado', 'seccion']);

        if ($this->search !== '') {
            // El legacy solo buscaba en `diagnostico` (a diferencia de los
            // planes, que también buscan en `observacion`).
            $query->where('diagnostico', 'like', '%'.$this->search.'%');
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

        $eiprojectks = $query->orderByDesc('created_at')->paginate($this->paginate);

        // Conteo para el encabezado del wizard (estrategias ya escritas).
        $planEstrategias = $this->modalType === 'strategy' && $this->eiprojectk_id
            ? Eiprojectkstrategy::where('eiprojectk_id', $this->eiprojectk_id)->count()
            : 0;

        return view('livewire.inicial.eiprojectk.index', [
            'eiprojectks' => $eiprojectks,
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
                'edit' => $this->loadProject($id),
                'view' => $this->loadProject($id),
                'strategy' => $this->loadProjectForStrategy($id),
                'summary' => $this->loadProjectForSummary($id),
                'edit-summary' => $this->loadSummary($id),
                'review' => $this->loadProjectForReview($id),
                'edit-review' => $this->loadReview($id),
                default => $this->resetModels(),
            };
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            // 404/403 (proyecto o hija ajena) NO se tragan: se convertirían en
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
        $this->eiprojectk_id = null;
        $this->eiprojectsummary_id = null;
        $this->eiprojectreview_id = null;
        $this->activeDay = 'lunes';
        $this->activeMomentIndex = 0;
        $this->activeMoment = 'Recibimiento';
        $this->resetValidation();
    }

    // ─── Carga de datos ──────────────────────────────────────────

    private function loadProject(?int $id): void
    {
        $project = $this->findProject($id);

        $this->eiprojectk_id = $project->id;
        $this->eiprojectk = $project->only([
            'profesor_id', 'grado_id', 'seccion_id', 'pensum_id',
            'finicial', 'ffinal', 'tiempo_ejecucion', 'diagnostico', 'observacion',
        ]);
        // Los `date` del modelo llegan como Carbon (`2026-10-05 00:00:00`):
        // el `input[type=date]` lo rechaza como valor inválido, muestra vacío
        // y en el siguiente roundtrip vuelve `null`. Se normaliza a `Y-m-d`.
        $this->eiprojectk['finicial'] = $this->fechaInput($this->eiprojectk['finicial'] ?? null);
        $this->eiprojectk['ffinal'] = $this->fechaInput($this->eiprojectk['ffinal'] ?? null);

        $this->listSeccion = $this->seccionesDe($project->grado_id);
        $this->listPensum = $this->loadPensums($project->grado_id);
    }

    private function loadProjectForStrategy(?int $id): void
    {
        $project = $this->findProject($id);
        $this->eiprojectk_id = $project->id;
        $this->loadStrategiesForProject($project->id);
    }

    private function loadProjectForSummary(?int $id): void
    {
        $project = $this->findProject($id);
        $this->eiprojectk_id = $project->id;
        // `collect()` a la fuerza: `$listPevaluacion` está tipeada como
        // Collection y cualquier retorno array lanzaría un TypeError.
        $this->listPevaluacion = collect($project->getPevaluacionsList($this->profesor_id));
        $this->resetModelSummary();
    }

    private function loadProjectForReview(?int $id): void
    {
        $project = $this->findProject($id);
        $this->eiprojectk_id = $project->id;
        $this->resetModelReview();
    }

    /**
     * Rellena la rejilla completa 5 × 10 con lo que haya en BD.
     *
     * ⚠️ Las claves de `$strategies` son ÍNDICES (0..9), no los nombres de los
     * momentos: los nombres contienen espacios y dos puntos, y Livewire 3
     * resuelve `wire:model` por ruta con puntos.
     */
    private function loadStrategiesForProject(int $projectId): void
    {
        $existentes = Eiprojectkstrategy::where('eiprojectk_id', $projectId)
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

        // Con alcance por `findSummary()`: el legacy hacía
        // `Eiprojectsummary::find($id)` sin filtro, de modo que un id ajeno
        // cargaba el resumen de otro docente y `saveSummary()` lo escribía.
        $summary = $this->findSummary($id);

        $this->eiprojectsummary_id = $summary->id;
        $this->eiprojectk_id = $summary->eiprojectk_id;
        $this->eiprojectsummary = $summary->only([
            'pevaluacion_id', 'componente', 'objetivo', 'aprendizaje_esperado',
            'indicadores', 'linea_investigacion', 'enfasis_curriculares',
            'estrategias', 'order',
        ]);
        $this->listPevaluacion = collect($summary->eiprojectk->getPevaluacionsList($this->profesor_id));
    }

    private function loadReview(?int $id): void
    {
        if (! $id) {
            $this->resetModelReview();

            return;
        }

        $review = $this->findReview($id);

        $this->eiprojectreview_id = $review->id;
        $this->eiprojectk_id = $review->eiprojectk_id;
        $this->eiprojectreview = $review->only([
            'posibles_temas_interes', 'eleccion_tema_nombre', 'que_sabe',
            'que_desean_aprender', 'que_necesitamos', 'quienes_nos_pueden_apoyar',
            'estrategias', 'order',
        ]);
    }

    /**
     * Busca un proyecto por id garantizando que sea del docente autenticado.
     */
    private function findProject(?int $id): Eiprojectk
    {
        abort_if(! $id, 404, 'Proyecto no especificado.');

        // `abort(404)` explícito en vez de `findOrFail()`: este último lanza
        // `ModelNotFoundException`, que NO es un `HttpExceptionInterface` y
        // caería en el `catch (\Throwable)` genérico de `openModal()`.
        $project = Eiprojectk::where('profesor_id', $this->profesor_id)
            ->whereKey($id)
            ->first();

        abort_if(! $project, 404, 'Este proyecto no existe o pertenece a otro docente.');

        return $project;
    }

    /**
     * Busca un resumen garantizando que su proyecto sea del docente
     * autenticado.
     *
     * El alcance se aplica en la query, así que la respuesta es 404 en vez de
     * un 403 que confirmaría que el id existe.
     */
    private function findSummary(?int $id): Eiprojectsummary
    {
        abort_if(! $id, 404, 'Resumen no especificado.');

        $summary = Eiprojectsummary::query()
            ->whereKey($id)
            ->whereHas('eiprojectk', fn ($q) => $q->where('profesor_id', $this->profesor_id))
            ->first();

        abort_if(! $summary, 404, 'Este resumen no existe o pertenece a otro docente.');

        return $summary;
    }

    /**
     * Igual que {@see findSummary()} pero para la revisión.
     */
    private function findReview(?int $id): Eiprojectreview
    {
        abort_if(! $id, 404, 'Revisión no especificada.');

        $review = Eiprojectreview::query()
            ->whereKey($id)
            ->whereHas('eiprojectk', fn ($q) => $q->where('profesor_id', $this->profesor_id))
            ->first();

        abort_if(! $review, 404, 'Esta revisión no existe o pertenece a otro docente.');

        return $review;
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

    public function updatedEiprojectkGradoId($value): void
    {
        $this->listSeccion = $value ? $this->seccionesDe($value) : collect();
        $this->eiprojectk['seccion_id'] = null;
        $this->listPensum = $this->loadPensums($value);
        $this->eiprojectk['pensum_id'] = null;
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
        if (! $this->eiprojectk_id) {
            $this->listPevaluacion = collect();

            return;
        }

        $project = Eiprojectk::find($this->eiprojectk_id);
        $this->listPevaluacion = collect(
            $project ? $project->getPevaluacionsList($this->profesor_id, $lapso_id) : []
        );
    }

    // ─── CRUD cabecera ───────────────────────────────────────────

    public function save(): void
    {
        $this->resetValidation();

        // El profesor es siempre el autenticado: nunca se toma del formulario.
        $this->eiprojectk['profesor_id'] = $this->profesor_id;

        // `fromInput()` en lugar de `createFrom($this)`: este último está
        // tipeado con `self` (un Request) y un componente Livewire no lo es.
        // `validateResolved()` devuelve void: hay que encadenar sobre el objeto.
        $request = EiprojectkRequest::fromInput($this->eiprojectk);
        $request->validateResolved();
        $validated = $request->planData();

        if (! empty($validated['pensum_id']) && ! $this->loadPensums($validated['grado_id'] ?? null)->has($validated['pensum_id'])) {
            $this->addError('eiprojectk.pensum_id', 'El área de aprendizaje elegida no pertenece a este docente.');

            return;
        }

        $project = $this->eiprojectk_id
            ? $this->findProject($this->eiprojectk_id)
            : new Eiprojectk;

        $project->fill($validated);
        $project->save();

        $this->notification()->success(
            title: 'Proyecto guardado',
            description: 'El proyecto de aula se guardó correctamente.'
        );

        $this->resetModels();
        $this->closeModal();
    }

    public function saveSummary(): void
    {
        if (! $this->eiprojectk_id) {
            $this->notification()->error(
                title: 'Error',
                description: 'No se ha seleccionado un proyecto válido.'
            );

            return;
        }

        $this->resetValidation();

        $this->eiprojectsummary['eiprojectk_id'] = $this->eiprojectk_id;

        $request = EiprojectsummaryRequest::fromInput($this->eiprojectsummary);
        $request->validateResolved();
        $data = $request->summaryData();

        // En edición se resuelve con `findSummary()` (que aplica el alcance del
        // docente). Un `find()` a secas —como el legacy— dejaba que un id ajeno
        // escribiera sobre el resumen de otro docente.
        $summary = $this->eiprojectsummary_id
            ? $this->findSummary($this->eiprojectsummary_id)
            : new Eiprojectsummary;

        $summary->fill($data);
        $summary->save();

        $this->notification()->success(
            title: 'Resumen guardado',
            description: 'El resumen por área se guardó correctamente.'
        );

        // Se vuelve al gestor de resúmenes con la lista ya fresca.
        $this->resetModelSummary();
        $this->openModal('summary', $this->eiprojectk_id);
    }

    public function saveReview(): void
    {
        if (! $this->eiprojectk_id) {
            $this->notification()->error(
                title: 'Error',
                description: 'No se ha seleccionado un proyecto válido.'
            );

            return;
        }

        $this->resetValidation();

        $this->eiprojectreview['eiprojectk_id'] = $this->eiprojectk_id;

        $request = EiprojectreviewRequest::fromInput($this->eiprojectreview);
        $request->validateResolved();
        $data = $request->reviewData();

        $review = $this->eiprojectreview_id
            ? $this->findReview($this->eiprojectreview_id)
            : new Eiprojectreview;

        $review->fill($data);
        $review->save();

        $this->notification()->success(
            title: 'Revisión guardada',
            description: 'La revisión del proyecto se guardó correctamente.'
        );

        // Se vuelve al gestor de revisiones con la lista ya fresca.
        $this->resetModelReview();
        $this->openModal('review', $this->eiprojectk_id);
    }

    // ─── CRUD estrategias ────────────────────────────────────────

    /**
     * Guarda la celda activa y avanza al momento siguiente (flujo del docente).
     */
    public function saveCurrentStrategy(): void
    {
        if (! $this->eiprojectk_id) {
            $this->notification()->error(
                title: 'Error',
                description: 'No se ha seleccionado un proyecto válido.'
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
        if (! $this->eiprojectk_id) {
            $this->notification()->error(
                title: 'Error',
                description: 'No se ha seleccionado un proyecto válido.'
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
            $strategy = new Eiprojectkstrategy;
            $strategy->eiprojectk_id = $this->eiprojectk_id;
            $strategy->day_of_week = $day;
            $strategy->momento_rutina_diaria = $moment;
        } else {
            $strategy = Eiprojectkstrategy::where('eiprojectk_id', $this->eiprojectk_id)
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
        if (! $this->eiprojectk_id || ! array_key_exists($day, $this->weekDays)) {
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

        Eiprojectkstrategy::where('eiprojectk_id', $this->eiprojectk_id)
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

    public function confirmDeleteProject(int $id): void
    {
        $this->dialog()->confirm([
            'title' => 'Eliminar proyecto de aula',
            'description' => 'Se eliminará el proyecto junto con sus revisiones, resúmenes y estrategias. Esta acción no se puede deshacer.',
            'icon' => 'warning',
            'accept' => [
                'label' => 'Eliminar',
                'method' => 'deleteProject',
                'params' => [$id],
                'color' => 'red',
            ],
            'reject' => ['label' => 'Cancelar'],
        ]);
    }

    public function deleteProject(int $id): void
    {
        $project = $this->findProject($id);

        // Sin FK declarada hacia las hijas, se borran explícitamente.
        $project->eiprojectkstrategies()->delete();
        $project->eiprojectsummaries()->delete();
        $project->eiprojectreviews()->delete();
        $project->delete();

        $this->notification()->success(
            title: 'Proyecto eliminado',
            description: 'El proyecto de aula se eliminó correctamente.'
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
        // Mismo alcance que en `findProject()`/`findSummary()`: `abort(404)`
        // explícito para que el 404 llegue como HttpException y no se coma en
        // el catch genérico de `openModal()`.
        $summary = $this->findSummary($id);

        abort_if(
            (int) $summary->eiprojectk_id !== (int) $this->eiprojectk_id,
            404,
            'Este resumen no pertenece al proyecto abierto.'
        );

        $summary->delete();

        $this->notification()->success(
            title: 'Resumen eliminado',
            description: 'El resumen se eliminó correctamente.'
        );
    }

    public function confirmDeleteReview(int $id): void
    {
        $this->dialog()->confirm([
            'title' => 'Eliminar revisión',
            'description' => 'Se eliminará la revisión del proyecto.',
            'icon' => 'warning',
            'accept' => [
                'label' => 'Eliminar',
                'method' => 'deleteReview',
                'params' => [$id],
                'color' => 'red',
            ],
            'reject' => ['label' => 'Cancelar'],
        ]);
    }

    public function deleteReview(int $id): void
    {
        $review = $this->findReview($id);

        abort_if(
            (int) $review->eiprojectk_id !== (int) $this->eiprojectk_id,
            404,
            'Esta revisión no pertenece al proyecto abierto.'
        );

        $review->delete();

        $this->notification()->success(
            title: 'Revisión eliminada',
            description: 'La revisión se eliminó correctamente.'
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
        $this->eiprojectk = [
            'profesor_id' => $this->profesor_id,
            'grado_id' => null,
            'seccion_id' => null,
            'pensum_id' => null,
            'finicial' => null,
            'ffinal' => null,
            'tiempo_ejecucion' => 1,
            'diagnostico' => null,
            'observacion' => null,
        ];

        $this->eiprojectk_id = null;
        $this->resetModelSummary();
        $this->resetModelReview();
        $this->resetModelStrategies();
    }

    private function resetModelSummary(): void
    {
        $this->eiprojectsummary = [
            'pevaluacion_id' => null,
            'componente' => null,
            'objetivo' => null,
            'aprendizaje_esperado' => null,
            'indicadores' => null,
            'linea_investigacion' => null,
            'enfasis_curriculares' => null,
            'estrategias' => null,
            'order' => null,
        ];

        $this->eiprojectsummary_id = null;
    }

    private function resetModelReview(): void
    {
        $this->eiprojectreview = [
            'posibles_temas_interes' => null,
            'eleccion_tema_nombre' => null,
            'que_sabe' => null,
            'que_desean_aprender' => null,
            'que_necesitamos' => null,
            'quienes_nos_pueden_apoyar' => null,
            'estrategias' => null,
            'order' => null,
        ];

        $this->eiprojectreview_id = null;
    }

    private function resetModelStrategies(): void
    {
        $this->strategies = [];

        foreach (array_keys($this->weekDays) as $day) {
            // Claves por ÍNDICE de momento (ver loadStrategiesForProject).
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
