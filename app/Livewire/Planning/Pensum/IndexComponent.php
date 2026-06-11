<?php

namespace App\Livewire\Planning\Pensum;

use App\Livewire\Forms\Planning\PensumForm;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pestudio;
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
    public PensumForm $form;

    // Select lists (cascading)
    public $pestudios;
    public $grados = [];
    public $asignaturas = [];

    // Search & filters
    public $search = '';
    public $filter_pestudio = '';
    public $filter_grado = '';
    public $listGrados = [];

    // Sort
    public string $sortField = 'pestudio_id';
    public string $sortDirection = 'asc';

    // Confirm delete
    public $confirmDeleteId = null;

    // Preview
    public $previewMode = false;
    public $previewPensum = null;

    public function mount()
    {
        $this->pestudios = Pestudio::where('status_active', 'true')
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id');

        $this->close();
    }

    // ─── SORT ─────────────────────────────────────────────────────

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // ─── FILTERS ──────────────────────────────────────────────────

    public function updatedFilterPestudio($value): void
    {
        $this->filter_grado = '';
        $this->listGrados = $value
            ? Grado::where('pestudio_id', $value)
                ->where('status_active', 'true')
                ->orderBy('name')
                ->get()
                ->pluck('full_name', 'id')
                ->toArray()
            : [];
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterPestudio() { $this->resetPage(); }
    public function updatingFilterGrado() { $this->resetPage(); }

    // ─── RENDER ───────────────────────────────────────────────────

    public function render()
    {
        $query = Pensum::with(['pestudio', 'grado', 'asignatura', 'diagCompetencies.referent'])
            ->withCount(['pevaluacions', 'diagCompetencies']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('pestudio', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('grado', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('asignatura', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('asignatura', fn($sq) => $sq->where('code', 'like', "%{$this->search}%"));
            });
        }

        if ($this->filter_pestudio) {
            $query->where('pestudio_id', $this->filter_pestudio);
        }

        if ($this->filter_grado) {
            $query->where('grado_id', $this->filter_grado);
        }

        // Aplicar ordenamiento
        if (in_array($this->sortField, ['pestudio_id', 'grado_id', 'asignatura_id', 'status_component', 'status_active', 'id'])) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } elseif (in_array($this->sortField, ['pevaluacions_count', 'diag_competencies_count'])) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('pestudio_id')->orderBy('grado_id')->orderBy('asignatura_id');
        }

        $pensums = $query->paginate(15);

        return view('livewire.planning.pensum.index-component', [
            'pensums' => $pensums,
        ]);
    }

    // ─── CASCADING SELECTS ──────────────────────────────────────

    public function updatedFormPestudioId($value)
    {
        // During edit, the form values are already set — don't cascade-null them
        if ($this->form->isEditing) {
            return;
        }

        $this->form->grado_id = null;
        $this->form->asignatura_id = null;

        if ($value) {
            $this->grados = Grado::where('pestudio_id', $value)
                ->where('status_active', 'true')
                ->orderBy('name')
                ->get()
                ->pluck('full_name', 'id')
                ->toArray();

            $this->asignaturas = Asignatura::where('pestudio_id', $value)
                ->orderBy('name')
                ->get()
                ->pluck('full_name', 'id')
                ->toArray();
        } else {
            $this->grados = [];
            $this->asignaturas = [];
        }
    }

    // ─── FORM ────────────────────────────────────────────────────

    public function create()
    {
        $this->form->resetForm();
        $this->grados = [];
        $this->asignaturas = [];
        $this->close();
        $this->modeForm = true;
    }

    public function edit($id)
    {
        $pensum = Pensum::findOrFail($id);
        $this->form->loadFromPensum($pensum);

        // Cargar listas para el pestudio seleccionado
        $this->grados = Grado::where('pestudio_id', $this->form->pestudio_id)
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        $this->asignaturas = Asignatura::where('pestudio_id', $this->form->pestudio_id)
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        $this->close();
        $this->modeForm = true;
    }

    public function save()
    {
        $this->validate();

        try {
            $this->form->save();

            $this->notification()->success(
                title: $this->form->isEditing ? 'Pensum Actualizado' : 'Pensum Creado',
                description: $this->form->isEditing
                    ? 'El pensum se actualizó correctamente.'
                    : 'El pensum se creó correctamente.'
            );
        } catch (\RuntimeException $e) {
            $this->notification()->error(
                title: 'Pensum Duplicado',
                description: $e->getMessage()
            );
            return;
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
        $pensum = Pensum::withCount('pevaluacions')
            ->findOrFail($this->confirmDeleteId);

        if ($pensum->pevaluacions_count > 0) {
            $this->notification()->error(
                title: 'No se puede eliminar',
                description: "El pensum tiene {$pensum->pevaluacions_count} carga(s) académica(s) asociada(s). Elimínelas primero."
            );
            $this->cancelDelete();
            return;
        }

        $pensum->delete();
        $this->cancelDelete();

        $this->notification()->success(
            title: 'Pensum Eliminado',
            description: 'El pensum se eliminó correctamente.'
        );
    }

    // ─── PREVIEW ────────────────────────────────────────────────

    public function showPreview($id)
    {
        $this->previewPensum = Pensum::with(['pestudio', 'grado', 'asignatura', 'diagCompetencies.referent'])
            ->withCount(['pevaluacions', 'diagCompetencies'])
            ->findOrFail($id);
        $this->previewMode = true;
    }

    public function closePreview()
    {
        $this->previewMode = false;
        $this->previewPensum = null;
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    public function close()
    {
        $this->modeIndex = false;
        $this->modeForm = false;
        $this->previewMode = false;
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
