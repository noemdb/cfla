<?php

namespace App\Livewire\Planning\Grado;

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
    public $grado_id;

    // Form fields
    public $pestudio_id, $name, $code, $code_sm, $description;
    public $status_active = 'true';
    public $hour_social = 60, $total_hour_social = 60, $order = 0;

    // Select lists
    public $pestudios;

    // Search & filters
    public $search = '';
    public $filter_pestudio = '';

    // Confirm delete
    public $confirmDeleteId = null;

    // Preview
    public $previewMode = false;
    public $previewGrado = null;

    protected $rules = [
        'pestudio_id' => 'required|integer|exists:pestudios,id',
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:10',
        'code_sm' => 'required|string|max:4',
        'description' => 'nullable|string|max:255',
        'status_active' => 'required|in:true,false',
        'hour_social' => 'nullable|integer|min:0|max:255',
        'total_hour_social' => 'nullable|integer|min:0|max:255',
        'order' => 'nullable|integer|min:0',
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
        $query = Grado::with(['pestudio'])->withCount('seccions');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhere('code_sm', 'like', "%{$this->search}%");
            });
        }

        if ($this->filter_pestudio) {
            $query->where('pestudio_id', $this->filter_pestudio);
        }

        $grados = $query->orderBy('order')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.planning.grado.index-component', [
            'grados' => $grados,
        ]);
    }

    // ─── FORM ────────────────────────────────────────────────────

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->grado_id = null;
        $this->close();
        $this->modeForm = true;
    }

    public function edit($id)
    {
        $grado = Grado::findOrFail($id);
        $this->grado_id = $grado->id;
        $this->pestudio_id = $grado->pestudio_id;
        $this->name = $grado->name;
        $this->code = $grado->code;
        $this->code_sm = $grado->code_sm;
        $this->description = $grado->description;
        $this->status_active = $grado->status_active;
        $this->hour_social = $grado->hour_social;
        $this->total_hour_social = $grado->total_hour_social;
        $this->order = $grado->order;
        $this->isEditing = true;
        $this->close();
        $this->modeForm = true;
    }

    public function save()
    {
        // Normalizar campo ENUM('true','false')
        $val = $this->status_active;
        $this->status_active = ($val === true || $val === 'true' || $val === 1 || $val === '1') ? 'true' : 'false';

        $this->validate();

        $data = [
            'pestudio_id' => $this->pestudio_id,
            'name' => $this->name,
            'code' => $this->code,
            'code_sm' => $this->code_sm,
            'description' => $this->description ?: null,
            'status_active' => $this->status_active,
            'hour_social' => $this->hour_social ?: null,
            'total_hour_social' => $this->total_hour_social ?: null,
            'order' => $this->order !== '' ? $this->order : 0,
        ];

        if ($this->isEditing) {
            $grado = Grado::findOrFail($this->grado_id);
            $grado->update($data);
            $this->notification()->success(
                title: 'Grado Actualizado',
                description: 'El grado se actualizó correctamente.'
            );
        } else {
            Grado::create($data);
            $this->notification()->success(
                title: 'Grado Creado',
                description: 'El grado se creó correctamente.'
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
        $grado = Grado::withCount('seccions')->findOrFail($this->confirmDeleteId);

        if ($grado->seccions_count > 0) {
            $this->notification()->error(
                title: 'No se puede eliminar',
                description: "El grado tiene {$grado->seccions_count} seccion(es) asociada(s). Elimínelas primero."
            );
            $this->cancelDelete();
            return;
        }

        $grado->delete();
        $this->cancelDelete();

        $this->notification()->success(
            title: 'Grado Eliminado',
            description: 'El grado se eliminó correctamente.'
        );
    }

    // ─── PREVIEW ────────────────────────────────────────────────

    public function showPreview($id)
    {
        $this->previewGrado = Grado::with(['pestudio'])
            ->withCount('seccions')
            ->findOrFail($id);
        $this->previewMode = true;
    }

    public function closePreview()
    {
        $this->previewMode = false;
        $this->previewGrado = null;
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    public function resetForm()
    {
        $this->reset([
            'pestudio_id', 'name', 'code', 'code_sm', 'description',
        ]);
        $this->status_active = 'true';
        $this->hour_social = 60;
        $this->total_hour_social = 60;
        $this->order = 0;
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
