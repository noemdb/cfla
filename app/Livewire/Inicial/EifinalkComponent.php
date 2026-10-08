<?php

namespace App\Livewire\Inicial;

use App\Http\Requests\Inicial\EifinalkRequest;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Inicial\Eifinalk;
use App\Models\app\Inicial\Eilearningexpectation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

/**
 * CRUD del INFORME FINAL por estudiante (Educación Inicial).
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * EL PATRÓN DISTINTO DEL MÓDULO
 * ─────────────────────────────────────────────────────────────────────────────
 * Los otros cinco componentes comparten esqueleto: modal único con `$modalType`,
 * filtro por grado/sección y listados de "documentos". Aquí el patrón es otro, y
 * se conserva a propósito porque responde a una necesidad distinta:
 *
 *  · DOS PESTAÑAS. `informesList` es el trabajo normal (los informes del
 *    docente). `estudiantesList` es un índice NIÑO × CARGA que responde "¿quién
 *    no tiene informe todavía?", con la insignia de si ya lo tiene y un enlace
 *    para crearlo de una vez. Sin esta pestaña, la docente tiene que abrir cada
 *    informe y acordarse de cuáles faltan.
 *  · ACORDEÓN DE EXPECTATIVAS. Las expectativas de aprendizaje se eligen por
 *    ÁREA (que se cargan según el grado de la carga académica) y se guardan en
 *    el pivote `eifinalk_expectation`.
 *  · CAMPOS CONDICIONALES por `$pevaluacion->status_official`: un informe
 *    oficial pide logros y observaciones individuales; uno de componente pide la
 *    observación del especialista. Ocultar lo que no aplica evita que se rellene
 *    a ciegas.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * BUGS DEL LEGACY CORREGIDOS AQUÍ
 * ─────────────────────────────────────────────────────────────────────────────
 *  · IDOR en `edit()`/`delete()`: ambos usaban `findOrFail($id)` a secas. El
 *    listado sí filtraba por `byProfesor()`, pero llegar al modal con un id ajeno
 *    cargaba o BORRABA el informe de otro docente. Aquí todo pasa por
 *    `findReport()`.
 *  · `update()` SÍ filtraba por profesor, pero `edit()` y `delete()` no: el mismo
 *    componente era inconsistente consigo mismo.
 *  · `$this->fill($record->toArray())` volcaba TODO el modelo —incluidos `id`,
 *    `created_at` y las claves del pivote— en propiedades públicas del
 *    componente, que Livewire después serializa en cada request. Aquí se copian solo
 *    los 14 campos del formulario.
 *  · Las expectativas se reconstruían con un `find()` por cada una. Se valida
 *    el conjunto contra las áreas del grado antes de sincronizar (ver
 *    {@see syncExpectations()}).
 *  · `Eifinalk::delete()` no borraba las filas del pivote: quedaban expectativas
 *    colgadas de informes inexistentes.
 *  · 403 explícito en `mount()`: el legacy dejaba `profesor_id = null` y el
 *    listado acababa vacío en vez de rechazar.
 */
class EifinalkComponent extends Component
{
    use WireUiActions, WithPagination;

    // ─── Estado del modal ─────────────────────────────────────────

    public bool $showModal = false;

    public string $modalType = '';

    /**
     * Id del informe en edición; `null` en alta.
     */
    public ?int $editingId = null;

    // ─── Pestañas ─────────────────────────────────────────────────

    /**
     * informesList · estudiantesList
     */
    public string $activeTab = 'informesList';

    // ─── Formulario ───────────────────────────────────────────────

    public ?int $profesor_id = null;

    public $eifinalk = [];

    /**
     * Ids de expectativas marcadas en el acordeón.
     *
     * @var array<int, int|string>
     */
    public array $selected_expectations = [];

    // ─── Filtros de la pestaña de informes ────────────────────────

    public string $filterPevaluacion = '';

    public string $filterEstudiant = '';

    public string $filterTitle = '';

    // ─── Pestaña de estudiantes ───────────────────────────────────

    /**
     * Carga académica seleccionada: define qué niños se listan.
     */
    public $filterPevaluacionEstudiantes = '';

    public string $filterEstudiantSearch = '';

    // ─── Listas ───────────────────────────────────────────────────

    public Collection $listPevaluacion;

    public Collection $pevaluacions;

    public Collection $estudiantes;

    public Collection $learningAreas;

    public ?int $lapso_id = null;

    public int $paginate = 10;

    public function mount(): void
    {
        $this->authorizeInicial();

        $profesor = Auth::user()->profesor;
        $this->profesor_id = $profesor?->id;

        $context = new EifinalkContext($this->profesor_id);

        $this->pevaluacions = $context->pevaluacions();
        $this->listPevaluacion = $context->pevaluacionList();
        $this->estudiantes = collect();
        $this->learningAreas = collect();

        $this->resetModels();
    }

    public function render()
    {
        return view('livewire.inicial.eifinalk.index', [
            'eifinalks' => $this->informes(),
            'totalInformes' => $this->informes()->count(),
        ]);
    }

    /**
     * Informes del docente con sus relaciones, filtrados y paginados.
     *
     * `byProfesor` resuelve vía `pevaluacion.profesor_id`: el informe no tiene
     * `profesor_id` propio.
     */
    private function informes()
    {
        $query = Eifinalk::with(['pevaluacion.pensum.grado', 'pevaluacion.seccion', 'expectant', 'expectations.area']);

        if ($this->profesor_id) {
            $query->byProfesor($this->profesor_id);

            if ($this->filterPevaluacion !== '') {
                $query->where('pevaluacion_id', $this->filterPevaluacion);
            }

            if ($this->filterEstudiant !== '') {
                $termino = $this->filterEstudiant;
                $query->whereHas('expectant', function ($q) use ($termino) {
                    $q->where('name', 'like', "%{$termino}%")
                        ->orWhere('lastname', 'like', "%{$termino}%")
                        ->orWhere('ci_estudiant', 'like', "%{$termino}%");
                });
            }

            if ($this->filterTitle !== '') {
                $query->where('title', 'like', '%'.$this->filterTitle.'%');
            }
        }

        // `order` primero: el boletín se imprime en ese orden y es la única
        // garantía de un orden estable (created_at empataría).
        return $query->orderBy('order')->orderBy('id')->paginate($this->paginate);
    }

    // ─── Modal ───────────────────────────────────────────────────

    public function openModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->modalType = $id ? 'edit' : 'create';
        $this->editingId = $id;
        $this->showModal = true;

        try {
            $id ? $this->loadReport($id) : $this->resetModels();
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
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
        $this->learningAreas = collect();
        $this->selected_expectations = [];
        $this->resetValidation();
    }

    // ─── Carga de datos ───────────────────────────────────────────

    private function loadReport(?int $id): void
    {
        $report = $this->findReport($id);
        $context = new EifinalkContext($this->profesor_id);

        $this->eifinalk = $report->only(self::CAMPOS);

        // Las expectativas ya vinculadas alimentan el acordeón con las casillas
        // marcadas.
        $this->selected_expectations = $report->expectations->pluck('id')->map(fn ($id) => (int) $id)->all();

        // Las áreas se recargan por el GRADO de la carga académica: si el docente
        // cambia de área, el acordeón debe cambiar con ella.
        // `pevaluacions` NO tiene columna `grado_id`: el grado llega por
        // `hasOneThrough` desde la sección. Leer el atributo devolvía null y el
        // acordeón salía vacío sin avisar.
        $gradoId = $report->pevaluacion?->grado?->id;
        $this->learningAreas = $context->learningAreas($gradoId);
        $this->lapso_id = $report->pevaluacion?->lapso_id;
    }

    /**
     * Los 14 campos del formulario.
     *
     * Lista explícita y NO `toArray()`: el legacy volcaba también `id`,
     * `created_at`, `updated_at` y las claves del pivote en propiedades públicas,
     * que Livewire serializa en cada request sin que sirvan para nada.
     */
    private const CAMPOS = [
        'order', 'pevaluacion_id', 'estudiant_id', 'title',
        'context_group', 'planing_eject', 'featured_project', 'special_activities',
        'achievements', 'individual_observations', 'specialist_observation',
        'family_participation', 'conclusions', 'recommendations', 'expected_learnings',
    ];

    /**
     * Busca un informe garantizando que su carga académica sea del docente.
     *
     * Sin esta comprobación, `edit()` y `delete()` con un id ajeno permitían leer
     * y BORRAR informes de otro docente: el listado sí filtraba, pero el modal no.
     */
    private function findReport(?int $id): Eifinalk
    {
        abort_if(! $id, 404, 'Informe no especificado.');

        $report = Eifinalk::query()
            ->whereKey($id)
            ->whereHas('pevaluacion', fn ($q) => $q->where('profesor_id', $this->profesor_id))
            ->first();

        abort_if(! $report, 404, 'Este informe no existe o pertenece a otro docente.');

        return $report;
    }

    // ─── Pestañas y cascada de selects ────────────────────────────

    public function setActiveTab(string $tab): void
    {
        if (in_array($tab, ['informesList', 'estudiantesList'], true)) {
            $this->activeTab = $tab;
        }
    }

    /**
     * Al elegir la carga del formulario se fija el lapso y se recarga el
     * acordeón de áreas del grado correspondiente.
     */
    public function updatedEifinalkPevaluacionId($value): void
    {
        $context = new EifinalkContext($this->profesor_id);

        $pevaluacion = $value ? Pevaluacion::find($value) : null;

        $this->lapso_id = $pevaluacion?->lapso_id;
        $this->learningAreas = $context->learningAreas($pevaluacion?->grado?->id);

        // Las expectativas marcadas pertenecen al grado anterior: si no se
        // limpian, el `sync()` guardaría expectativas de un grado que no es el
        // del informe.
        $this->selected_expectations = [];
    }

    /**
     * La pestaña de estudiantes se populationa desde la carga académica elegida.
     */
    public function updatedFilterPevaluacionEstudiantes($value): void
    {
        $context = new EifinalkContext($this->profesor_id);

        $pevaluacion = $value ? Pevaluacion::find($value) : null;

        $this->lapso_id = $pevaluacion?->lapso_id;
        $this->estudiantes = $context->estudiantesDeSeccion($pevaluacion?->seccion_id);
    }

    public function updatedFilterPevaluacion(): void
    {
        $this->resetPage();
    }

    public function updatedSearchEstudiantes(): void
    {
        // La búsqueda del niño se filtra en la vista: la lista es de una sola
        // sección y no llega a justificar un `where` en cada tecla.
    }

    public function updatedFilterEstudiant(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTitle(): void
    {
        $this->resetPage();
    }

    // ─── CRUD ─────────────────────────────────────────────────────

    public function save(): void
    {
        $this->resetValidation();

        // Alta y edición comparten el mismo camino: `persist()`. El legacy tenía
        // `save()` y `update()` por separado, duplicados salvo en el pivote.
        $this->persist();
    }

    public function update(): void
    {
        $this->resetValidation();

        abort_if(! $this->editingId, 404, 'No hay ningún informe en edición.');

        $this->persist();
    }

    /**
     * Valida, guarda y sincroniza las expectativas.
     *
     * Alta y edición en un solo método a propósito: separarlas obligaba a
     * duplicar el `modelData()` y el `attach`/`sync`, y fue como el legacy acabó
     * con dos rutas que se comportaban distinto (una filtraba por profesor y la
     * otra no).
     */
    private function persist(): void
    {
        // La carga académica la pone el usuario (es un dato del informe), pero la
        // validación comprueba que exista; el filtro por profesor se hace en
        // `findReport()`/`byProfesor` al leer y guardar.
        $request = EifinalkRequest::fromInput($this->eifinalk);
        $request->validateResolved();
        $data = $request->reportData();

        $expectations = $this->expectationsValidas($data['pevaluacion_id']);

        $report = $this->editingId
            ? $this->findReport($this->editingId)
            : new Eifinalk;

        $report->fill($data);
        $report->save();

        // `sync()` y no `attach()`: es un reemplazo total, así que al desmarcar
        // una expectativa en el acordeón debe desaparecer del pivote. Con
        // `attach()` se acumulaban y el informe acababa con más expectativas de
        // las marcadas.
        $report->expectations()->sync($expectations);

        $this->notification()->success(
            title: $this->editingId ? 'Informe actualizado' : 'Informe creado',
            description: 'El informe final se guardó correctamente.'
        );

        $this->resetModels();
        $this->closeModal();
    }

    /**
     * Traduce los ids marcados del acordeón al mapa del pivote, DESCARTANDO las
     * expectativas que no pertenecen al grado de la carga académica.
     *
     * El legacy hacía `Eilearningexpectation::find()` por cada id marcado y lo
     * adjuntaba sin más: marcara lo que marcara, una expectativa de otro grado
     * se guardaba igual y contaminaba el informe.
     *
     * @return array<int, array{eilearningarea_id:int, pevaluacion_id:int}>
     */
    private function expectationsValidas(int $pevaluacionId): array
    {
        if ($this->selected_expectations === []) {
            return [];
        }

        $areaIds = $this->learningAreas->pluck('id')->map(fn ($id) => (int) $id)->all();

        $validas = Eilearningexpectation::whereIn('id', $this->selected_expectations)
            ->whereIn('eilearningarea_id', $areaIds)
            ->get(['id', 'eilearningarea_id']);

        return $validas->mapWithKeys(fn ($expectation) => [
            $expectation->id => [
                'eilearningarea_id' => (int) $expectation->eilearningarea_id,
                'pevaluacion_id' => $pevaluacionId,
            ],
        ])->all();
    }

    /**
     * Marca o desmarca una expectativa. La UI lo hace con `wire:model` sobre
     * `$selected_expectations`, pero las áreas del acordeón se recargan al cambiar
     * de carga: este método permite fijarlas desde código sin perder el resto.
     */
    public function toggleExpectation(int $expectationId): void
    {
        $ids = array_map('intval', $this->selected_expectations);

        $this->selected_expectations = in_array($expectationId, $ids, true)
            ? array_values(array_diff($ids, [$expectationId]))
            : array_values(array_unique(array_merge($ids, [$expectationId])));
    }

    // ─── Borrados ────────────────────────────────────────────────

    public function confirmDeleteReport(int $id): void
    {
        $this->dialog()->confirm([
            'title' => 'Eliminar informe final',
            'description' => 'Se eliminará el informe y sus expectativas vinculadas. Esta acción no se puede deshacer.',
            'icon' => 'warning',
            'accept' => [
                'label' => 'Eliminar',
                'method' => 'deleteReport',
                'params' => [$id],
                'color' => 'red',
            ],
            'reject' => ['label' => 'Cancelar'],
        ]);
    }

    public function deleteReport(int $id): void
    {
        $report = $this->findReport($id);

        // Sin FK declarada con ON DELETE CASCADE, el pivote queda con filas
        // apuntando a un informe inexistente si no se limpia a mano.
        $report->expectations()->detach();
        $report->delete();

        $this->notification()->success(
            title: 'Informe eliminado',
            description: 'El informe final se eliminó correctamente.'
        );
    }

    // ─── Utilidades de vista ──────────────────────────────────────

    /**
     * Campos que aplica un informe OFICIAL (`status_official`), frente a los de
     * un informe de COMPONENTE.
     *
     * El legacy lo tenía cableado en la vista con dos `@if`; se expone aquí para
     * que la condición viva junto a los datos y no dentro del markup.
     *
     * @return array<string, string> etiqueta => texto de ayuda
     */
    public function camposOficiales(): array
    {
        return [
            'expected_learnings' => 'Aprendizajes esperados',
            'achievements' => 'Logros',
            'individual_observations' => 'Observaciones individuales',
        ];
    }

    /**
     * Campos que aplican un informe de COMPONENTE.
     *
     * @return array<string, string>
     */
    public function camposComponente(): array
    {
        return [
            'specialist_observation' => 'Observación del especialista',
        ];
    }

    /**
     * ¿El informe se emite como oficial? `null` mientras no hay carga elegida.
     */
    public function esOficial(?int $pevaluacionId = null): bool
    {
        $pevaluacionId = $pevaluacionId ?? ($this->eifinalk['pevaluacion_id'] ?? null);

        if (! $pevaluacionId) {
            return false;
        }

        // El tipo viene de la carga, no de un campo del informe: es lo que
        // decide qué bloques tiene sentido pedir.
        return (bool) Pevaluacion::whereKey($pevaluacionId)->value('status_official');
    }

    /**
     * Etiqueta legible del estudiante del informe.
     */
    public function nombreEstudiante(?int $estudiantId): string
    {
        if (! $estudiantId) {
            return '—';
        }

        $estudiant = \App\Models\app\Learner\Estudiant::find($estudiantId);

        return $estudiant?->full_name ?? "Estudiante #{$estudiantId}";
    }

    /**
     * ¿Este estudiante ya tiene informe para el cargo/lapso seleccionados?
     * Alimenta la insignia verde de la pestaña de estudiantes.
     */
    public function tieneInforme(int $estudiantId): bool
    {
        $pevaluacionId = $this->filterPevaluacionEstudiantes;

        if (! $pevaluacionId) {
            return false;
        }

        return Eifinalk::where('pevaluacion_id', $pevaluacionId)
            ->where('estudiant_id', $estudiantId)
            ->exists();
    }

    /**
     * Prepara el alta desde la pestaña de estudiantes: elige la carga y abre el
     * modal con el niño ya fijado.
     */
    public function nuevoInformePara(int $estudiantId): void
    {
        $this->resetModels();

        $this->eifinalk['pevaluacion_id'] = $this->filterPevaluacionEstudiantes ?: null;
        $this->eifinalk['estudiant_id'] = $estudiantId;
        $this->eifinalk['order'] = (Eifinalk::where('pevaluacion_id', $this->filterPevaluacionEstudiantes)->max('order') ?: 0) + 1;

        // El `@updated` solo salta al cambiar el campo desde la UI: aquí hay que
        // replicar la carga del acordeón a mano o el modal abriría sin áreas.
        $this->updatedEifinalkPevaluacionId($this->eifinalk['pevaluacion_id']);

        // …y `updated…` limpia las expectativas, así que el estudiante se fija
        // DESPUÉS de esa llamada.
        $this->eifinalk['estudiant_id'] = $estudiantId;

        $this->modalType = 'create';
        $this->showModal = true;
    }

    // ─── Resets y utilidades ──────────────────────────────────────

    private function authorizeInicial(): void
    {
        $user = Auth::user();

        abort_if(! $user || (! $user->isInicial() && ! $user->is_admin), 403, 'Acceso denegado al módulo de Educación Inicial.');
    }

    private function resetModels(): void
    {
        $this->eifinalk = [
            'order' => 1,
            'pevaluacion_id' => null,
            'estudiant_id' => null,
            'title' => null,
            'context_group' => null,
            'planing_eject' => null,
            'featured_project' => null,
            'special_activities' => null,
            'achievements' => null,
            'individual_observations' => null,
            'specialist_observation' => null,
            'family_participation' => null,
            'conclusions' => null,
            'recommendations' => null,
            'expected_learnings' => null,
        ];

        $this->editingId = null;
        $this->selected_expectations = [];
        $this->learningAreas = collect();
    }
}
