<?php

namespace App\Livewire\Planning\Pevaluacion;

use App\Models\app\Academy\Escala;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\GrupoEstable;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
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

    // Editing flag
    public $isEditing = false;
    public $pevaluacion_id;

    // Form fields
    public $pestudio_id, $grado_id, $seccion_id;
    public $lapso_id, $pensum_id, $profesor_id;
    public $grupo_estable_id, $escala_id;
    public $nota_type = 'ACUMULATIVA';
    public $status_note_report = true;
    public $status_official = true;
    public $status_baremo;
    public $objetivo, $description, $observations, $category;

    // Select lists (cascading)
    public $pestudios;
    public $grados = [];
    public $secciones = [];
    public $pensums = [];
    public $profesors = [];
    public $profesors_search = '';
    public $lapsos;
    public $escalas;
    public $grupos_estables;

    // Filters
    public $search = '';
    public $filter_pestudio = '';
    public $filter_profesor = '';
    public $filter_grado = '';
    public $filter_seccion = '';
    public $filter_lapso = '';

    // Sorting
    public $sortField = 'pevaluacions.created_at';
    public $sortDirection = 'desc';

    // Confirm delete
    public $confirmDeleteId = null;

    // Preview
    public $previewMode = false;
    public $previewPevaluacion = null;

    // Pagination
    public $paginate = 15;

    protected $rules = [
        'pestudio_id' => 'required|integer|exists:pestudios,id',
        'grado_id' => 'required|integer|exists:grados,id',
        'seccion_id' => 'required|integer|exists:seccions,id',
        'lapso_id' => 'required|integer|exists:lapsos,id',
        'pensum_id' => 'required|integer|exists:pensums,id',
        'profesor_id' => 'required|integer|exists:profesors,id',
        'grupo_estable_id' => 'nullable|integer|exists:grupo_estables,id',
        'escala_id' => 'nullable|integer|exists:escalas,id',
        'nota_type' => 'required|in:ACUMULATIVA,PROMEDIADA',
        'status_note_report' => 'required|boolean',
        'status_official' => 'required|boolean',
        'status_baremo' => 'nullable|string|max:255',
        'objetivo' => 'nullable|string|max:500',
        'description' => 'nullable|string|max:500',
        'observations' => 'nullable|string|max:500',
        'category' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->pestudios = Pestudio::where('planning_module', true)
            ->where('status_active', 'true')
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id');

        $this->lapsos = Lapso::orderBy('id')
            ->get()
            ->pluck('full_name', 'id');

        $this->escalas = Escala::orderBy('name')
            ->get()
            ->pluck('name', 'id');

        $this->grupos_estables = GrupoEstable::where('status_active', true)
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id');

        $this->profesors = Profesor::where('status_active', true)
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id');

        $this->close();
    }

    public function render()
    {
        $query = Pevaluacion::with([
                'profesor', 'lapso', 'seccion', 'pensum.asignatura', 'pensum.pestudio',
                'pensum.grado',
            ])
            ->withCount('activities')
            ->withPlanningModule();

        // Search across related models
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('profesor', fn($sq) => $sq->where('name', 'like', "%{$this->search}%")
                    ->orWhere('lastname', 'like', "%{$this->search}%"))
                  ->orWhereHas('pensum.asignatura', fn($sq) => $sq->where('name', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%"))
                  ->orWhereHas('seccion', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('lapso', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"));
            });
        }

        // Filters
        if ($this->filter_pestudio) {
            $query->whereHas('pensum.pestudio', fn($q) => $q->where('id', $this->filter_pestudio));
        }
        if ($this->filter_profesor) {
            $query->where('profesor_id', $this->filter_profesor);
        }
        if ($this->filter_grado) {
            $query->whereHas('seccion', fn($q) => $q->where('grado_id', $this->filter_grado));
        }
        if ($this->filter_seccion) {
            $query->where('seccion_id', $this->filter_seccion);
        }
        if ($this->filter_lapso) {
            $query->where('lapso_id', $this->filter_lapso);
        }

        $pevaluacions = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->paginate);

        return view('livewire.planning.pevaluacion.index-component', [
            'pevaluacions' => $pevaluacions,
        ]);
    }

    // ─── CASCADING SELECTS ──────────────────────────────────────

    public function updatedPestudioId($value)
    {
        $this->grado_id = null;
        $this->seccion_id = null;
        $this->pensum_id = null;
        $this->profesor_id = null;

        if ($value) {
            $this->grados = Grado::where('pestudio_id', $value)
                ->where('status_active', 'true')
                ->orderBy('name')
                ->get()
                ->pluck('full_name', 'id')
                ->toArray();

            $this->profesors = Profesor::where('status_active', true)
                ->orderBy('name')
                ->get()
                ->pluck('full_name', 'id')
                ->toArray();
        } else {
            $this->grados = [];
            $this->secciones = [];
            $this->pensums = [];
        }
    }

    public function updatedGradoId($value)
    {
        $this->seccion_id = null;
        $this->pensum_id = null;

        if ($value && $this->pestudio_id) {
            $this->secciones = Seccion::where('grado_id', $value)
                ->where('status_active', true)
                ->orderBy('name')
                ->get()
                ->pluck('full_name', 'id')
                ->toArray();

            $this->pensums = Pensum::whereHas('grado', fn($q) => $q->where('id', $value))
                ->whereHas('pestudio', fn($q) => $q->where('id', $this->pestudio_id))
                ->where('status_active', true)
                ->with('asignatura')
                ->get()
                ->pluck('full_name', 'id')
                ->toArray();
        } else {
            $this->secciones = [];
            $this->pensums = [];
        }
    }

    // ─── FILTERS UPDATE RESET PAGE ──────────────────────────────

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterPestudio() { $this->resetPage(); }
    public function updatingFilterProfesor() { $this->resetPage(); }
    public function updatingFilterGrado() { $this->resetPage(); }
    public function updatingFilterSeccion() { $this->resetPage(); }
    public function updatingFilterLapso() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }

    // ─── SORTING ────────────────────────────────────────────────

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // ─── FORM ────────────────────────────────────────────────────

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->pevaluacion_id = null;
        $this->grados = [];
        $this->secciones = [];
        $this->pensums = [];
        $this->close();
        $this->modeForm = true;
    }

    public function edit($id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id);

        // Check if lapso is closed
        if ($pevaluacion->is_lapso_closed) {
            $this->notification()->error(
                title: 'Lapso Cerrado',
                description: 'No se puede editar una carga académica de un lapso cerrado.'
            );
            return;
        }

        $this->pevaluacion_id = $pevaluacion->id;
        $this->pestudio_id = $pevaluacion->pensum->pestudio_id;
        $this->grado_id = $pevaluacion->seccion->grado_id;
        $this->seccion_id = $pevaluacion->seccion_id;
        $this->lapso_id = $pevaluacion->lapso_id;
        $this->pensum_id = $pevaluacion->pensum_id;
        $this->profesor_id = $pevaluacion->profesor_id;
        $this->grupo_estable_id = $pevaluacion->grupo_estable_id;
        $this->escala_id = $pevaluacion->escala_id;
        $this->nota_type = $pevaluacion->nota_type ?? 'ACUMULATIVA';
        $this->status_note_report = $pevaluacion->status_note_report ?? true;
        $this->status_official = $pevaluacion->status_official ?? true;
        $this->status_baremo = $pevaluacion->status_baremo;
        $this->objetivo = $pevaluacion->objetivo;
        $this->description = $pevaluacion->description;
        $this->observations = $pevaluacion->observations;
        $this->category = $pevaluacion->category;

        // Load cascading lists
        $this->grados = Grado::where('pestudio_id', $this->pestudio_id)
            ->where('status_active', 'true')
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        $this->secciones = Seccion::where('grado_id', $this->grado_id)
            ->where('status_active', true)
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        $this->pensums = Pensum::whereHas('grado', fn($q) => $q->where('id', $this->grado_id))
            ->whereHas('pestudio', fn($q) => $q->where('id', $this->pestudio_id))
            ->where('status_active', true)
            ->with('asignatura')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        $this->isEditing = true;
        $this->close();
        $this->modeForm = true;
    }

    public function save()
    {
        $this->status_note_report = filter_var($this->status_note_report, FILTER_VALIDATE_BOOLEAN);
        $this->status_official = filter_var($this->status_official, FILTER_VALIDATE_BOOLEAN);

        $this->validate();

        // Validar unicidad compuesta (lapso_id + seccion_id + pensum_id)
        $exists = Pevaluacion::where('lapso_id', $this->lapso_id)
            ->where('seccion_id', $this->seccion_id)
            ->where('pensum_id', $this->pensum_id);

        if ($this->isEditing) {
            $exists->where('id', '!=', $this->pevaluacion_id);
        }

        if ($exists->exists()) {
            $this->notification()->error(
                title: 'Carga Académica Duplicada',
                description: 'Ya existe una asignación para esta área de formación, sección y lapso.'
            );
            return;
        }

        $data = [
            'profesor_id' => $this->profesor_id,
            'pensum_id' => $this->pensum_id,
            'seccion_id' => $this->seccion_id,
            'lapso_id' => $this->lapso_id,
            'grupo_estable_id' => $this->grupo_estable_id ?: null,
            'escala_id' => $this->escala_id ?: null,
            'nota_type' => $this->nota_type,
            'status_note_report' => $this->status_note_report,
            'status_official' => $this->status_official,
            'status_baremo' => $this->status_baremo ?: null,
            'objetivo' => $this->objetivo ?: null,
            'description' => $this->description ?: null,
            'observations' => $this->observations ?: null,
            'category' => $this->category ?: null,
        ];

        if ($this->isEditing) {
            $pevaluacion = Pevaluacion::findOrFail($this->pevaluacion_id);
            $pevaluacion->update($data);
            $this->notification()->success(
                title: 'Carga Académica Actualizada',
                description: 'La asignación se actualizó correctamente.'
            );
        } else {
            Pevaluacion::create($data);
            $this->notification()->success(
                title: 'Carga Académica Creada',
                description: 'La asignación se creó correctamente.'
            );
        }

        $this->close();
        $this->modeIndex = true;
    }

    // ─── DELETE ──────────────────────────────────────────────────

    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmDeleteId = null;
    }

    public function destroy()
    {
        $pevaluacion = Pevaluacion::withCount('activities')
            ->findOrFail($this->confirmDeleteId);

        if ($pevaluacion->activities_count > 0) {
            $this->notification()->error(
                title: 'No se puede eliminar',
                description: "La asignación tiene {$pevaluacion->activities_count} actividad(es) registrada(s). Elimínelas primero."
            );
            $this->cancelDelete();
            return;
        }

        $pevaluacion->delete();
        $this->cancelDelete();

        $this->notification()->success(
            title: 'Carga Académica Eliminada',
            description: 'La asignación se eliminó correctamente.'
        );
    }

    // ─── PREVIEW ────────────────────────────────────────────────

    public function showPreview($id)
    {
        $this->previewPevaluacion = Pevaluacion::with([
            'profesor', 'lapso', 'seccion', 'pensum.asignatura', 'pensum.pestudio',
            'pensum.grado', 'escala', 'grupoEstable',
        ])
        ->withCount('activities')
        ->findOrFail($id);
        $this->previewMode = true;
    }

    public function closePreview()
    {
        $this->previewMode = false;
        $this->previewPevaluacion = null;
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    public function resetFilters()
    {
        $this->reset([
            'search', 'filter_pestudio', 'filter_profesor',
            'filter_grado', 'filter_seccion', 'filter_lapso',
        ]);
    }

    public function resetForm()
    {
        $this->reset([
            'pestudio_id', 'grado_id', 'seccion_id', 'lapso_id',
            'pensum_id', 'profesor_id', 'grupo_estable_id', 'escala_id',
            'nota_type', 'status_baremo', 'objetivo', 'description',
            'observations', 'category',
        ]);
        $this->status_note_report = true;
        $this->status_official = true;
        $this->nota_type = 'ACUMULATIVA';
    }

    public function close()
    {
        $this->modeForm = false;
        $this->previewMode = false;
    }

    #[Layout('layouts.dashboard')]
    public function layout() {}
}
