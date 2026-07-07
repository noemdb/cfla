<?php

namespace App\Livewire\Planning\AreaConocimiento;

use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\CampoConocimiento;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Peducativo;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WithPagination, WireUiActions;

    // Modal modes
    public $modeForm = false;
    public $modeCampo = false;      // 95% dialog for campo_conocimientos

    // Editing flag
    public $isEditing = false;
    public $area_id;

    // ─── Form AreaConocimiento ────────────────────────────────────
    public $peducativo_id, $pestudio_id, $leader_id;
    public $name, $code, $code_sm, $description, $observations;
    public $order = 1;
    public $enable_academic_index = 'true';

    // ─── CampoConocimiento ────────────────────────────────────────
    public $campoAreaId = null;          // area_conocimiento_id for current campo management
    public $campoAreaName = '';          // display name in dialog header
    public $campo_asignatura_id;
    public $campo_observations;
    public $campoEditingId = null;       // editing a specific campo

    // Select lists
    public $pestudios = [];
    public $peducativos = [];
    public $usuarios = [];
    public $asignaturasList = [];        // for campo asignatura selection

    // Search & filters
    public $search = '';
    public $filter_pestudio = '';
    public $filter_peducativo = '';
    public $paginate = 20;

    // Confirm delete
    public $confirmDeleteId = null;
    public $confirmDeleteCampoId = null;

    protected $rules = [
        'pestudio_id'   => 'required|integer|exists:pestudios,id',
        'name'           => 'required|string|max:255',
        'code'           => 'required|string|max:20',
        'code_sm'        => 'required|string|max:10',
        'description'    => 'nullable|string|max:500',
        'observations'   => 'nullable|string|max:500',
        'order'          => 'required|integer|min:1|max:50',
        'enable_academic_index' => 'required|in:true,false',
        'peducativo_id'  => 'nullable|integer|exists:peducativos,id',
        'leader_id'      => 'nullable|integer|exists:users,id',
    ];

    protected $rulesCampo = [
        'campo_asignatura_id' => 'required|integer|exists:asignaturas,id',
        'campo_observations'   => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $this->pestudios = Pestudio::where('status_active', 'true')
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();

        $this->peducativos = Peducativo::where('status_active', 'true')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->usuarios = User::orderBy('username')
            ->get()
            ->pluck('username', 'id')
            ->toArray();

        $this->asignaturasList = Asignatura::orderBy('code')
            ->get()
            ->pluck('full_name', 'id')
            ->toArray();
    }

    public function render()
    {
        $query = AreaConocimiento::with(['pestudio', 'peducativo', 'leader'])
            ->withCount('campo_conocimientos');

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

        if ($this->filter_peducativo) {
            $query->where('peducativo_id', $this->filter_peducativo);
        }

        $area_conocimientos = $query->orderBy('order')
            ->orderBy('name')
            ->paginate($this->paginate);

        return view('livewire.planning.area-conocimiento.index-component', [
            'area_conocimientos' => $area_conocimientos,
        ]);
    }

    // ─── PAGINATION & FILTERS ────────────────────────────────────

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterPestudio() { $this->resetPage(); }
    public function updatingFilterPeducativo() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }

    // ─── CRUD AreaConocimiento ───────────────────────────────────

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->area_id = null;
        $this->modeForm = true;
    }

    public function edit($id)
    {
        $area = AreaConocimiento::findOrFail($id);
        $this->area_id = $area->id;
        $this->peducativo_id = $area->peducativo_id;
        $this->pestudio_id = $area->pestudio_id;
        $this->leader_id = $area->leader_id;
        $this->name = $area->name;
        $this->code = $area->code;
        $this->code_sm = $area->code_sm;
        $this->description = $area->description;
        $this->observations = $area->observations;
        $this->order = $area->order;
        $this->enable_academic_index = $area->enable_academic_index;
        $this->isEditing = true;
        $this->modeForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'peducativo_id' => $this->peducativo_id ?: null,
            'pestudio_id'   => $this->pestudio_id,
            'leader_id'     => $this->leader_id ?: null,
            'name'          => $this->name,
            'code'          => $this->code,
            'code_sm'       => $this->code_sm,
            'description'   => $this->description,
            'observations'  => $this->observations,
            'order'         => $this->order,
            'enable_academic_index' => $this->enable_academic_index ?: 'false',
        ];

        if ($this->isEditing) {
            $area = AreaConocimiento::findOrFail($this->area_id);
            $area->update($data);
            $this->notification()->success(
                title: 'Área Actualizada',
                description: 'El área de conocimiento se actualizó correctamente.'
            );
        } else {
            AreaConocimiento::create($data);
            $this->notification()->success(
                title: 'Área Creada',
                description: 'El área de conocimiento se creó correctamente.'
            );
        }

        $this->modeForm = false;
        $this->resetForm();
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
        $area = AreaConocimiento::withCount('campo_conocimientos')->findOrFail($this->confirmDeleteId);

        if ($area->campo_conocimientos_count > 0) {
            $this->notification()->error(
                title: 'No se puede eliminar',
                description: "El área tiene {$area->campo_conocimientos_count} asignatura(s) adscrita(s). Elimínelas primero."
            );
            $this->cancelDelete();
            return;
        }

        $area->delete();
        $this->cancelDelete();
        $this->notification()->success(
            title: 'Área Eliminada',
            description: 'El área de conocimiento se eliminó correctamente.'
        );
    }

    // ─── CAMPO CONOCIMIENTO (modal 95%) ──────────────────────────

    public function openCampoManager($areaId)
    {
        $area = AreaConocimiento::withCount('campo_conocimientos')->findOrFail($areaId);
        $this->campoAreaId = $area->id;
        $this->campoAreaName = $area->name;
        $this->resetCampoForm();
        $this->modeCampo = true;
    }

    public function closeCampoManager()
    {
        $this->modeCampo = false;
        $this->campoAreaId = null;
        $this->campoAreaName = '';
        $this->resetCampoForm();
    }

    public function resetCampoForm()
    {
        $this->campo_asignatura_id = null;
        $this->campo_observations = null;
        $this->campoEditingId = null;
    }

    public function editCampo($id)
    {
        $campo = CampoConocimiento::findOrFail($id);
        $this->campoEditingId = $campo->id;
        $this->campo_asignatura_id = $campo->asignatura_id;
        $this->campo_observations = $campo->observations;
    }

    public function saveCampo()
    {
        $this->validate($this->rulesCampo);

        $data = [
            'area_conocimiento_id' => $this->campoAreaId,
            'asignatura_id'        => $this->campo_asignatura_id,
            'observations'         => $this->campo_observations,
        ];

        if ($this->campoEditingId) {
            $campo = CampoConocimiento::findOrFail($this->campoEditingId);
            $campo->update($data);
            $this->notification()->success(
                title: 'Adscripción Actualizada',
                description: 'La asignatura se actualizó en el área de conocimiento.'
            );
        } else {
            CampoConocimiento::create($data);
            $this->notification()->success(
                title: 'Asignatura Adscrita',
                description: 'La asignatura se adscribió al área de conocimiento.'
            );
        }

        $this->resetCampoForm();
    }

    public function confirmDeleteCampo($id)
    {
        $this->confirmDeleteCampoId = $id;
    }

    public function cancelDeleteCampo()
    {
        $this->confirmDeleteCampoId = null;
    }

    public function destroyCampo()
    {
        $campo = CampoConocimiento::findOrFail($this->confirmDeleteCampoId);
        $campo->delete();
        $this->cancelDeleteCampo();
        $this->notification()->success(
            title: 'Adscripción Eliminada',
            description: 'La asignatura se desadscribió del área de conocimiento.'
        );
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    public function resetForm()
    {
        $this->reset([
            'peducativo_id', 'pestudio_id', 'leader_id',
            'name', 'code', 'code_sm', 'description', 'observations',
        ]);
        $this->order = 1;
        $this->enable_academic_index = 'true';
    }

    public function getCampoConocimientosProperty()
    {
        if (! $this->campoAreaId) return collect();
        return CampoConocimiento::with('asignatura')
            ->where('area_conocimiento_id', $this->campoAreaId)
            ->orderBy('id', 'desc')
            ->get();
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
