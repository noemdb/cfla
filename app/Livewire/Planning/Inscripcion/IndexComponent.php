<?php

namespace App\Livewire\Planning\Inscripcion;

use App\Models\app\Academy\Escolaridad;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\GrupoEstable;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Programacion;
use App\Models\app\Academy\Seccion;
use App\Models\app\Academy\Tinscripcion;
use App\Models\app\Learner\Estudiant;
use App\Services\Planning\InscripcionCsvImporter;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WireUiActions, WithFileUploads, WithPagination;

    // Modal modes
    public $modeIndex = true;

    public $modeForm = false;

    public $isEditing = false;

    public $inscripcion_id;

    // ─── Wizard state ──────────────────────────────────────────
    public $wizardStep = 1;            // 1–4

    public $searchStudent = '';

    public $studentSearchResults = [];

    public $selectedStudentData = null;

    // Form fields
    public $pestudio_id = '';

    public $grado_id = '';

    public $seccion_id;

    public $estudiant_id;

    public $tipo_id;

    public $escolaridad_id;

    public $programacion_id;

    public $grupo_estable_id;

    public $observations;

    // Select lists for form
    public $gradosForm = [];

    public $seccionesForm = [];

    // Search & filters
    public $search = '';

    public $filterPestudio = '';

    public $filterGrado = '';

    public $filterSeccion = '';

    public $filterTipo = '';

    public $paginate = 15;

    // Confirm delete
    public $confirmDeleteId = null;

    // View student profile
    public bool $showStudentModal = false;

    public ?array $viewingStudent = null;

    // ─── CSV import (x-dialog) ─────────────────────────────────
    public $importCsvFile = null;

    public $importPestudioId = '';

    public $importTipoId = '';

    public $importEscolaridadId = '';

    public $importProgramacionId = '';

    public $importGrupoEstableId = '';

    public $importPlanPagoId = '';

    public $importRepresentantCi = '';

    public bool $importUpdateAcademicData = false;

    public bool $importUpdateStudentNames = false;

    public array $importPlanPagos = [];

    public string $importFileHash = '';

    public array $importRows = [];

    public array $importPreview = [];

    public array $importReport = [];

    public string $importStatus = '';

    public string $importStatusType = '';

    public bool $importing = false;

    protected $rules = [
        'estudiant_id' => 'required|integer|exists:estudiants,id',
        'seccion_id' => 'required|integer|exists:seccions,id',
        'tipo_id' => 'required|integer|exists:tinscripcions,id',
        'escolaridad_id' => 'required|integer|exists:escolaridads,id',
        'programacion_id' => 'required|integer|exists:programacions,id',
        'grupo_estable_id' => 'nullable|integer|exists:grupo_estables,id',
        'observations' => 'nullable|string|max:250',
    ];

    public function mount(): void
    {
        //
    }

    // ─── Wizard: Step navigation ───────────────────────────────

    public function selectStudent(int $id): void
    {
        $this->estudiant_id = $id;
        $this->selectedStudentData = Estudiant::with('representant')
            ->find($id)?->toArray();
        $this->resetValidation('estudiant_id');
    }

    public function nextStep(): void
    {
        // Validate current step before advancing
        if ($this->wizardStep === 1) {
            if (! $this->estudiant_id) {
                $this->addError('estudiant_id', 'Debes seleccionar un estudiante para continuar.');

                return;
            }
        }
        if ($this->wizardStep === 2) {
            if (! $this->seccion_id) {
                $this->addError('seccion_id', 'Debes seleccionar una sección para continuar.');

                return;
            }
        }
        if ($this->wizardStep === 3) {
            $this->validate([
                'tipo_id' => 'required|integer|exists:tinscripcions,id',
                'escolaridad_id' => 'required|integer|exists:escolaridads,id',
                'programacion_id' => 'required|integer|exists:programacions,id',
            ]);
        }

        $this->resetValidation();
        $this->wizardStep++;
    }

    public function prevStep(): void
    {
        $this->resetValidation();
        if ($this->wizardStep > 1) {
            $this->wizardStep--;
        }
    }

    public function goToStep(int $step): void
    {
        // Only allow going back to completed steps
        if ($step < $this->wizardStep) {
            $this->resetValidation();
            $this->wizardStep = $step;
        }
    }

    // ─── Student search (real-time) ────────────────────────────

    public function updatedSearchStudent($value): void
    {
        if (strlen($value) < 2) {
            $this->studentSearchResults = [];

            return;
        }

        $this->studentSearchResults = Estudiant::where('name', 'like', "%{$value}%")
            ->orWhere('lastname', 'like', "%{$value}%")
            ->orWhere('ci_estudiant', 'like', "%{$value}%")
            ->with('representant')
            ->orderBy('name')
            ->take(30)
            ->get()
            ->toArray();
    }

    // ─── Filtro cascada: Pestudio → Grado → Seccion (para LISTADO) ──

    public function updatedFilterPestudio($value): void
    {
        $this->filterGrado = '';
        $this->filterSeccion = '';
        $this->resetPage();
    }

    public function updatedFilterGrado($value): void
    {
        $this->filterSeccion = '';
        $this->resetPage();
    }

    public function updatedFilterSeccion(): void
    {
        $this->resetPage();
    }

    // ─── Cascada para FORMULARIO ───────────────────────────────

    public function updatedPestudioId($value): void
    {
        $this->grado_id = '';
        $this->seccion_id = '';
        $this->seccionesForm = [];

        $this->gradosForm = $value
            ? Grado::where('pestudio_id', $value)
                ->where('status_active', 'true')
                ->orderBy('name')->pluck('name', 'id')->toArray()
            : [];
    }

    public function updatedGradoId($value): void
    {
        $this->seccion_id = '';

        $this->seccionesForm = $value
            ? Seccion::where('grado_id', $value)
                ->where('status_active', 'true')
                ->orderBy('name')->pluck('name', 'id')->toArray()
            : [];
    }

    // ─── CRUD ──────────────────────────────────────────────────

    public function create(): void
    {
        $this->resetForm();
        $this->wizardStep = 1;
        $this->searchStudent = '';
        $this->studentSearchResults = [];
        $this->selectedStudentData = null;
        $this->isEditing = false;
        $this->inscripcion_id = null;
        $this->modeIndex = false;
        $this->modeForm = true;
    }

    public function edit(int $id): void
    {
        $inscripcion = Inscripcion::with([
            'seccion.grado', 'estudiant', 'tipo', 'escolaridad', 'programacion', 'grupoEstable',
        ])->findOrFail($id);

        $this->inscripcion_id = $id;
        $this->isEditing = true;

        if ($inscripcion->seccion?->grado) {
            $grado = $inscripcion->seccion->grado;
            $this->pestudio_id = $grado->pestudio_id;
            $this->grado_id = $grado->id;

            $this->gradosForm = Grado::where('pestudio_id', $this->pestudio_id)
                ->where('status_active', 'true')
                ->orderBy('name')->pluck('name', 'id')->toArray();

            $this->seccionesForm = Seccion::where('grado_id', $this->grado_id)
                ->where('status_active', 'true')
                ->orderBy('name')->pluck('name', 'id')->toArray();
        }

        $this->seccion_id = $inscripcion->seccion_id;
        $this->estudiant_id = $inscripcion->estudiant_id;
        $this->tipo_id = $inscripcion->tipo_id;
        $this->escolaridad_id = $inscripcion->escolaridad_id;
        $this->programacion_id = $inscripcion->programacion_id;
        $this->grupo_estable_id = $inscripcion->grupo_estable_id;
        $this->observations = $inscripcion->observations;

        $this->modeIndex = false;
        $this->modeForm = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->isEditing) {
            $inscripcion = Inscripcion::findOrFail($this->inscripcion_id);
            $inscripcion->update([
                'seccion_id' => $this->seccion_id,
                'tipo_id' => $this->tipo_id,
                'escolaridad_id' => $this->escolaridad_id,
                'programacion_id' => $this->programacion_id,
                'grupo_estable_id' => $this->grupo_estable_id ?: null,
                'observations' => $this->observations,
            ]);

            $this->notification()->success(
                title: 'Inscripción actualizada',
                description: 'La inscripción se actualizó correctamente.'
            );
        } else {
            $existing = Inscripcion::where('estudiant_id', $this->estudiant_id)->first();
            if ($existing) {
                $this->addError('estudiant_id', 'Este estudiante ya tiene una inscripción activa.');
                $this->notification()->error(
                    title: 'Estudiante ya inscrito',
                    description: 'Este estudiante ya tiene una inscripción activa. Cada estudiante puede tener solo una inscripción.'
                );

                return;
            }

            Inscripcion::create([
                'estudiant_id' => $this->estudiant_id,
                'seccion_id' => $this->seccion_id,
                'tipo_id' => $this->tipo_id,
                'escolaridad_id' => $this->escolaridad_id,
                'programacion_id' => $this->programacion_id,
                'grupo_estable_id' => $this->grupo_estable_id ?: null,
                'observations' => $this->observations,
            ]);

            $this->notification()->success(
                title: 'Inscripción creada',
                description: 'La inscripción se creó correctamente.'
            );
        }

        $this->cancelForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmDeleteId = $id;
    }

    public function destroy(): void
    {
        Inscripcion::findOrFail($this->confirmDeleteId)->delete();
        $this->confirmDeleteId = null;

        $this->notification()->success(
            title: 'Inscripción eliminada',
            description: 'La inscripción fue eliminada correctamente.'
        );
    }

    // ─── CSV import ────────────────────────────────────────────

    public function openImportModal(): void
    {
        $this->resetImportForm();

        $this->importTipoId = Tinscripcion::orderBy('id')->value('id') ?? '';
        $this->importEscolaridadId = Escolaridad::orderBy('id')->value('id') ?? '';
        $this->importProgramacionId = Programacion::orderBy('id')->value('id') ?? '';
        $this->importPlanPagos = $this->importer()->planPagoOptions();

        $this->reopenImportDialog();
    }

    public function closeImportModal(): void
    {
        $this->resetImportForm();
    }

    protected function importer(): InscripcionCsvImporter
    {
        return app(InscripcionCsvImporter::class);
    }

    protected function reopenImportDialog(): void
    {
        // No pasar 'title' en las opciones: el componente <x-dialog> ya define
        // el prop title, por lo que WireUI no renderiza x-ref="title" y su
        // processDialog() lanza "Cannot set properties of undefined (innerHTML)".
        // 'close' => false desactiva el botón "OK" por defecto (full-width);
        // el diálogo se cierra con nuestro botón "Cerrar" o con el fondo.
        $this->dialog()->id('csv-import')->show([
            'icon' => '',
            'close' => false,
        ]);
    }

    protected function resetImportForm(): void
    {
        $this->importCsvFile = null;
        $this->importRows = [];
        $this->importPreview = [];
        $this->importReport = [];
        $this->importStatus = '';
        $this->importStatusType = '';
        $this->importing = false;
        $this->importFileHash = '';
        $this->resetValidation();
    }

    public function updatedImportCsvFile(): void
    {
        $this->validate([
            'importCsvFile' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $this->importRows = [];
        $this->importPreview = [];
        $this->importReport = [];
        $this->importStatus = '';
        $this->importStatusType = '';
        $this->importFileHash = '';

        try {
            $this->importRows = $this->importer()->parse($this->importCsvFile->getRealPath());
            $this->importFileHash = hash_file('sha256', $this->importCsvFile->getRealPath()) ?: '';
        } catch (\Throwable $e) {
            $this->importStatusType = 'error';
            $this->importStatus = 'No se pudo leer el archivo: '.$e->getMessage();
            $this->reopenImportDialog();

            return;
        }

        if (empty($this->importRows)) {
            $this->importStatusType = 'error';
            $this->importStatus = 'No se detectaron filas. Verifica el separador (coma, punto y coma o tabulación) y la codificación del archivo.';
            $this->reopenImportDialog();

            return;
        }

        $this->refreshImportPreview();
        $this->reopenImportDialog();
    }

    public function updatedImportPestudioId(): void
    {
        $this->refreshPreviewIfLoaded();
    }

    public function updatedImportUpdateAcademicData(): void
    {
        $this->refreshPreviewIfLoaded();
    }

    public function updatedImportUpdateStudentNames(): void
    {
        $this->refreshPreviewIfLoaded();
    }

    protected function refreshPreviewIfLoaded(): void
    {
        if (! empty($this->importRows)) {
            $this->refreshImportPreview();
        }

        $this->reopenImportDialog();
    }

    protected function refreshImportPreview(): void
    {
        $this->importPreview = $this->importer()->preview($this->importRows, $this->importOptions());

        $ok = count(array_filter($this->importPreview, fn ($row) => $row['status'] === 'ok'));
        $bad = count($this->importPreview) - $ok;
        $duplicates = count(array_filter($this->importPreview, fn ($row) => ! empty($row['duplicate'])));

        $this->importStatusType = $ok > 0 ? 'ok' : 'error';
        $this->importStatus = "{$ok} fila(s) lista(s) para importar"
            .($bad ? " · {$bad} con errores" : '')
            .($duplicates ? " · {$duplicates} duplicada(s) en el archivo" : '')
            .'.';

        if ($this->importFileHash && Cache::has($this->importCacheKey())) {
            $this->importStatusType = 'error';
            $this->importStatus = 'Este archivo ya fue importado recientemente. '.$this->importStatus;
        }

        $this->dispatch('inscripcion-import-preview-ready');
    }

    protected function importCacheKey(): string
    {
        return 'inscripcion-csv-imported:'.$this->importFileHash;
    }

    /**
     * @return array<string, mixed>
     */
    protected function importOptions(): array
    {
        return [
            'pestudio_id' => $this->importPestudioId,
            'tipo_id' => $this->importTipoId,
            'escolaridad_id' => $this->importEscolaridadId,
            'programacion_id' => $this->importProgramacionId,
            'grupo_estable_id' => $this->importGrupoEstableId,
            'plan_pago_id' => $this->importPlanPagoId,
            'representant_ci' => $this->importRepresentantCi,
            'update_academic_data' => (bool) $this->importUpdateAcademicData,
            'update_student_names' => (bool) $this->importUpdateStudentNames,
            'observations_note' => 'Importado desde CSV ('.now()->format('d/m/Y').')',
        ];
    }

    public function importInscriptions(): void
    {
        $this->validate([
            'importTipoId' => 'required|integer|exists:tinscripcions,id',
            'importEscolaridadId' => 'required|integer|exists:escolaridads,id',
            'importProgramacionId' => 'required|integer|exists:programacions,id',
            'importGrupoEstableId' => 'nullable|integer|exists:grupo_estables,id',
            'importPlanPagoId' => 'nullable|integer|exists:planpagos,id',
        ]);

        if (empty($this->importPreview)) {
            $this->importStatusType = 'error';
            $this->importStatus = 'No hay datos analizados para importar.';
            $this->reopenImportDialog();

            return;
        }

        $this->importing = true;
        $this->importReport = [];

        $report = $this->importer()->import($this->importPreview, $this->importOptions());

        $this->importing = false;
        $this->importReport = $report['errors'];

        $summary = [];
        if ($report['created']) {
            $summary[] = "{$report['created']} estudiante(s) creado(s)";
        }
        if ($report['inscribed']) {
            $summary[] = "{$report['inscribed']} inscripción(es) creada(s)";
        }
        if ($report['updated']) {
            $summary[] = "{$report['updated']} inscripción(es) actualizada(s)";
        }
        if ($report['unchanged']) {
            $summary[] = "{$report['unchanged']} sin cambios";
        }
        if ($report['skipped']) {
            $summary[] = "{$report['skipped']} omitida(s)";
        }

        $message = $summary ? implode(', ', $summary).'.' : 'No se procesó ninguna fila.';
        $processed = $report['created'] + $report['inscribed'] + $report['updated'];

        $this->importStatusType = $processed > 0 ? 'ok' : 'error';
        $this->importStatus = $message;

        if ($processed > 0) {
            Cache::put($this->importCacheKey(), now()->toIso8601String(), now()->addDay());
            $this->notification()->success('Importación completada', $message);
        } else {
            $this->notification()->warning('Importación finalizada', $message);
        }

        $this->importCsvFile = null;
        $this->importRows = [];
        $this->importPreview = [];
        $this->resetPage();
        $this->reopenImportDialog();
    }

    // ─── View Student Profile ─────────────────────────────────

    public function viewStudent(int $id): void
    {
        $inscripcion = Inscripcion::with([
            'estudiant.representant',
            'estudiant.user',
            'seccion.grado.pestudio',
            'tipo',
            'escolaridad',
            'programacion',
            'grupoEstable',
        ])->findOrFail($id);

        $this->viewingStudent = $inscripcion->toArray();
        $this->showStudentModal = true;
    }

    public function closeViewStudent(): void
    {
        $this->showStudentModal = false;
        $this->viewingStudent = null;
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->modeIndex = true;
        $this->modeForm = false;
    }

    private function resetForm(): void
    {
        $this->reset([
            'pestudio_id', 'grado_id', 'seccion_id', 'estudiant_id',
            'tipo_id', 'escolaridad_id', 'programacion_id',
            'grupo_estable_id', 'observations', 'inscripcion_id', 'isEditing',
            'gradosForm', 'seccionesForm', 'wizardStep',
            'searchStudent', 'studentSearchResults', 'selectedStudentData',
        ]);
        $this->resetValidation();
    }

    // ─── Render ────────────────────────────────────────────────

    #[Layout('planning.layouts.app')]
    public function render(): \Illuminate\View\View
    {
        $query = Inscripcion::with([
            'estudiant',
            'seccion.grado.pestudio',
            'tipo',
            'escolaridad',
            'programacion',
            'grupoEstable',
        ]);

        if ($this->search) {
            $query->whereHas('estudiant', function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('lastname', 'like', "%{$this->search}%")
                    ->orWhere('ci_estudiant', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterPestudio) {
            $query->whereHas('seccion.grado', fn ($q) => $q->where('pestudio_id', $this->filterPestudio)
            );
        }

        if ($this->filterGrado) {
            $query->whereHas('seccion', fn ($q) => $q->where('grado_id', $this->filterGrado)
            );
        }

        if ($this->filterSeccion) {
            $query->where('seccion_id', $this->filterSeccion);
        }

        if ($this->filterTipo) {
            $query->where('tipo_id', $this->filterTipo);
        }

        $inscripcions = $query->orderBy('created_at', 'desc')
            ->paginate($this->paginate);

        $pestudios = Pestudio::where('status_active', 'true')
            ->orderBy('name')->pluck('name', 'id');

        $grados = $this->filterPestudio
            ? Grado::where('pestudio_id', $this->filterPestudio)
                ->where('status_active', 'true')
                ->orderBy('name')->pluck('name', 'id')
            : collect();

        $secciones = $this->filterGrado
            ? Seccion::where('grado_id', $this->filterGrado)
                ->where('status_active', 'true')
                ->orderBy('name')->pluck('name', 'id')
            : collect();

        $tipos = Tinscripcion::orderBy('name')->pluck('name', 'id');

        // Form selects
        $pestudiosForm = Pestudio::where('status_active', 'true')
            ->orderBy('name')->pluck('name', 'id');

        $estudiantsList = Estudiant::orderBy('name')
            ->take(200)
            ->get()
            ->mapWithKeys(fn ($e) => [
                $e->id => "{$e->name} {$e->lastname} — {$e->ci_estudiant}",
            ])
            ->toArray();

        $tiposForm = Tinscripcion::orderBy('name')->pluck('name', 'id');
        $escolaridadsForm = Escolaridad::orderBy('name')->pluck('name', 'id');
        $programacionsForm = Programacion::orderBy('name')->pluck('name', 'id');
        $grupoEstablesForm = GrupoEstable::where('status_active', 'true')
            ->orderBy('name')->pluck('name', 'id');

        return view('livewire.planning.inscripcion.index-component', [
            'inscripcions' => $inscripcions,
            'pestudios' => $pestudios,
            'grados' => $grados,
            'secciones' => $secciones,
            'tipos' => $tipos,
            'pestudiosForm' => $pestudiosForm,
            'gradosForm' => collect($this->gradosForm),
            'seccionesForm' => collect($this->seccionesForm),
            'estudiantsList' => $estudiantsList,
            'tiposForm' => $tiposForm,
            'escolaridadsForm' => $escolaridadsForm,
            'programacionsForm' => $programacionsForm,
            'grupoEstablesForm' => $grupoEstablesForm,
            'estudiants' => $estudiantsList,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterTipo()
    {
        $this->resetPage();
    }

    public function updatingPaginate()
    {
        $this->resetPage();
    }
}
