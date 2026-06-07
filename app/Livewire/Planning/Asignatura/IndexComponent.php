<?php

namespace App\Livewire\Planning\Asignatura;

use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Escala;
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
    public $asignatura_id;

    // Form fields
    public $pestudio_id, $code, $code_sm, $name, $tescala = 'NUMÉRICA', $order = 1;
    public $hour_t_week, $hour_p_week, $unid_credit, $approved_credit_unir;
    public $enable_academic_index = 'true', $enable_lost_regulation = 'true';
    public $enable_official_doc = 'true', $enable_repairable = 'true', $enable_grupo_estable = 'false';
    public $observations, $prelacions;

    // Select lists
    public $pestudios, $escalas;
    public $tescala_options = ['NUMÉRICA', 'CUALITATIVA', 'NUMÉRICA Y CUALITATIVA'];

    // Search & filters
    public $search = '';
    public $filter_pestudio = '';

    // Confirm delete
    public $confirmDeleteId = null;

    // Preview
    public $previewMode = false;
    public $previewAsignatura = null;

    protected $rules = [
        'pestudio_id' => 'required|integer|exists:pestudios,id',
        'code' => 'required|string|max:10',
        'code_sm' => 'required|string|max:4',
        'name' => 'required|string|max:255',
        'tescala' => 'nullable|in:NUMÉRICA,CUALITATIVA,NUMÉRICA Y CUALITATIVA',
        'order' => 'required|integer|min:1|max:12',
        'hour_t_week' => 'nullable|integer|min:0|max:10',
        'hour_p_week' => 'nullable|integer|min:0|max:10',
        'unid_credit' => 'nullable|integer|min:0',
        'approved_credit_unir' => 'nullable|integer|min:0',
        'enable_academic_index' => 'required|in:true,false',
        'enable_lost_regulation' => 'required|in:true,false',
        'enable_official_doc' => 'required|in:true,false',
        'enable_repairable' => 'required|in:true,false',
        'enable_grupo_estable' => 'required|in:true,false',
        'observations' => 'nullable|string|max:255',
        'prelacions' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->pestudios = Pestudio::where('status_active', 'true')
            ->orderBy('name')
            ->get()
            ->pluck('full_name', 'id');
        $this->escalas = Escala::orderBy('name')->pluck('name', 'id');

        $this->close();
    }

    public function render()
    {
        $query = Asignatura::with(['pestudio'])->withCount('pensums');

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

        $asignaturas = $query->orderBy('order')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.planning.asignatura.index-component', [
            'asignaturas' => $asignaturas,
        ]);
    }

    // ─── FORM ────────────────────────────────────────────────────

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->asignatura_id = null;
        $this->close();
        $this->modeForm = true;
    }

    public function edit($id)
    {
        $asignatura = Asignatura::findOrFail($id);
        $this->asignatura_id = $asignatura->id;
        $this->pestudio_id = $asignatura->pestudio_id;
        $this->code = $asignatura->code;
        $this->code_sm = $asignatura->code_sm;
        $this->name = $asignatura->name;
        $this->tescala = $asignatura->tescala;
        $this->order = $asignatura->order;
        $this->hour_t_week = $asignatura->hour_t_week;
        $this->hour_p_week = $asignatura->hour_p_week;
        $this->unid_credit = $asignatura->unid_credit;
        $this->approved_credit_unir = $asignatura->approved_credit_unir;
        $this->enable_academic_index = $asignatura->enable_academic_index;
        $this->enable_lost_regulation = $asignatura->enable_lost_regulation;
        $this->enable_official_doc = $asignatura->enable_official_doc;
        $this->enable_repairable = $asignatura->enable_repairable;
        $this->enable_grupo_estable = $asignatura->enable_grupo_estable;
        $this->observations = $asignatura->observations;
        $this->prelacions = $asignatura->prelacions;
        $this->isEditing = true;
        $this->close();
        $this->modeForm = true;
    }

    public function save()
    {
        // Normalizar campos ENUM('true','false')
        $enumFields = [
            'enable_academic_index', 'enable_lost_regulation',
            'enable_official_doc', 'enable_repairable', 'enable_grupo_estable',
        ];
        foreach ($enumFields as $field) {
            $val = $this->$field;
            $this->$field = ($val === true || $val === 'true' || $val === 1 || $val === '1') ? 'true' : 'false';
        }

        $this->validate();

        $data = [
            'pestudio_id' => $this->pestudio_id,
            'code' => $this->code,
            'code_sm' => $this->code_sm,
            'name' => $this->name,
            'tescala' => $this->tescala ?: null,
            'order' => $this->order,
            'hour_t_week' => $this->hour_t_week ?: null,
            'hour_p_week' => $this->hour_p_week ?: null,
            'unid_credit' => $this->unid_credit ?: null,
            'approved_credit_unir' => $this->approved_credit_unir ?: null,
            'enable_academic_index' => $this->enable_academic_index,
            'enable_lost_regulation' => $this->enable_lost_regulation,
            'enable_official_doc' => $this->enable_official_doc,
            'enable_repairable' => $this->enable_repairable,
            'enable_grupo_estable' => $this->enable_grupo_estable,
            'observations' => $this->observations,
            'prelacions' => $this->prelacions,
        ];

        if ($this->isEditing) {
            $asignatura = Asignatura::findOrFail($this->asignatura_id);
            $asignatura->update($data);
            $this->notification()->success(
                title: 'Asignatura Actualizada',
                description: 'La asignatura se actualizó correctamente.'
            );
        } else {
            Asignatura::create($data);
            $this->notification()->success(
                title: 'Asignatura Creada',
                description: 'La asignatura se creó correctamente.'
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
        $asignatura = Asignatura::withCount('pensums')->findOrFail($this->confirmDeleteId);

        if ($asignatura->pensums_count > 0) {
            $this->notification()->error(
                title: 'No se puede eliminar',
                description: "La asignatura tiene {$asignatura->pensums_count} pensum(s) asociado(s). Elimínelos primero."
            );
            $this->cancelDelete();
            return;
        }

        $asignatura->delete();
        $this->cancelDelete();

        $this->notification()->success(
            title: 'Asignatura Eliminada',
            description: 'La asignatura se eliminó correctamente.'
        );
    }

    // ─── PREVIEW ────────────────────────────────────────────────

    public function showPreview($id)
    {
        $this->previewAsignatura = Asignatura::with(['pestudio'])
            ->withCount('pensums')
            ->findOrFail($id);
        $this->previewMode = true;
    }

    public function closePreview()
    {
        $this->previewMode = false;
        $this->previewAsignatura = null;
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    public function resetForm()
    {
        $this->reset([
            'pestudio_id', 'code', 'code_sm', 'name', 'tescala',
            'hour_t_week', 'hour_p_week', 'unid_credit', 'approved_credit_unir',
            'observations', 'prelacions',
        ]);
        $this->order = 1;
        $this->enable_academic_index = 'true';
        $this->enable_lost_regulation = 'true';
        $this->enable_official_doc = 'true';
        $this->enable_repairable = 'true';
        $this->enable_grupo_estable = 'false';
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
