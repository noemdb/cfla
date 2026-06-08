<?php

namespace App\Livewire\Planning\Peducativo;

use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pescolar;
use App\Models\User;
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
    public $peducativo_id;

    // Form fields
    public $pescolar_id, $manager_id, $deputy_id, $assistant_id;
    public $name, $description, $order = 1;
    public $status_active = 'true', $show_quantitative_indicators = 'true';

    // Select lists
    public $pescolars, $users;

    // Search & filters
    public $search = '';
    public $filter_status = '';

    // Confirm delete
    public $confirmDeleteId = null;

    // Preview
    public $previewMode = false;
    public $previewPeducativo = null;

    protected $rules = [
        'pescolar_id' => 'required|integer|exists:pescolars,id',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:255',
        'order' => 'required|integer|min:1|max:10',
        'manager_id' => 'nullable|integer|exists:users,id',
        'deputy_id' => 'nullable|integer|exists:users,id',
        'assistant_id' => 'nullable|integer|exists:users,id',
        'status_active' => 'required|in:true,false',
        'show_quantitative_indicators' => 'required|in:true,false',
    ];

    public function mount()
    {
        $this->pescolars = Pescolar::whereNull('deleted_at')
            ->orderBy('name')
            ->pluck('name', 'id');
        $this->users = User::where('is_active', true)
            ->orderBy('username')
            ->pluck('username', 'id');

        $this->close();
    }

    public function render()
    {
        $query = Peducativo::with(['pescolar', 'manager', 'deputy', 'assistant'])
            ->withCount('pestudios');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->filter_status !== '') {
            $query->where('status_active', $this->filter_status === 'active' ? 'true' : 'false');
        }

        $peducativos = $query->orderBy('order')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.planning.peducativo.index-component', [
            'peducativos' => $peducativos,
        ]);
    }

    // ─── FORM ────────────────────────────────────────────────────

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->peducativo_id = null;
        $this->close();
        $this->modeForm = true;
    }

    public function edit($id)
    {
        $peducativo = Peducativo::findOrFail($id);
        $this->peducativo_id = $peducativo->id;
        $this->pescolar_id = $peducativo->pescolar_id;
        $this->name = $peducativo->name;
        $this->description = $peducativo->description;
        $this->order = $peducativo->order;
        $this->manager_id = $peducativo->manager_id;
        $this->deputy_id = $peducativo->deputy_id;
        $this->assistant_id = $peducativo->assistant_id;
        $this->status_active = $peducativo->status_active;
        $this->show_quantitative_indicators = $peducativo->show_quantitative_indicators;
        $this->isEditing = true;
        $this->close();
        $this->modeForm = true;
    }

    public function save()
    {
        // Normalizar campos ENUM('true','false')
        $enumFields = ['status_active', 'show_quantitative_indicators'];
        foreach ($enumFields as $field) {
            $val = $this->$field;
            $this->$field = ($val === true || $val === 'true' || $val === 1 || $val === '1') ? 'true' : 'false';
        }

        $this->validate();

        $data = [
            'pescolar_id' => $this->pescolar_id,
            'name' => $this->name,
            'description' => $this->description,
            'order' => $this->order,
            'manager_id' => $this->manager_id ?: null,
            'deputy_id' => $this->deputy_id ?: null,
            'assistant_id' => $this->assistant_id ?: null,
            'status_active' => $this->status_active,
            'show_quantitative_indicators' => $this->show_quantitative_indicators,
        ];

        if ($this->isEditing) {
            $peducativo = Peducativo::findOrFail($this->peducativo_id);
            $peducativo->update($data);
            $this->notification()->success(
                title: 'Programa Actualizado',
                description: 'El programa educativo se actualizó correctamente.'
            );
        } else {
            Peducativo::create($data);
            $this->notification()->success(
                title: 'Programa Creado',
                description: 'El programa educativo se creó correctamente.'
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
        $peducativo = Peducativo::withCount('pestudios')->findOrFail($this->confirmDeleteId);

        if ($peducativo->pestudios_count > 0) {
            $this->notification()->error(
                title: 'No se puede eliminar',
                description: "El programa educativo tiene {$peducativo->pestudios_count} plan(es) de estudio asociado(s). Elimínelos primero."
            );
            $this->cancelDelete();
            return;
        }

        $peducativo->delete();
        $this->cancelDelete();

        $this->notification()->success(
            title: 'Programa Eliminado',
            description: 'El programa educativo se eliminó correctamente.'
        );
    }

    // ─── PREVIEW ────────────────────────────────────────────────

    public function showPreview($id)
    {
        $this->previewPeducativo = Peducativo::with(['pescolar', 'manager', 'deputy', 'assistant'])
            ->withCount('pestudios')
            ->findOrFail($id);
        $this->previewMode = true;
    }

    public function closePreview()
    {
        $this->previewMode = false;
        $this->previewPeducativo = null;
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    public function resetForm()
    {
        $this->reset([
            'pescolar_id', 'name', 'description', 'manager_id',
            'deputy_id', 'assistant_id',
        ]);
        $this->order = 1;
        $this->status_active = 'true';
        $this->show_quantitative_indicators = 'true';
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
