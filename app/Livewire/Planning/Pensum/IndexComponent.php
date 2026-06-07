<?php

namespace App\Livewire\Planning\Pensum;

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

    // Editing flag
    public $isEditing = false;
    public $pensum_id;

    // Form fields
    public $pestudio_id, $grado_id, $asignatura_id;
    public $status_component = 'false';
    public $status_active = true;
    public $status_active_diagnostic = false;
    public $observations;

    // Select lists (cascading)
    public $pestudios;
    public $grados = [];
    public $asignaturas = [];

    // Search & filters
    public $search = '';
    public $filter_pestudio = '';

    // Confirm delete
    public $confirmDeleteId = null;

    // Preview
    public $previewMode = false;
    public $previewPensum = null;

    protected $rules = [
        'pestudio_id' => 'required|integer|exists:pestudios,id',
        'grado_id' => 'required|integer|exists:grados,id',
        'asignatura_id' => 'required|integer|exists:asignaturas,id',
        'status_component' => 'required|in:true,false',
        'status_active' => 'required|boolean',
        'status_active_diagnostic' => 'required|boolean',
        'observations' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->pestudios = Pestudio::where('status_active', 'true')
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id');

        $this->close();
    }

    public function render()
    {
        $query = Pensum::with(['pestudio', 'grado', 'asignatura'])
            ->withCount('pevaluacions');

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

        $pensums = $query->orderBy('pestudio_id')
            ->orderBy('grado_id')
            ->orderBy('asignatura_id')
            ->paginate(15);

        return view('livewire.planning.pensum.index-component', [
            'pensums' => $pensums,
        ]);
    }

    // ─── CASCADING SELECTS ──────────────────────────────────────

    public function updatedPestudioId($value)
    {
        $this->grado_id = null;
        $this->asignatura_id = null;

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
        $this->resetForm();
        $this->isEditing = false;
        $this->pensum_id = null;
        $this->grados = [];
        $this->asignaturas = [];
        $this->close();
        $this->modeForm = true;
    }

    public function edit($id)
    {
        $pensum = Pensum::findOrFail($id);
        $this->pensum_id = $pensum->id;
        $this->pestudio_id = $pensum->pestudio_id;
        $this->grado_id = $pensum->grado_id;
        $this->asignatura_id = $pensum->asignatura_id;
        $this->status_component = $pensum->status_component;
        $this->status_active = $pensum->status_active;
        $this->status_active_diagnostic = $pensum->status_active_diagnostic;
        $this->observations = $pensum->observations;

        // Cargar listas para el pestudio seleccionado
        $this->grados = Grado::where('pestudio_id', $this->pestudio_id)
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        $this->asignaturas = Asignatura::where('pestudio_id', $this->pestudio_id)
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        $this->isEditing = true;
        $this->close();
        $this->modeForm = true;
    }

    public function save()
    {
        // Normalizar campos
        $val = $this->status_component;
        $this->status_component = ($val === true || $val === 'true' || $val === 1 || $val === '1') ? 'true' : 'false';
        $this->status_active = filter_var($this->status_active, FILTER_VALIDATE_BOOLEAN);
        $this->status_active_diagnostic = filter_var($this->status_active_diagnostic, FILTER_VALIDATE_BOOLEAN);

        $this->validate();

        // Validar unicidad compuesta (pestudio_id + grado_id + asignatura_id)
        $exists = Pensum::where('pestudio_id', $this->pestudio_id)
            ->where('grado_id', $this->grado_id)
            ->where('asignatura_id', $this->asignatura_id);

        if ($this->isEditing) {
            $exists->where('id', '!=', $this->pensum_id);
        }

        if ($exists->exists()) {
            $this->notification()->error(
                title: 'Pensum Duplicado',
                description: 'Ya existe un pensum con ese plan de estudio, grado y asignatura.'
            );
            return;
        }

        $data = [
            'pestudio_id' => $this->pestudio_id,
            'grado_id' => $this->grado_id,
            'asignatura_id' => $this->asignatura_id,
            'status_component' => $this->status_component,
            'status_active' => $this->status_active,
            'status_active_diagnostic' => $this->status_active_diagnostic,
            'observations' => $this->observations ?: null,
        ];

        if ($this->isEditing) {
            $pensum = Pensum::findOrFail($this->pensum_id);
            $pensum->update($data);
            $this->notification()->success(
                title: 'Pensum Actualizado',
                description: 'El pensum se actualizó correctamente.'
            );
        } else {
            Pensum::create($data);
            $this->notification()->success(
                title: 'Pensum Creado',
                description: 'El pensum se creó correctamente.'
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
        $this->previewPensum = Pensum::with(['pestudio', 'grado', 'asignatura'])
            ->withCount('pevaluacions')
            ->findOrFail($id);
        $this->previewMode = true;
    }

    public function closePreview()
    {
        $this->previewMode = false;
        $this->previewPensum = null;
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    public function resetForm()
    {
        $this->reset([
            'pestudio_id', 'grado_id', 'asignatura_id', 'observations',
        ]);
        $this->status_component = 'false';
        $this->status_active = true;
        $this->status_active_diagnostic = false;
    }

    public function close()
    {
        $this->modeIndex = false;
        $this->modeForm = false;
        $this->previewMode = false;
    }

    #[Layout('layouts.dashboard')]
    public function layout() {}
}
