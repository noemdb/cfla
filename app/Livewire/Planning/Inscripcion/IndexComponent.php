<?php

namespace App\Livewire\Planning\Inscripcion;

use App\Models\app\Academy\Grado;
use App\Models\app\Academy\GrupoEstable;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Seccion;
use App\Models\app\Academy\Tinscripcion;
use App\Models\app\Academy\Escolaridad;
use App\Models\app\Academy\Programacion;
use App\Models\app\Learner\Estudiant;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WithPagination, WireUiActions;

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

    protected $rules = [
        'estudiant_id'       => 'required|integer|exists:estudiants,id',
        'seccion_id'         => 'required|integer|exists:seccions,id',
        'tipo_id'            => 'required|integer|exists:tinscripcions,id',
        'escolaridad_id'     => 'required|integer|exists:escolaridads,id',
        'programacion_id'    => 'required|integer|exists:programacions,id',
        'grupo_estable_id'   => 'nullable|integer|exists:grupo_estables,id',
        'observations'       => 'nullable|string|max:250',
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
            if (!$this->estudiant_id) {
                $this->addError('estudiant_id', 'Debes seleccionar un estudiante para continuar.');
                return;
            }
        }
        if ($this->wizardStep === 2) {
            if (!$this->seccion_id) {
                $this->addError('seccion_id', 'Debes seleccionar una sección para continuar.');
                return;
            }
        }
        if ($this->wizardStep === 3) {
            $this->validate([
                'tipo_id'          => 'required|integer|exists:tinscripcions,id',
                'escolaridad_id'   => 'required|integer|exists:escolaridads,id',
                'programacion_id'  => 'required|integer|exists:programacions,id',
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
            'seccion.grado', 'estudiant', 'tipo', 'escolaridad', 'programacion', 'grupoEstable'
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
                'seccion_id'       => $this->seccion_id,
                'tipo_id'          => $this->tipo_id,
                'escolaridad_id'   => $this->escolaridad_id,
                'programacion_id'  => $this->programacion_id,
                'grupo_estable_id' => $this->grupo_estable_id ?: null,
                'observations'     => $this->observations,
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
                'estudiant_id'     => $this->estudiant_id,
                'seccion_id'       => $this->seccion_id,
                'tipo_id'          => $this->tipo_id,
                'escolaridad_id'   => $this->escolaridad_id,
                'programacion_id'  => $this->programacion_id,
                'grupo_estable_id' => $this->grupo_estable_id ?: null,
                'observations'     => $this->observations,
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

    // ─── View Student Profile ─────────────────────────────────

    public function viewStudent(int $id): void
    {
        $inscripcion = Inscripcion::with([
            'estudiant.representant',
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
            $query->whereHas('seccion.grado', fn($q) =>
                $q->where('pestudio_id', $this->filterPestudio)
            );
        }

        if ($this->filterGrado) {
            $query->whereHas('seccion', fn($q) =>
                $q->where('grado_id', $this->filterGrado)
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
            ->mapWithKeys(fn($e) => [
                $e->id => "{$e->name} {$e->lastname} — {$e->ci_estudiant}"
            ])
            ->toArray();

        $tiposForm = Tinscripcion::orderBy('name')->pluck('name', 'id');
        $escolaridadsForm = Escolaridad::orderBy('name')->pluck('name', 'id');
        $programacionsForm = Programacion::orderBy('name')->pluck('name', 'id');
        $grupoEstablesForm = GrupoEstable::where('status_active', 'true')
            ->orderBy('name')->pluck('name', 'id');

        return view('livewire.planning.inscripcion.index-component', [
            'inscripcions'      => $inscripcions,
            'pestudios'         => $pestudios,
            'grados'            => $grados,
            'secciones'         => $secciones,
            'tipos'             => $tipos,
            'pestudiosForm'     => $pestudiosForm,
            'gradosForm'        => collect($this->gradosForm),
            'seccionesForm'     => collect($this->seccionesForm),
            'estudiantsList'    => $estudiantsList,
            'tiposForm'         => $tiposForm,
            'escolaridadsForm'  => $escolaridadsForm,
            'programacionsForm' => $programacionsForm,
            'grupoEstablesForm' => $grupoEstablesForm,
            'estudiants'        => $estudiantsList,
        ]);
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterTipo() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }
}
