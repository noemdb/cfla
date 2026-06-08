<?php

namespace App\Livewire\Planning\Seccion;

use App\Models\app\Academy\Seccion;
use App\Models\app\Academy\Grado;
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
    public $seccion_id;

    // Form fields
    public $grado_id, $name, $description, $amount_student = 40;
    public $observation, $comment_final;
    public $status_active = 'true', $status_inscription_affects = 'true';

    // Select lists
    public $grados;

    // Search & filters
    public $search = '';
    public $filter_grado = '';

    // Confirm delete
    public $confirmDeleteId = null;

    // Preview
    public $previewMode = false;
    public $previewSeccion = null;

    protected $rules = [
        'grado_id' => 'required|integer|exists:grados,id',
        'name' => 'required|string|max:1',
        'description' => 'nullable|string|max:255',
        'amount_student' => 'nullable|integer|min:1|max:50',
        'observation' => 'nullable|string|max:255',
        'comment_final' => 'nullable|string',
        'status_active' => 'required|in:true,false',
        'status_inscription_affects' => 'required|in:true,false',
    ];

    public function mount()
    {
        $this->grados = Grado::where('status_active', 'true')
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id');

        $this->close();
    }

    public function render()
    {
        $query = Seccion::with(['grado.pestudio'])
            ->withCount(['inscripcions', 'profesor_guias']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->filter_grado) {
            $query->where('grado_id', $this->filter_grado);
        }

        $seccions = $query->orderBy('grado_id')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.planning.seccion.index-component', [
            'seccions' => $seccions,
        ]);
    }

    // ─── FORM ────────────────────────────────────────────────────

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->seccion_id = null;
        $this->close();
        $this->modeForm = true;
    }

    public function edit($id)
    {
        $seccion = Seccion::findOrFail($id);
        $this->seccion_id = $seccion->id;
        $this->grado_id = $seccion->grado_id;
        $this->name = $seccion->name;
        $this->description = $seccion->description;
        $this->amount_student = $seccion->amount_student;
        $this->observation = $seccion->observation;
        $this->comment_final = $seccion->comment_final;
        $this->status_active = $seccion->status_active;
        $this->status_inscription_affects = $seccion->status_inscription_affects;
        $this->isEditing = true;
        $this->close();
        $this->modeForm = true;
    }

    public function save()
    {
        // Normalizar campos ENUM('true','false')
        $enumFields = ['status_active', 'status_inscription_affects'];
        foreach ($enumFields as $field) {
            $val = $this->$field;
            $this->$field = ($val === true || $val === 'true' || $val === 1 || $val === '1') ? 'true' : 'false';
        }

        $this->validate();

        $data = [
            'grado_id' => $this->grado_id,
            'name' => strtoupper($this->name),
            'description' => $this->description ?: null,
            'amount_student' => $this->amount_student ?: 40,
            'observation' => $this->observation ?: null,
            'comment_final' => $this->comment_final ?: null,
            'status_active' => $this->status_active,
            'status_inscription_affects' => $this->status_inscription_affects,
        ];

        if ($this->isEditing) {
            $seccion = Seccion::findOrFail($this->seccion_id);
            $seccion->update($data);
            $this->notification()->success(
                title: 'Sección Actualizada',
                description: 'La sección se actualizó correctamente.'
            );
        } else {
            Seccion::create($data);
            $this->notification()->success(
                title: 'Sección Creada',
                description: 'La sección se creó correctamente.'
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
        $seccion = Seccion::withCount(['inscripcions', 'profesor_guias'])
            ->findOrFail($this->confirmDeleteId);

        if ($seccion->inscripcions_count > 0 || $seccion->profesor_guias_count > 0) {
            $errors = [];
            if ($seccion->inscripcions_count > 0) {
                $errors[] = "{$seccion->inscripcions_count} inscripcione(s)";
            }
            if ($seccion->profesor_guias_count > 0) {
                $errors[] = "{$seccion->profesor_guias_count} profesor(es) guía";
            }
            $this->notification()->error(
                title: 'No se puede eliminar',
                description: 'La sección tiene ' . implode(' y ', $errors) . ' asociados. Elimínelos primero.'
            );
            $this->cancelDelete();
            return;
        }

        $seccion->delete();
        $this->cancelDelete();

        $this->notification()->success(
            title: 'Sección Eliminada',
            description: 'La sección se eliminó correctamente.'
        );
    }

    // ─── PREVIEW ────────────────────────────────────────────────

    public function showPreview($id)
    {
        $this->previewSeccion = Seccion::with(['grado.pestudio'])
            ->withCount(['inscripcions', 'profesor_guias'])
            ->findOrFail($id);
        $this->previewMode = true;
    }

    public function closePreview()
    {
        $this->previewMode = false;
        $this->previewSeccion = null;
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    public function resetForm()
    {
        $this->reset([
            'grado_id', 'name', 'description', 'observation', 'comment_final',
        ]);
        $this->amount_student = 40;
        $this->status_active = 'true';
        $this->status_inscription_affects = 'true';
    }

    public function close()
    {
        $this->modeIndex = false;
        $this->modeForm = false;
        $this->previewMode = false;
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
