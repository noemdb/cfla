<?php

namespace App\Livewire\Planning\Diagnostic;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;
use Livewire\Attributes\Layout;
use App\Models\app\Instrument\DiagReferent;
use App\Models\app\Instrument\DiagCompetency;
use App\Models\app\Instrument\DiagIndicator;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Grado;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReferentsMain extends Component
{
    use WithPagination, WireUiActions, WithFileUploads;

    // ─── VIEW STATE ───────────────────────────────────────────────

    /**
     * Current view mode: referents, competencies, indicators
     */
    public $viewMode = 'referents';

    /**
     * Selected parent IDs for nested navigation
     */
    public $selectedReferentId = null;
    public $selectedCompetencyId = null;

    /**
     * Context models for breadcrumb display
     */
    public $currentReferent = null;
    public $currentCompetency = null;

    // ─── SEARCH & FILTERS ─────────────────────────────────────────

    /**
     * Referent search (name, code, description)
     */
    public $search = '';

    /**
     * Referent active filter: '' = all, '1' = active, '0' = inactive
     */
    public $filterActive = '';

    /**
     * Referent filter by pestudio
     */
    public $filterPestudioId = '';

    /**
     * Referent filter by grado (cascaded from pestudio)
     */
    public $filterGradoId = '';

    /**
     * Grados filtered by selected pestudio (cascading)
     */
    public $filterPestudioGrados = [];

    /**
     * Competency filter: filter by grado (grade level)
     */
    public $compFilterGradoId = '';

    /**
     * Competency filter: filter by pensum (subject area)
     */
    public $compFilterPensumId = '';

    /**
     * Competency filter: pestudio cascading for grado
     */
    public $compFilterPestudioId = '';

    /**
     * Grados filtered by selected pestudio (competency cascade)
     */
    public $compFilterPestudioGrados = [];

    // ─── MODALS ───────────────────────────────────────────────────

    /**
     * Show the create/edit form modal
     */
    public $showModal = false;

    /**
     * Show the detail/read-only modal
     */
    public $detailModal = false;

    /**
     * ID of the record being edited (null = create mode)
     */
    public $editingId = null;

    /**
     * Model instance for the detail modal
     */
    public $detailItem = null;

    // ─── IMPORT MODAL ─────────────────────────────────────────────

    /**
     * Show the import JSON modal
     */
    public $showImportModal = false;

    /**
     * Uploaded JSON file for import
     */
    public $importJsonFile = null;

    /**
     * Available referents for the import select
     */
    public $importReferents = [];

    /**
     * Selected referent ID for import
     */
    public $importReferentId = '';

    /**
     * Parsed JSON preview data
     */
    public $importPreview = null;

    /**
     * Import status message
     */
    public $importStatus = '';

    /**
     * True when import is in progress
     */
    public $importing = false;

    // ─── FORM DATA ────────────────────────────────────────────────

    /**
     * Form fields array, structure depends on current viewMode:
     *
     * referents:
     *   pestudio_id, name, code, version, description, active
     *
     * competencies:
     *   referent_id, pensum_id, name, description
     *
     * indicators:
     *   competency_id, code, description, expected_level
     */
    public $form = [];

    // ─── SELECT LISTS ─────────────────────────────────────────────

    /**
     * Pestudios for the referent form select
     */
    public $referentPestudios = [];

    /**
     * All active grados for general selects
     */
    public $grados = [];

    /**
     * Pensums (grado-asignatura) for competency form select
     */
    public $pensums = [];

    /**
     * Grados that have pensums, for competency filters
     */
    public $compGrados = [];

    /**
     * Pensums filtered by selected grado, for competency filters
     */
    public $compPensums = [];

    // ─── CONSTANTS ────────────────────────────────────────────────

    /**
     * Expected level labels and color themes for indicators
     */
    public $expectedLevels = [
        1 => ['label' => 'Insuficiente', 'color' => 'warning'],
        2 => ['label' => 'En desarrollo', 'color' => 'warning'],
        3 => ['label' => 'Satisfactorio', 'color' => 'info'],
        4 => ['label' => 'Avanzado', 'color' => 'success'],
    ];

    // ─── MOUNT ────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->loadReferentPestudios();
        $this->loadGrados();
        $this->loadCompGrados();
        $this->loadPensums();
        $this->resetForm();
    }

    // ─── RENDER ───────────────────────────────────────────────────

    #[Layout('planning.layouts.app')]
    public function render()
    {
        $items = match ($this->viewMode) {
            'competencies' => $this->queryCompetencies(),
            'indicators'   => $this->queryIndicators(),
            default        => $this->queryReferents(),
        };

        $breadcrumb = $this->buildBreadcrumb();

        return view('livewire.planning.diagnostic.referents-main', [
            'items'      => $items,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    // ─── QUERIES ──────────────────────────────────────────────────

    protected function queryReferents()
    {
        $query = DiagReferent::with('pestudio')
            ->withCount('competencies')
            ->addSelect(DB::raw('(select count(*) from diag_indicators where competency_id in (select id from diag_competencies where referent_id = diag_referents.id)) as total_indicators_count'));

        if ($this->search !== '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($this->filterActive !== '') {
            $query->where('active', $this->filterActive === '1');
        }

        if ($this->filterGradoId !== '') {
            $query->whereHas('pestudio', fn ($q) => $q->whereHas('grados', fn ($g) => $g->where('id', $this->filterGradoId)));
        }

        if ($this->filterPestudioId !== '') {
            $query->where('pestudio_id', $this->filterPestudioId);
        }

        return $query->orderBy('code')->paginate(10);
    }

    protected function queryCompetencies()
    {
        $query = DiagCompetency::with(['referent', 'pensum.grado', 'pensum.asignatura'])
            ->withCount('indicators')
            ->where('referent_id', $this->selectedReferentId);

        if ($this->compFilterPestudioId !== '') {
            $query->whereHas('pensum', fn ($q) => $q->where('pestudio_id', $this->compFilterPestudioId));
        }

        if ($this->compFilterGradoId !== '') {
            $query->whereHas('pensum', fn ($q) => $q->where('grado_id', $this->compFilterGradoId));
        }

        if ($this->compFilterPensumId !== '') {
            $query->where('pensum_id', $this->compFilterPensumId);
        }

        return $query->orderBy('name')->paginate(10);
    }

    protected function queryIndicators()
    {
        return DiagIndicator::with('competency')
            ->where('competency_id', $this->selectedCompetencyId)
            ->orderBy('code')
            ->paginate(10);
    }

    // ─── NAVIGATION ──────────────────────────────────────────────

    /**
     * Navigate into a referent to show its competencies
     */
    public function showReferentDetail($id): void
    {
        $this->selectedReferentId = $id;
        $this->currentReferent = DiagReferent::findOrFail($id);
        $this->viewMode = 'competencies';
        $this->resetFilters();
        $this->resetPage();

        // Note: We intentionally do NOT auto-set the pestudio filter here.
        // The referent's pestudio_id may differ from the pestudio_id of the
        // pensums linked to its competencies (e.g., referent points to a newer
        // plan code while competencies use pensums from the active plan).
        // Let the user apply filters manually to avoid hiding results.
    }

    /**
     * Navigate into a competency to show its indicators
     */
    public function showCompetencyDetail($id): void
    {
        $this->selectedCompetencyId = $id;
        $this->currentCompetency = DiagCompetency::with('referent')->findOrFail($id);
        $this->viewMode = 'indicators';
        $this->resetPage();
    }

    /**
     * Go back to the referents list
     */
    public function backToReferents(): void
    {
        $this->viewMode = 'referents';
        $this->selectedReferentId = null;
        $this->currentReferent = null;
        $this->selectedCompetencyId = null;
        $this->currentCompetency = null;
        $this->resetFilters();
        $this->resetPage();
    }

    /**
     * Go back to the competencies list from indicators
     */
    public function backToCompetencies(): void
    {
        $this->viewMode = 'competencies';
        $this->selectedCompetencyId = null;
        $this->currentCompetency = null;
        $this->resetPage();
    }

    /**
     * Build breadcrumb trail based on current view mode
     */
    protected function buildBreadcrumb(): array
    {
        $crumbs = [
            ['label' => 'Referentes Curriculares', 'route' => null],
        ];

        if ($this->viewMode === 'competencies' || $this->viewMode === 'indicators') {
            $referentName = $this->currentReferent?->name ?? 'Referente #' . $this->selectedReferentId;
            $crumbs[] = ['label' => $referentName, 'route' => null];
        }

        if ($this->viewMode === 'indicators') {
            $competencyName = $this->currentCompetency?->name ?? 'Competencia #' . $this->selectedCompetencyId;
            $crumbs[] = ['label' => $competencyName, 'route' => null];
        }

        return $crumbs;
    }

    // ─── CRUD: REFERENTS ─────────────────────────────────────────

    public function createReferent(): void
    {
        $this->resetForm();
        $this->form['active'] = true;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function editReferent(int $id): void
    {
        $referent = DiagReferent::findOrFail($id);
        $this->form = [
            'pestudio_id' => $referent->pestudio_id,
            'name'        => $referent->name,
            'code'        => $referent->code,
            'version'     => $referent->version,
            'description' => $referent->description,
            'active'      => $referent->active,
        ];
        $this->editingId = $id;
        $this->showModal = true;
    }

    public function saveReferent(): void
    {
        $this->validate([
            'form.pestudio_id' => 'required|integer|exists:pestudios,id',
            'form.name'        => 'required|string|max:255',
            'form.code'        => 'required|string|max:50',
            'form.version'     => 'nullable|string|max:50',
            'form.description' => 'nullable|string|max:1000',
            'form.active'      => 'boolean',
        ]);

        // Versioning: only one active referent per pestudio
        if ($this->form['active']) {
            DiagReferent::where('pestudio_id', $this->form['pestudio_id'])
                ->where('active', true)
                ->where('id', '!=', $this->editingId ?? 0)
                ->update(['active' => false]);
        }

        if ($this->editingId) {
            $referent = DiagReferent::findOrFail($this->editingId);
            $referent->update($this->form);
            $this->notification()->success(
                'Referente actualizado',
                'El referente curricular se actualizó correctamente.'
            );
        } else {
            DiagReferent::create($this->form);
            $this->notification()->success(
                'Referente creado',
                'El referente curricular se creó correctamente.'
            );
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDeleteReferent(int $id): void
    {
        $referent = DiagReferent::withCount('competencies')->findOrFail($id);

        if ($referent->competencies_count > 0) {
            $this->notification()->error(
                'No se puede eliminar',
                "El referente \"{$referent->name}\" tiene {$referent->competencies_count} competencia(s) asociada(s). Elimínelas primero."
            );
            return;
        }

        $this->dialog()->confirm([
            'title'       => 'Eliminar Referente',
            'description' => "Esta accion eliminara permanentemente el referente \"{$referent->name}\". No se puede deshacer.",
            'icon'        => 'error',
            'accept'      => [
                'label'  => 'Eliminar',
                'method' => 'deleteReferent',
                'params' => $id,
                'color'  => 'negative',
            ],
            'reject' => [
                'label' => 'Cancelar',
            ],
        ]);
    }

    public function deleteReferent(int $id): void
    {
        DiagReferent::findOrFail($id)->delete();

        $this->notification()->success(
            'Referente eliminado',
            'El referente curricular se eliminó correctamente.'
        );
    }

    /**
     * Toggle active status, enforcing versioning constraint
     */
    public function toggleReferentActive(int $id): void
    {
        $referent = DiagReferent::findOrFail($id);
        $newActive = ! $referent->active;

        if ($newActive) {
            DiagReferent::where('pestudio_id', $referent->pestudio_id)
                ->where('active', true)
                ->where('id', '!=', $id)
                ->update(['active' => false]);
        }

        $referent->update(['active' => $newActive]);

        $this->notification()->success(
            $newActive ? 'Referente activado' : 'Referente desactivado',
            $newActive
                ? 'El referente ahora esta activo como unico referente activo para este plan de estudio.'
                : 'El referente ha sido desactivado.'
        );
    }

    // ─── CRUD: COMPETENCIES ──────────────────────────────────────

    public function createCompetency(): void
    {
        $this->resetForm();
        $this->form['referent_id'] = $this->selectedReferentId;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function editCompetency(int $id): void
    {
        $competency = DiagCompetency::findOrFail($id);
        $this->form = [
            'referent_id' => $competency->referent_id,
            'pensum_id'   => $competency->pensum_id,
            'name'        => $competency->name,
            'description' => $competency->description,
        ];
        $this->editingId = $id;
        $this->showModal = true;
    }

    public function saveCompetency(): void
    {
        $this->validate([
            'form.referent_id' => 'required|integer|exists:diag_referents,id',
            'form.pensum_id'   => 'required|integer|exists:pensums,id',
            'form.name'        => 'required|string|max:255',
            'form.description' => 'nullable|string|max:1000',
        ]);

        if ($this->editingId) {
            $competency = DiagCompetency::findOrFail($this->editingId);
            $competency->update($this->form);
            $this->notification()->success(
                'Competencia actualizada',
                'La competencia se actualizó correctamente.'
            );
        } else {
            DiagCompetency::create($this->form);
            $this->notification()->success(
                'Competencia creada',
                'La competencia se creó correctamente.'
            );
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDeleteCompetency(int $id): void
    {
        $competency = DiagCompetency::withCount('indicators')->findOrFail($id);

        if ($competency->indicators_count > 0) {
            $this->notification()->error(
                'No se puede eliminar',
                "La competencia \"{$competency->name}\" tiene {$competency->indicators_count} indicador(es) asociado(s). Elimínelos primero."
            );
            return;
        }

        $this->dialog()->confirm([
            'title'       => 'Eliminar Competencia',
            'description' => "Esta accion eliminara permanentemente la competencia \"{$competency->name}\". No se puede deshacer.",
            'icon'        => 'error',
            'accept'      => [
                'label'  => 'Eliminar',
                'method' => 'deleteCompetency',
                'params' => $id,
                'color'  => 'negative',
            ],
            'reject' => [
                'label' => 'Cancelar',
            ],
        ]);
    }

    public function deleteCompetency(int $id): void
    {
        DiagCompetency::findOrFail($id)->delete();

        $this->notification()->success(
            'Competencia eliminada',
            'La competencia se eliminó correctamente.'
        );
    }

    // ─── CRUD: INDICATORS ────────────────────────────────────────

    public function createIndicator(): void
    {
        $this->resetForm();
        $this->form['competency_id'] = $this->selectedCompetencyId;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function editIndicator(int $id): void
    {
        $indicator = DiagIndicator::findOrFail($id);
        $this->form = [
            'competency_id'  => $indicator->competency_id,
            'code'           => $indicator->code,
            'description'    => $indicator->description,
            'expected_level' => $indicator->expected_level,
        ];
        $this->editingId = $id;
        $this->showModal = true;
    }

    public function saveIndicator(): void
    {
        $this->validate([
            'form.competency_id'  => 'required|integer|exists:diag_competencies,id',
            'form.code'           => 'required|string|max:50',
            'form.description'    => 'required|string|max:1000',
            'form.expected_level' => 'required|integer|in:1,2,3,4',
        ]);

        if ($this->editingId) {
            $indicator = DiagIndicator::findOrFail($this->editingId);
            $indicator->update($this->form);
            $this->notification()->success(
                'Indicador actualizado',
                'El indicador se actualizó correctamente.'
            );
        } else {
            DiagIndicator::create($this->form);
            $this->notification()->success(
                'Indicador creado',
                'El indicador se creó correctamente.'
            );
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDeleteIndicator(int $id): void
    {
        $indicator = DiagIndicator::findOrFail($id);

        $this->dialog()->confirm([
            'title'       => 'Eliminar Indicador',
            'description' => "Esta accion eliminara permanentemente el indicador \"{$indicator->code}\". No se puede deshacer.",
            'icon'        => 'error',
            'accept'      => [
                'label'  => 'Eliminar',
                'method' => 'deleteIndicator',
                'params' => $id,
                'color'  => 'negative',
            ],
            'reject' => [
                'label' => 'Cancelar',
            ],
        ]);
    }

    public function deleteIndicator(int $id): void
    {
        DiagIndicator::findOrFail($id)->delete();

        $this->notification()->success(
            'Indicador eliminado',
            'El indicador se eliminó correctamente.'
        );
    }

    // ─── DETAIL MODAL ────────────────────────────────────────────

    /**
     * Open the read-only detail modal for an item at the current level
     */
    public function openDetailModal($id): void
    {
        $this->detailItem = match ($this->viewMode) {
            'competencies' => DiagCompetency::with(['referent.pestudio', 'pensum.grado', 'pensum.asignatura'])
                ->withCount('indicators')
                ->findOrFail($id),
            'indicators' => DiagIndicator::with('competency.referent')
                ->findOrFail($id),
            default => DiagReferent::with('pestudio')
                ->withCount('competencies')
                ->addSelect(DB::raw('(select count(*) from diag_indicators where competency_id in (select id from diag_competencies where referent_id = diag_referents.id)) as total_indicators_count'))
                ->findOrFail($id),
        };

        $this->detailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->detailModal = false;
        $this->detailItem = null;
    }

    // ─── IMPORT MODAL ───────────────────────────────────────────

    /**
     * Open the import JSON modal for a specific referent.
     * Pre-selects the referent and its associated pestudio's pensums.
     */
    public function openImportModal(?int $referentId = null): void
    {
        $this->showModal = false;
        $this->detailModal = false;

        $this->resetImportForm();

        $this->importReferents = DiagReferent::with('pestudio')
            ->orderBy('name')
            ->get();

        if ($referentId) {
            $this->importReferentId = (string) $referentId;
        }

        $this->showImportModal = true;
    }

    /**
     * Close import modal and reset state.
     */
    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->resetImportForm();
    }

    /**
     * Reset import form state.
     */
    protected function resetImportForm(): void
    {
        $this->importJsonFile = null;
        $this->importReferentId = '';
        $this->importPreview = null;
        $this->importStatus = '';
        $this->importing = false;
    }

    /**
     * When the JSON file is uploaded, auto-parse it and show preview.
     * Extracts pensumId from area_formacion.pensumId in the JSON.
     */
    public function updatedImportJsonFile(): void
    {
        $this->validate([
            'importJsonFile' => 'required|file|mimes:json|max:2048',
        ]);

        $content = $this->importJsonFile->get();
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->importStatus = 'error:Error al decodificar JSON: ' . json_last_error_msg();
            $this->importPreview = null;
            return;
        }

        if (!isset($data['competencias']) || !isset($data['indicadores'])) {
            $this->importStatus = 'error:El JSON no contiene las claves "competencias" y/o "indicadores".';
            $this->importPreview = null;
            return;
        }

        // Extract pensumId from the JSON itself
        $pensumId = $data['area_formacion']['pensumId'] ?? null;

        if (!$pensumId) {
            $this->importStatus = 'error:El JSON no contiene area_formacion.pensumId.';
            $this->importPreview = null;
            return;
        }

        // Verify the pensum exists
        $pensum = Pensum::find((int) $pensumId);

        if (!$pensum) {
            $this->importStatus = "error:El pensum ID {$pensumId} del JSON no existe en la base de datos.";
            $this->importPreview = null;
            return;
        }

        $this->importPreview = $data;
        $this->importStatus = 'ok:Área: ' . ($pensum->asignatura?->name ?? '—') .
            ' | ' . count($data['competencias']) . ' competencia(s) y ' .
            count($data['indicadores']) . ' indicador(es).';
    }

    /**
     * Load and parse the uploaded JSON file (alias for manual trigger).
     */
    public function loadImportPreview(): void
    {
        if ($this->importJsonFile) {
            $this->updatedImportJsonFile();
        } else {
            $this->importStatus = 'error:No se ha cargado ningún archivo JSON.';
        }
    }

    /**
     * Import competencies and indicators from the JSON preview.
     * Uses a two-pass approach: insert competencies first, then
     * map indicators to their new competency IDs.
     */
    public function importData(): void
    {
        if (!$this->importPreview) {
            return;
        }

        $this->importing = true;
        $this->importStatus = '';

        $data = $this->importPreview;

        // Extract pensumId from the JSON data
        $pensumId = (int) ($data['area_formacion']['pensumId'] ?? 0);

        // Validate selections
        $this->validate([
            'importReferentId' => 'required|integer|exists:diag_referents,id',
        ]);

        if (!$pensumId || !Pensum::find($pensumId)) {
            $this->importStatus = 'error:El pensum ID del JSON no es válido o no existe en la base de datos.';
            $this->importing = false;
            return;
        }

        $referentId = (int) $this->importReferentId;

        DB::beginTransaction();
        try {
            $errors = [
                'competencias' => [],
                'indicadores'  => [],
            ];

            // ── Pass 1: Insert competencies ──
            $competenciasInserted = 0;
            $competencyIdMap = []; // JSON competency ID → DB competency ID

            foreach ($data['competencias'] as $comp) {
                $compName = trim($comp['nombre'] ?? '');
                $compDesc = trim($comp['descripcion'] ?? '');

                if (empty($compName)) {
                    $errors['competencias'][] = 'Competencia sin nombre, omitida.';
                    continue;
                }

                // Check for name uniqueness within this referent/pensum
                $existing = DiagCompetency::where('referent_id', $referentId)
                    ->where('pensum_id', $pensumId)
                    ->where('name', $compName)
                    ->first();

                if ($existing) {
                    $competencyIdMap[$comp['id']] = $existing->id;
                    continue;
                }

                $competency = DiagCompetency::create([
                    'referent_id' => $referentId,
                    'pensum_id'   => $pensumId,
                    'name'        => $compName,
                    'description' => $compDesc,
                ]);

                $competencyIdMap[$comp['id']] = $competency->id;
                $competenciasInserted++;
            }

            // ── Pass 2: Insert indicators ──
            $indicatorsInserted = 0;
            $indicatorsSkipped = 0;

            foreach ($data['indicadores'] as $ind) {
                $indCode = trim($ind['codigo'] ?? '');
                $indDesc = trim($ind['descripcion'] ?? '');
                $indLevel = (int) ($ind['nivel_esperado'] ?? 3);

                if (empty($indCode) || empty($indDesc)) {
                    $errors['indicadores'][] = 'Indicador sin código o descripción, omitido.';
                    continue;
                }

                // Map JSON competency_id to DB competency ID
                $jsonCompId = $ind['competency_id'];
                $dbCompId = $competencyIdMap[$jsonCompId] ?? null;

                if (!$dbCompId) {
                    $errors['indicadores'][] = "Indicador {$indCode}: no se pudo mapear a una competencia (ID JSON: {$jsonCompId}).";
                    $indicatorsSkipped++;
                    continue;
                }

                // Validate expected_level
                if (!in_array($indLevel, [1, 2, 3, 4])) {
                    $indLevel = 3;
                }

                // Check for code uniqueness within the competency
                $existing = DiagIndicator::where('competency_id', $dbCompId)
                    ->where('code', $indCode)
                    ->first();

                if ($existing) {
                    $errors['indicadores'][] = "Código '{$indCode}' ya existe para esta competencia, omitido.";
                    $indicatorsSkipped++;
                    continue;
                }

                DiagIndicator::create([
                    'competency_id'  => $dbCompId,
                    'code'           => $indCode,
                    'description'    => $indDesc,
                    'expected_level' => $indLevel,
                ]);

                $indicatorsInserted++;
            }

            DB::commit();

            // Build status message
            $parts = [];
            if ($competenciasInserted > 0) {
                $parts[] = "{$competenciasInserted} competencia(s) insertada(s)";
            }
            if ($indicatorsInserted > 0) {
                $parts[] = "{$indicatorsInserted} indicador(es) insertado(s)";
            }
            if ($indicatorsSkipped > 0) {
                $parts[] = "{$indicatorsSkipped} indicador(es) omitido(s)";
            }

            $message = 'Importación completada. ' . implode(', ', $parts) . '.';
            $this->importStatus = $parts ? "ok:{$message}" : 'warning:No se insertaron registros nuevos.';

            $this->notification()->success(
                'Importación completada',
                $message
            );

            // Reset preview so user can load another
            $this->importPreview = null;

            // Refresh the current view
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->importStatus = 'error:Error en la importación: ' . $e->getMessage();
            $this->notification()->error(
                'Error de importación',
                'Ocurrió un error durante la importación: ' . $e->getMessage()
            );
        } finally {
            $this->importing = false;
        }
    }

    /**
     * Find the blueprint JSON file for a given pensum ID.
     *
     * Scans the blueprint/lesson/json/ directory for a file
     * containing [pensumId={$id}] in its name.
     *
     * @param  int  $pensumId
     * @return string|null
     */
    protected function findBlueprintFile(int $pensumId): ?string
    {
        $pattern = base_path("blueprint/lesson/json/*[pensumId={$pensumId}]*.json");
        $files = glob($pattern);

        return $files[0] ?? null;
    }

    // ─── FILTER UPDATES ──────────────────────────────────────────

    /**
     * When pestudio filter changes for competencies, cascade grado options
     */
    public function updatedCompFilterPestudioId($value): void
    {
        if ($value) {
            $this->loadCompPestudioGrados($value);
        } else {
            $this->compFilterPestudioGrados = [];
        }
        $this->compFilterGradoId = '';
        $this->compFilterPensumId = '';
        $this->compPensums = [];
        $this->resetPage();
    }

    /**
     * When grado filter changes for competencies, reload the pensum list
     */
    public function updatedCompFilterGradoId($value): void
    {
        if ($value) {
            $this->compPensums = Pensum::where('grado_id', $value)
                ->with('grado', 'asignatura')
                ->get();
        } else {
            $this->compPensums = [];
        }
        $this->compFilterPensumId = '';
        $this->resetPage();
    }

    public function updatedCompFilterPensumId($value): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterActive(): void
    {
        $this->resetPage();
    }

    public function updatedFilterPestudioId($value): void
    {
        if ($value) {
            $this->filterPestudioGrados = Grado::where('status_active', 'true')
                ->where('pestudio_id', $value)
                ->orderBy('code')
                ->get();
        } else {
            $this->filterPestudioGrados = [];
        }
        $this->filterGradoId = '';
        $this->resetPage();
    }

    public function updatedFilterGradoId(): void
    {
        $this->resetPage();
    }

    // ─── HELPERS ─────────────────────────────────────────────────

    /**
     * Reset the form array to defaults for the current view mode
     */
    public function resetForm(): void
    {
        $this->form = match ($this->viewMode) {
            'competencies' => [
                'referent_id' => $this->selectedReferentId,
                'pensum_id'   => '',
                'name'        => '',
                'description' => '',
            ],
            'indicators' => [
                'competency_id'  => $this->selectedCompetencyId,
                'code'           => '',
                'description'    => '',
                'expected_level' => '',
            ],
            default => [
                'pestudio_id' => '',
                'name'        => '',
                'code'        => '',
                'version'     => '',
                'description' => '',
                'active'      => true,
            ],
        };

        $this->editingId = null;
    }

    /**
     * Reset all search and filter fields
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterActive = '';
        $this->filterGradoId = '';
        $this->filterPestudioGrados = [];
        $this->filterPestudioId = '';
        $this->compFilterPestudioId = '';
        $this->compFilterPestudioGrados = [];
        $this->compFilterGradoId = '';
        $this->compFilterPensumId = '';
        $this->compPensums = [];
    }

    /**
     * Load pestudios for the referent form select
     */
    protected function loadReferentPestudios(): void
    {
        $this->referentPestudios = Pestudio::where('status_active', 'true')
            ->orderBy('code')
            ->get();
    }

    /**
     * Load all active grados for general selects
     */
    protected function loadGrados(): void
    {
        $this->grados = Grado::where('status_active', 'true')
            ->orderBy('code')
            ->get();
    }

    /**
     * Load grados that have pensums (for competency filter)
     */
    protected function loadCompGrados(): void
    {
        $this->compGrados = Grado::where('status_active', 'true')
            ->whereHas('pensums')
            ->orderBy('code')
            ->get();
    }

    /**
     * Load all pensums for the competency form select
     */
    protected function loadPensums(): void
    {
        $this->pensums = Pensum::with('grado', 'asignatura')
            ->get();
    }

    /**
     * Load grados of a pestudio that have pensums, for competency filters
     */
    protected function loadCompPestudioGrados(int $pestudioId): void
    {
        $this->compFilterPestudioGrados = Grado::where('status_active', 'true')
            ->where('pestudio_id', $pestudioId)
            ->whereHas('pensums')
            ->orderBy('code')
            ->get();
    }

    // ─── MODAL CONTROL ───────────────────────────────────────────

    /**
     * Close the form modal and reset form state
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }
}
