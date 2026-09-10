<?php

namespace App\Livewire\Planning\Pevaluacion;

use App\Livewire\Forms\Planning\PevaluacionForm;
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

    // Form Object
    public PevaluacionForm $form;

    // Select lists del formulario (cascading)
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
    public $filter_asignatura = '';
    public $filter_lapso = '';

    // Opciones de los selects de filtro (independientes de las del formulario)
    public $filter_grados = [];
    public $filter_secciones = [];
    public $filter_asignaturas = [];

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
            ->orderBy('lastname')
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id');

        $this->close();
    }

    public function render()
    {
        $query = Pevaluacion::with([
                'profesor', 'lapso', 'seccion', 'pensum.asignatura', 'pensum.pestudio',
                'pensum.grado', 'grupoEstable',
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
        if ($this->filter_asignatura) {
            $query->whereHas('pensum', fn($q) => $q->where('asignatura_id', $this->filter_asignatura));
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

    public function updatedFormPestudioId($value)
    {
        $this->form->grado_id = null;
        $this->form->seccion_id = null;
        $this->form->pensum_id = null;
        $this->form->profesor_id = null;

        if ($value) {
            $this->grados = Grado::where('pestudio_id', $value)
                ->where('status_active', 'true')
                ->orderBy('name')
                ->get()
                ->pluck('full_name', 'id')
                ->toArray();

            $this->profesors = Profesor::where('status_active', true)
                ->orderBy('lastname')
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

    public function updatedFormGradoId($value)
    {
        $this->form->seccion_id = null;
        $this->form->pensum_id = null;

        if ($value && $this->form->pestudio_id) {
            $this->secciones = Seccion::where('grado_id', $value)
                ->where('status_active', true)
                ->orderBy('name')
                ->get()
                ->pluck('full_name', 'id')
                ->toArray();

            $this->pensums = Pensum::whereHas('grado', fn($q) => $q->where('id', $value))
                ->whereHas('pestudio', fn($q) => $q->where('id', $this->form->pestudio_id))
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
    public function updatingFilterGrado() { $this->resetPage(); $this->filter_seccion = ''; }
    public function updatingFilterSeccion() { $this->resetPage(); }
    public function updatingFilterAsignatura() { $this->resetPage(); }
    public function updatingFilterLapso() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }

    public function updatedFilterPestudio($value)
    {
        $this->filter_grado = '';
        $this->filter_seccion = '';
        $this->filter_secciones = [];
        $this->filter_asignatura = '';

        if ($value) {
            $this->filter_grados = Grado::where('pestudio_id', $value)
                ->where('status_active', 'true')
                ->orderBy('name')
                ->get()
                ->pluck('full_name', 'id')
                ->toArray();
        } else {
            $this->filter_grados = [];
        }

        $this->loadFilterAsignaturas();
    }

    public function updatedFilterGrado($value)
    {
        $this->filter_seccion = '';

        if ($value && $this->filter_pestudio) {
            $this->filter_secciones = Seccion::where('grado_id', $value)
                ->where('status_active', true)
                ->orderBy('name')
                ->get()
                ->pluck('full_name', 'id')
                ->toArray();
        } else {
            $this->filter_secciones = [];
        }

        $this->loadFilterAsignaturas();

        // Si la asignatura elegida ya no aplica al grado seleccionado, se limpia.
        if ($this->filter_asignatura && ! array_key_exists($this->filter_asignatura, $this->filter_asignaturas)) {
            $this->filter_asignatura = '';
        }
    }

    /**
     * Carga las asignaturas disponibles para filtrar, acotadas por el plan de
     * estudio y (si hay) el grado seleccionados. Se obtienen a través de los
     * pensums activos: pevaluacion → pensum → asignatura.
     */
    private function loadFilterAsignaturas(): void
    {
        if (! $this->filter_pestudio) {
            $this->filter_asignaturas = [];

            return;
        }

        $this->filter_asignaturas = Pensum::query()
            ->where('pestudio_id', $this->filter_pestudio)
            ->when($this->filter_grado, fn($q) => $q->where('grado_id', $this->filter_grado))
            ->where('status_active', true)
            ->with('asignatura')
            ->get()
            ->pluck('asignatura.full_name', 'asignatura.id')
            ->filter()
            ->sort()
            ->toArray();
    }

    /**
     * Restaura el estado de filtros desde localStorage en un único round-trip,
     * reconstruyendo las cascadas dependientes (grados/secciones). Evita la
     * carrera que producía hacer $wire.set() por cada clave.
     */
    public function restoreFilters(array $filters): void
    {
        $this->search = (string) ($filters['search'] ?? '');
        $this->filter_pestudio = $filters['filter_pestudio'] ?? '';
        $this->filter_profesor = $filters['filter_profesor'] ?? '';
        $this->filter_grado = $filters['filter_grado'] ?? '';
        $this->filter_seccion = $filters['filter_seccion'] ?? '';
        $this->filter_asignatura = $filters['filter_asignatura'] ?? '';
        $this->filter_lapso = $filters['filter_lapso'] ?? '';

        $this->filter_grados = $this->filter_pestudio
            ? Grado::where('pestudio_id', $this->filter_pestudio)
                ->where('status_active', 'true')
                ->orderBy('name')
                ->get()
                ->pluck('full_name', 'id')
                ->toArray()
            : [];

        $this->filter_secciones = ($this->filter_grado && $this->filter_pestudio)
            ? Seccion::where('grado_id', $this->filter_grado)
                ->where('status_active', true)
                ->orderBy('name')
                ->get()
                ->pluck('full_name', 'id')
                ->toArray()
            : [];

        $this->loadFilterAsignaturas();

        if ($this->filter_asignatura && ! array_key_exists($this->filter_asignatura, $this->filter_asignaturas)) {
            $this->filter_asignatura = '';
        }

        $this->resetPage();
    }

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
        $this->form->resetForm();
        $this->grados = [];
        $this->secciones = [];
        $this->pensums = [];
        $this->close();
        $this->modeForm = true;
    }

    public function edit($id)
    {
        $pevaluacion = Pevaluacion::with([
            'seccion', 'pensum',
        ])->findOrFail($id);

        // Check if lapso is closed
        if ($pevaluacion->is_lapso_closed) {
            $this->notification()->error(
                title: 'Lapso Cerrado',
                description: 'No se puede editar una carga académica de un lapso cerrado.'
            );
            return;
        }

        // Load form data from the pevaluacion
        $this->form->loadFromPevaluacion($pevaluacion);

        // Load cascading lists based on the loaded values
        $this->grados = Grado::where('pestudio_id', $this->form->pestudio_id)
            ->where('status_active', 'true')
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        $this->secciones = Seccion::where('grado_id', $this->form->grado_id)
            ->where('status_active', true)
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        $this->pensums = Pensum::whereHas('grado', fn($q) => $q->where('id', $this->form->grado_id))
            ->whereHas('pestudio', fn($q) => $q->where('id', $this->form->pestudio_id))
            ->where('status_active', true)
            ->with('asignatura')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        $this->close();
        $this->modeForm = true;
    }

    public function save()
    {
        $this->validate();

        // Validar unicidad compuesta
        // (lapso_id + seccion_id + pensum_id + grupo_estable_id)
        // El grupo estable forma parte de la clave: un mismo pensum puede
        // dictarse en la misma sección y lapso bajo distintos grupos estables
        // (p. ej. talleres electivos con distintos profesores).
        $grupoEstableId = $this->form->grupo_estable_id ?: null;

        $exists = Pevaluacion::where('lapso_id', $this->form->lapso_id)
            ->where('seccion_id', $this->form->seccion_id)
            ->where('pensum_id', $this->form->pensum_id);

        if ($grupoEstableId) {
            $exists->where('grupo_estable_id', $grupoEstableId);
        } else {
            $exists->whereNull('grupo_estable_id');
        }

        if ($this->form->isEditing) {
            $exists->where('id', '!=', $this->form->pevaluacion_id);
        }

        if ($exists->exists()) {
            $this->notification()->error(
                title: 'Carga Académica Duplicada',
                description: 'Ya existe una asignación para esta área de formación, sección, lapso y grupo estable.'
            );
            return;
        }

        $data = $this->form->getData();

        if ($this->form->isEditing) {
            $pevaluacion = Pevaluacion::findOrFail($this->form->pevaluacion_id);
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
            'filter_grado', 'filter_seccion', 'filter_asignatura', 'filter_lapso',
            'filter_grados', 'filter_secciones', 'filter_asignaturas',
        ]);
    }

    public function close()
    {
        $this->modeForm = false;
        $this->previewMode = false;
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
