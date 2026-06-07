<?php

namespace App\Livewire\Planning\Pestudio;

use App\Models\app\Academy\Escala;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pestudio;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
    public $pestudio_id;

    // Form fields
    public $peducativo_id, $code, $code_oficial, $name, $order = 1;
    public $description, $description_aux, $mention, $title, $profile;
    public $manager_id, $scale, $color = 'info';
    // ENUM('true','false') fields
    public $status_active = 'true', $show_hr = 'false', $status_a_cualitative = 'false';
    public $status_baremo = 'false', $status_carga_notas = 'false';
    public $status_build_promotion = 'false';
    public $show_quantitative_indicators = 'true';
    // TINYINT(1) fields
    public $status_inscripcion_active = '1', $status_socials = '1', $planning_module = '1';
    public $paper = 'letter', $activities_avr;
    public $remision_resumen_final, $fecha_informe_final, $fecha_certificacion;
    public $fecha_descriptivo, $fecha_promocion, $fecha_prosecucion;
    public $description_trainig_component;

    // Select lists
    public $peducativos, $escalas, $users;
    public $color_options = ['info', 'primary', 'success'];

    // Search & filters
    public $search = '';
    public $filter_status = '';
    public $filter_peducativo = '';

    // Confirm delete
    public $confirmDeleteId = null;

    // Preview
    public $previewMode = false;
    public $previewPestudio = null;

    protected $rules = [
        'peducativo_id' => 'required|integer|exists:peducativos,id',
        'code' => 'required|string|max:255',
        'code_oficial' => 'nullable|string|max:255',
        'name' => 'required|string|max:255',
        'order' => 'required|integer|min:1|max:10',
        'description' => 'required|string|max:255',
        'description_aux' => 'nullable|string|max:255',
        'mention' => 'nullable|string|max:255',
        'status_build_promotion' => 'required|in:true,false',
        'title' => 'nullable|string|max:255',
        'scale' => 'required|integer|exists:escalas,id',
        'profile' => 'nullable|string',
        'color' => 'required|in:info,primary,success',
        'show_hr' => 'required|in:true,false',
        'status_a_cualitative' => 'required|in:true,false',
        'status_baremo' => 'required|in:true,false',
        'status_active' => 'required|in:true,false',
        'status_carga_notas' => 'required|in:true,false',
        'status_inscripcion_active' => 'required|boolean',
        'show_quantitative_indicators' => 'required|in:true,false',
        'status_socials' => 'required|boolean',
        'remision_resumen_final' => 'nullable|date',
        'fecha_informe_final' => 'nullable|date',
        'fecha_certificacion' => 'nullable|date',
        'fecha_descriptivo' => 'nullable|date',
        'fecha_promocion' => 'nullable|date',
        'fecha_prosecucion' => 'nullable|date',
        'description_trainig_component' => 'nullable|string|max:255',
        'manager_id' => 'nullable|integer|exists:users,id',
        'planning_module' => 'required|boolean',
        'activities_avr' => 'nullable|integer|min:0',
        'paper' => 'required|in:oficial,letter',
    ];

    public function mount()
    {
        $this->peducativos = Peducativo::where('status_active', 'true')
            ->orderBy('name')
            ->pluck('name', 'id');
        $this->escalas = Escala::orderBy('name')->pluck('name', 'id');
        $this->users = User::where('is_active', true)
            ->orderBy('username')
            ->pluck('username', 'id');

        $this->close();
    }

    public function render()
    {
        $query = Pestudio::with(['peducativo', 'manager', 'escala'])
            ->withCount('grados');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhere('code_oficial', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->filter_status !== '') {
            $query->where('status_active', $this->filter_status === 'active' ? 'true' : 'false');
        }

        if ($this->filter_peducativo) {
            $query->where('peducativo_id', $this->filter_peducativo);
        }

        $pestudios = $query->orderBy('order')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.planning.pestudio.index-component', [
            'pestudios' => $pestudios,
        ]);
    }

    // ─── FORM ────────────────────────────────────────────────────

    public function create()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->pestudio_id = null;
        $this->close();
        $this->modeForm = true;
    }

    public function edit($id)
    {
        $pestudio = Pestudio::findOrFail($id);
        $this->pestudio_id = $pestudio->id;
        $this->peducativo_id = $pestudio->peducativo_id;
        $this->code = $pestudio->code;
        $this->code_oficial = $pestudio->code_oficial;
        $this->name = $pestudio->name;
        $this->order = $pestudio->order;
        $this->description = $pestudio->description;
        $this->description_aux = $pestudio->description_aux;
        $this->mention = $pestudio->mention;
        $this->title = $pestudio->title;
        $this->profile = $pestudio->profile;
        $this->manager_id = $pestudio->manager_id;
        $this->scale = $pestudio->scale;
        $this->color = $pestudio->color ?: 'info';
        $this->status_active = $pestudio->status_active;
        $this->show_hr = $pestudio->show_hr;
        $this->status_a_cualitative = $pestudio->status_a_cualitative;
        $this->status_baremo = $pestudio->status_baremo;
        $this->status_carga_notas = $pestudio->status_carga_notas;
        $this->status_build_promotion = $pestudio->status_build_promotion;
        $this->status_inscripcion_active = $pestudio->status_inscripcion_active;
        $this->show_quantitative_indicators = $pestudio->show_quantitative_indicators;
        $this->status_socials = $pestudio->status_socials;
        $this->planning_module = $pestudio->planning_module;
        $this->paper = $pestudio->paper;
        $this->activities_avr = $pestudio->activities_avr;
        $this->remision_resumen_final = $pestudio->remision_resumen_final;
        $this->fecha_informe_final = $pestudio->fecha_informe_final;
        $this->fecha_certificacion = $pestudio->fecha_certificacion;
        $this->fecha_descriptivo = $pestudio->fecha_descriptivo;
        $this->fecha_promocion = $pestudio->fecha_promocion;
        $this->fecha_prosecucion = $pestudio->fecha_prosecucion;
        $this->description_trainig_component = $pestudio->description_trainig_component;
        $this->isEditing = true;
        $this->close();
        $this->modeForm = true;
    }

    public function save()
    {
        // Normalizar campos: ENUM('true','false') → string, TINYINT(1) → integer
        $enumFields = ['status_active', 'show_hr', 'status_a_cualitative', 'status_baremo',
                       'status_carga_notas', 'status_build_promotion', 'show_quantitative_indicators'];
        $tinyintFields = ['status_inscripcion_active', 'status_socials', 'planning_module'];

        foreach ($enumFields as $field) {
            $val = $this->$field;
            $this->$field = ($val === true || $val === 'true' || $val === 1 || $val === '1') ? 'true' : 'false';
        }
        foreach ($tinyintFields as $field) {
            $val = $this->$field;
            $this->$field = ($val === true || $val === 'true' || $val === 1 || $val === '1') ? 1 : 0;
        }

        $this->validate();

        $data = [
            'peducativo_id' => $this->peducativo_id,
            'code' => $this->code,
            'code_oficial' => $this->code_oficial,
            'name' => $this->name,
            'order' => $this->order,
            'description' => $this->description,
            'description_aux' => $this->description_aux,
            'mention' => $this->mention,
            'title' => $this->title,
            'profile' => $this->profile,
            'manager_id' => $this->manager_id,
            'scale' => $this->scale,
            'color' => $this->color,
            'status_active' => $this->status_active,
            'show_hr' => $this->show_hr,
            'status_a_cualitative' => $this->status_a_cualitative,
            'status_baremo' => $this->status_baremo,
            'status_carga_notas' => $this->status_carga_notas,
            'status_build_promotion' => $this->status_build_promotion,
            'status_inscripcion_active' => $this->status_inscripcion_active,
            'show_quantitative_indicators' => $this->show_quantitative_indicators,
            'status_socials' => $this->status_socials,
            'planning_module' => $this->planning_module,
            'paper' => $this->paper,
            'activities_avr' => $this->activities_avr,
            'remision_resumen_final' => $this->remision_resumen_final ?: null,
            'fecha_informe_final' => $this->fecha_informe_final ?: null,
            'fecha_certificacion' => $this->fecha_certificacion ?: null,
            'fecha_descriptivo' => $this->fecha_descriptivo ?: null,
            'fecha_promocion' => $this->fecha_promocion ?: null,
            'fecha_prosecucion' => $this->fecha_prosecucion ?: null,
            'description_trainig_component' => $this->description_trainig_component,
        ];

        if ($this->isEditing) {
            $pestudio = Pestudio::findOrFail($this->pestudio_id);
            $pestudio->update($data);
            $this->notification()->success(
                title: 'Plan Actualizado',
                description: 'El plan de estudio se actualizó correctamente.'
            );
        } else {
            Pestudio::create($data);
            $this->notification()->success(
                title: 'Plan Creado',
                description: 'El plan de estudio se creó correctamente.'
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
        $pestudio = Pestudio::withCount('grados')->findOrFail($this->confirmDeleteId);

        if ($pestudio->grados_count > 0) {
            $this->notification()->error(
                title: 'No se puede eliminar',
                description: "El plan de estudio tiene {$pestudio->grados_count} grado(s) asociado(s). Elimínelos primero."
            );
            $this->cancelDelete();
            return;
        }

        $pestudio->delete();
        $this->cancelDelete();

        $this->notification()->success(
            title: 'Plan Eliminado',
            description: 'El plan de estudio se eliminó correctamente.'
        );
    }

    // ─── PREVIEW ────────────────────────────────────────────────

    public function showPreview($id)
    {
        $this->previewPestudio = Pestudio::with(['peducativo', 'manager', 'escala'])
            ->withCount('grados')
            ->findOrFail($id);
        $this->previewMode = true;
    }

    public function closePreview()
    {
        $this->previewMode = false;
        $this->previewPestudio = null;
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    public function resetForm()
    {
        $this->reset([
            'peducativo_id', 'code', 'code_oficial', 'name', 'description',
            'description_aux', 'mention', 'title', 'profile', 'manager_id',
            'scale', 'activities_avr', 'remision_resumen_final',
            'fecha_informe_final', 'fecha_certificacion', 'fecha_descriptivo',
            'fecha_promocion', 'fecha_prosecucion', 'description_trainig_component',
        ]);
        $this->order = 1;
        $this->color = 'info';
        $this->paper = 'letter';
        $this->status_active = 'true';
        $this->show_hr = 'false';
        $this->status_a_cualitative = 'false';
        $this->status_baremo = 'false';
        $this->status_carga_notas = 'false';
        $this->status_build_promotion = 'false';
        $this->status_inscripcion_active = '1';
        $this->show_quantitative_indicators = 'true';
        $this->status_socials = '1';
        $this->planning_module = '1';
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
