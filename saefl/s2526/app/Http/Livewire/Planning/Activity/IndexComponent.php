<?php

namespace App\Http\Livewire\Planning\Activity;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Leader;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Activity;
use App\Models\app\Profesor\Pevaluacion;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class IndexComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap-4';

    use updatedTrait;

    // public $pevaluacions;
    public $activity,$activity_id,$pevaluacion_id,$objetivo,$comments,$status,$pevaluacion,$observations,$pestudios,$peducativos,$lapso_current;
    public $modeCreator,$modeEdit,$modeIndex,$modeObservation,$modeEditObservations,$modeComments,$status_activities;
    public $modeCreate = false, $modeEditAct = false, $modeShowAct = false;
    public $user_id,$leader_id,$grado_id,$seccion_id,$lapso_id,$profesor_id;
    public $search,$paginate=10,$name;
    // Form fields for Activity CRUD
    public $act_finicial, $act_ffinal, $act_topic, $act_thematic, $act_references;
    public $act_teaching, $act_learning, $act_description, $act_observations;
    public $act_achievement, $act_weighting, $act_name, $act_status = false;
    public $act_selected_pevaluacion; // Pevaluacion model for context
    public $list_grado,$list_seccion;
    public $pestudio_id, $list_pestudio, $list_lapso,$list_comment,$list_pensum,$list_profesors;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingName() { $this->resetPage(); }
    public function updatingPestudioId() { $this->resetPage(); }
    public function updatingGradoId() { $this->resetPage(); }
    public function updatingSeccionId() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }

    public function mount()
    {
        $user = User::findOrFail(Auth::id());
        $this->user_id = $user->id;
        $this->leader_id = $user->id;

        $this->close();
        $this->modeIndex = true;
        $this->pestudios = Leader::getPestudioForLeader($this->leader_id); //dd($this->pestudios);
        $this->peducativos = Leader::getPeducativosForLeader($this->leader_id); //dd($this->peducativos);

        $this->list_grado = Leader::getGradosForLeader($this->leader_id)->pluck('name','id'); //dd($this->list_grado);
        $this->list_pensum = Leader::getPensumsForLeader($this->leader_id)->pluck('asignatura_fullname','id'); //dd($this->list_pensum);
        $this->list_seccion = collect();
        $this->list_pestudio = $this->pestudios->pluck('name','id'); //dd($this->list_pestudio);
        $this->setProfesorLists(); //dd($this->list_profesors);
        $this->list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $this->list_comment = Pevaluacion::COLUMN_COMMENTS;

        $this->paginate = 10; //dd($this->pestudios,$this->peducativos,$this->list_grado,$this->list_pestudio,$this->list_lapso,$this->list_comment,$this->list_profesors);

        //Carga del lapso altual
        $this->lapso_current = Lapso::current(); //dd($this->lapso_current);
        $this->lapso_id = $this->lapso_current->id ?? null; //dd($this->lapso_id);
    }

    public function render()
    {
        $leaderId= $this->leader_id;
        $filters = array_filter([
            'seccion_id' => $this->seccion_id ?? null,
            'grado_id' => $this->grado_id ?? null,
            'lapso_id' => $this->lapso_id ?? null,
            'pestudio_id' => $this->pestudio_id ?? null,
            'profesor_id' => $this->profesor_id ?? null,
            'status_activities' => $this->status_activities ?? null,
        ], function ($value) {
            return $value !== null && $value !== '';
        });

        $pevaluacions = Leader::getPevaluacionesForLeader($leaderId, $filters, true, $this->paginate);

        return view('livewire.planning.activity.index-component', [
            'pevaluacions' => $pevaluacions,
        ]);
    }

    public function setProfesorLists($value = null)
    {
        $profesors = Profesor::orderby('profesors.lastname','asc')
        ->select('profesors.id')
        ->selectRaw("CONCAT(profesors.lastname,' ',profesors.name) as profesor_fullname")
        ->orderby('profesor_fullname','asc')
        ->where('profesors.status_active','true');
        $profesors = ($value) ? $profesors->where('profesors.name','like','%'.$value.'%')->orWhere('profesors.lastname','like','%'.$value.'%') : $profesors ;
        $this->list_profesors = $profesors->pluck('profesor_fullname', 'id');
    }

    public function close()
    {
        $this->modeCreator = false;
        $this->modeEdit = false;
        $this->modeIndex = false;
        $this->modeObservation = false;
        $this->modeComments = false;
        $this->modeCreate = false;
        $this->modeEditAct = false;
        $this->modeShowAct = false;
    }

    public function createObservation($id)
    {
        $this->pevaluacion = Pevaluacion::findOrFail($id); //dd($this->pevaluacion);
        $this->pevaluacion_id = $id;
        $this->observations = ($this->pevaluacion) ? $this->pevaluacion->observations : null;
        $this->close();
        $this->modeObservation = true;
    }

    public function saveObservation()
    {
        $this->pevaluacion->observations = $this->observations;
        $this->pevaluacion->save();
        $this->pevaluacion = null;
        $this->pevaluacion_id = null;
        $this->close();
        $this->modeIndex = true;
    }

    // public function createComent($id)
    // {
    //     $this->activity = Activity::findOrFail($id);
    //     $this->comments = $this->activity->comments;
    //     $this->close();
    //     $this->modeCreator = true;
    // }

    public function setModeComment($activitie_id)
    {
        $this->activity = Activity::findOrFail($activitie_id);
        $this->activity_id = $this->activity->id; //dd($this->activity_id);
        $this->comments = $this->activity->comments ;
        $this->status = $this->activity->status ;
        $this->close();
        $this->modeComments = true;
    }

    public function saveComent()
    {
        $this->activity->comments = $this->comments;
        $this->activity->status = $this->status;
        $this->activity->save();
        $this->activity = null;
        $this->activity_id = null;
        $this->close();
        $this->modeIndex = true;
    }

    public function editObservation($id)
    {
        $this->pevaluacion = Pevaluacion::findOrFail($id); //dd($this->pevaluacion);
        $this->observations = $this->pevaluacion->observations;
        $this->close();
        $this->modeObservation = true;
    }

    // =========================================================
    // CRUD ACTIVIDADES
    // =========================================================

    public function rules()
    {
        return [
            'act_finicial' => 'required|date',
            'act_ffinal' => 'required|date|after_or_equal:act_finicial',
            'act_topic' => 'required|string|max:500',
            'act_thematic' => 'nullable|string|max:500',
            'act_references' => 'nullable|string|max:500',
            'act_teaching' => 'nullable|string|max:1000',
            'act_learning' => 'nullable|string|max:1000',
            'act_description' => 'nullable|string|max:1000',
            'act_observations' => 'nullable|string|max:500',
            'act_achievement' => 'nullable|string|max:500',
            'act_weighting' => 'nullable|numeric|min:0|max:100',
            'act_name' => 'nullable|string|max:255',
        ];
    }

    public function setCreate($pevaluacion_id)
    {
        $this->act_selected_pevaluacion = Pevaluacion::with('pensum.asignatura','pensum.grado','seccion','lapso')->findOrFail($pevaluacion_id);
        $this->pevaluacion_id = $pevaluacion_id;
        $this->resetActForm();
        $this->close();
        $this->modeCreate = true;
        $this->dispatchBrowserEvent('open-create-modal');
    }

    public function store()
    {
        $this->validate();

        Activity::create([
            'pevaluacion_id' => $this->pevaluacion_id,
            'finicial' => $this->act_finicial,
            'ffinal' => $this->act_ffinal,
            'topic' => $this->act_topic,
            'thematic' => $this->act_thematic,
            'references' => $this->act_references,
            'teaching' => $this->act_teaching,
            'learning' => $this->act_learning,
            'description' => $this->act_description,
            'observations' => $this->act_observations,
            'status' => $this->act_status ?? false,
        ]);

        $this->showSwal('¡Creada!', 'Actividad creada exitosamente.', 'success');
        $this->close();
        $this->modeIndex = true;
        $this->resetActForm();
        $this->dispatchBrowserEvent('close-create-modal');
    }

    public function setEditAct($id)
    {
        $activity = Activity::with('pevaluacion.pensum.asignatura','pevaluacion.pensum.grado','pevaluacion.seccion','pevaluacion.lapso')->findOrFail($id);
        $this->activity = $activity;
        $this->activity_id = $activity->id;
        $this->pevaluacion_id = $activity->pevaluacion_id;
        $this->act_selected_pevaluacion = null; // reset create context

        $this->act_finicial = $activity->finicial;
        $this->act_ffinal = $activity->ffinal;
        $this->act_topic = $activity->topic;
        $this->act_thematic = $activity->thematic;
        $this->act_references = $activity->references;
        $this->act_teaching = $activity->teaching;
        $this->act_learning = $activity->learning;
        $this->act_description = $activity->description;
        $this->act_observations = $activity->observations;
        $this->act_achievement = $activity->achievement ?? null;
        $this->act_weighting = $activity->weighting ?? null;
        $this->act_name = $activity->name ?? null;
        $this->act_status = (bool) $activity->status;

        $this->close();
        $this->modeEditAct = true;
        $this->dispatchBrowserEvent('open-edit-modal');
    }

    public function updateAct()
    {
        $this->validate();

        $activity = Activity::findOrFail($this->activity_id);
        $activity->update([
            'finicial' => $this->act_finicial,
            'ffinal' => $this->act_ffinal,
            'topic' => $this->act_topic,
            'thematic' => $this->act_thematic,
            'references' => $this->act_references,
            'teaching' => $this->act_teaching,
            'learning' => $this->act_learning,
            'description' => $this->act_description,
            'observations' => $this->act_observations,
            'achievement' => $this->act_achievement,
            'weighting' => $this->act_weighting,
            'name' => $this->act_name,
            'status' => $this->act_status ?? false,
        ]);

        $this->showSwal('¡Actualizada!', 'Actividad actualizada exitosamente.', 'success');
        $this->close();
        $this->modeIndex = true;
        $this->resetActForm();
        $this->dispatchBrowserEvent('close-edit-modal');
    }

    public function setShow($id)
    {
        $this->activity = Activity::with('pevaluacion.pensum.asignatura','pevaluacion.pensum.grado','pevaluacion.seccion','pevaluacion.lapso','pevaluacion.profesor')->findOrFail($id);
        $this->activity_id = $this->activity->id;
        $this->act_selected_pevaluacion = null;
        $this->close();
        $this->modeShowAct = true;
        $this->dispatchBrowserEvent('open-show-modal');
    }

    public function deleteAct($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();

        $this->showSwal('¡Eliminada!', 'Actividad eliminada exitosamente.', 'success');
        $this->close();
        $this->modeIndex = true;
    }

    private function resetActForm()
    {
        $this->act_finicial = null;
        $this->act_ffinal = null;
        $this->act_topic = null;
        $this->act_thematic = null;
        $this->act_references = null;
        $this->act_teaching = null;
        $this->act_learning = null;
        $this->act_description = null;
        $this->act_observations = null;
        $this->act_achievement = null;
        $this->act_weighting = null;
        $this->act_name = null;
        $this->act_status = false;
        $this->resetErrorBag();
    }

    /**
     * Dispatch a SweetAlert notification via browser event.
     */
    public function showSwal($title, $html, $icon = 'success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer' => 6000,
            'icon' => $icon,
        ]);
    }
}
