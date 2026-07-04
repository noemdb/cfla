<?php

namespace App\Http\Livewire\Bienestar\Incident;

use App\Http\Controllers\Bienestar\Email\IncidentAgreementController;
use Carbon\Carbon;
use Jenssegers\Date\Date;
use Livewire\Component;
use Livewire\WithPagination;

// use App\Http\Controllers\Bienestar\Email\MailerController;

use App\Http\Controllers\Bienestar\Email\IncidentController;

use App\Models\app\Incident\Incident;
use App\Models\app\Incident\IncidentDescription;
use App\Models\app\Incident\IncidentReason;
use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\Auth;

class IndexComponent extends Component
{
    use WithPagination;
    use IncidentTrait;
    use UpdatedTrait;
    public Incident $incident;
    protected $paginationTheme = 'bootstrap';

    public $incident_id,$estudiant_id,$estudiant,$events;
    public $representant;

    public $search = '',$paginate=10; //'name',teacher,cource,status_notify_close
    public $student_recordt_id,$student_records,$estudiant_selected;
    public $envents;
    public $status_notify_close,$close_observations,$status_close;
    public $finicial,$ffinal,$status_aggression,$status_pedagogical,$status_announcement,$date_announcement;
    public $status_notify,$status_notify_agreement,$status_close_filter;

    public $modeIndex,$modeEdit,$modeCreate,$modeView,$modeTline,$modeClose,$modeFilter,$modeShow;
    public $loadFilter;

    public $institucion,$autoridad1,$autoridad2,$autoridad3,$toDate;

    public $list_comment;

    public $status_last,$status_first,$saveInto;

    public $student_records_list,$list_profesor,$list_type,$list_reason,$list_status_close;
    
    public $list_description,$list_valoration,$list_description_tab;

    public $list_grado,$list_seccion,$grado_id,$seccion_id;

    public $lapso_current,$status_valoration;

    public function mount()
    {
        $this->cleanView();
        $this->modeIndex = true;
        $this->list_comment = Incident::COLUMN_COMMENTS;
        $this->list_profesor = Profesor::list_profesors(true);

        $this->list_type = Incident::list_type();
        $this->list_reason = IncidentReason::list_reason_category(); //dd($this->list_reason);
        $this->list_status_close = Incident::list_status_close();
        $this->list_valoration = Incident::list_valoration();
        $this->list_description_tab = IncidentDescription::list_description_tab();
        $this->list_description = IncidentDescription::list_description_tab();
        $this->lapso_current = Lapso::getCurrentOrFirst();

        $this->list_grado = Grado::list_pestudio_grado();
        $this->list_seccion = Array();
    }

    public function updatedGradoId($value)
    {
        $this->list_seccion = ($this->grado_id) ? Seccion::where('grado_id',$this->grado_id)->pluck('name','id') : Array() ;
    }

    public function applyFilter()
    {
        $this->cleanView();
        $this->modeIndex = true;
    }

    public function render()
    {
        $search = $this->search;
        $estudiants = Estudiant::select('estudiants.*')
        ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
        ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
        ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
        ;

        if ($this->finicial || $this->ffinal || $this->status_aggression || $this->status_pedagogical || $this->status_announcement || $this->date_announcement || $this->status_notify || $this->status_notify_agreement || $this->status_close_filter) {
            $this->loadFilter = true;
            $estudiants = $estudiants->join('incidents', 'estudiants.id', '=', 'incidents.estudiant_id');

            $estudiants = ($this->finicial) ? $estudiants->where('incidents.created_at','>=',$this->finicial) : $estudiants ;
            $estudiants = ($this->ffinal) ? $estudiants->where('incidents.created_at','<=',$this->ffinal) : $estudiants ;
            $estudiants = ($this->status_aggression) ? $estudiants->where('incidents.status_aggression',true) : $estudiants ;
            $estudiants = ($this->status_pedagogical) ? $estudiants->where('incidents.status_pedagogical',true) : $estudiants ;
            $estudiants = ($this->status_announcement) ? $estudiants->where('incidents.status_announcement',true) : $estudiants ;
            $estudiants = ($this->date_announcement) ? $estudiants->where('incidents.date_announcement',$this->date_announcement) : $estudiants ;
            $estudiants = ($this->status_notify) ? $estudiants->where('incidents.status_notify',true) : $estudiants ;
            $estudiants = ($this->status_notify_agreement) ? $estudiants->where('incidents.status_notify_agreement',true) : $estudiants ;
            $estudiants = ($this->status_close_filter) ? $estudiants->where('incidents.status_close',true) : $estudiants ;
        } else {
            $this->loadFilter=false;
        }

        $estudiants = ($this->grado_id) ? $estudiants->where('grados.id',$this->grado_id) : $estudiants ;
        $estudiants = ($this->seccion_id) ? $estudiants->where('seccions.id',$this->seccion_id) : $estudiants ;

        $estudiants = (!empty($search)) ? $estudiants->where(
            function($query) use ($search) {
                $query->orwhere('estudiants.lastname','like', '%'.$search.'%')
                    ->orWhere('estudiants.name','like','%'.$search.'%')
                    ->orWhere('estudiants.ci_estudiant','like','%'.$search.'%')
                    ;
            })
            : $estudiants;

        $estudiants = $estudiants->paginate($this->paginate);

        return view('livewire.bienestar.incident.index-component', [
            'estudiants' => $estudiants,
        ]);
    }

    public function tline($estudiant_id)
    {
        $estudiant = Estudiant::findOrfail($estudiant_id);
        $this->estudiant_selected = $estudiant;
        $this->estudiant_id = $estudiant->id;
        $this->events = $estudiant->incident_events; //dd($this->events);
        $this->cleanView();
        $this->modeTline = true;
    }

    public function cleanView()
    {
        $this->modeIndex = false;
        $this->modeCreate = false;
        $this->modeEdit = false;
        $this->modeView = false;
        $this->modeTline = false;
        $this->modeClose = false;
        $this->modeFilter = false;
        $this->modeShow = false;
    }

    public function create($estudiant_id)
    {
        $estudiant = Estudiant::findOrfail($estudiant_id);
        $this->estudiant_selected = $estudiant;
        $this->estudiant_id = $estudiant->id; //dd($this->estudiant_id);
        $this->incident = new Incident; //dd($this->incident);
        $this->incident->estudiant_id = $estudiant->id;

        $this->cleanView();
        $this->modeCreate = true;
    }

    public function edit($id)
    {
        $incident = Incident::findOrfail($id); //dd($incident);
        $estudiant = Estudiant::findOrfail($incident->estudiant_id);

        $this->estudiant_selected = $estudiant;
        $this->estudiant_id = $estudiant->id;
        $this->incident = $incident;
        $this->student_recordt_id = $incident->id;

        $this->cleanView();
        $this->modeEdit = true;
    }

    public function viewMail($id)
    {
        $incident = Incident::findOrfail($id);
        $estudiant = Estudiant::findOrfail($incident->estudiant_id);

        $this->incident = $incident;
        $this->estudiant = $estudiant;
        $this->estudiant_id = $estudiant->id;
        $this->representant = $estudiant->representant;

        $this->institucion = Institucion::OrderBy('created_at','DESC')->first();
        $this->autoridad1 = Autoridad::getTipoAuthority('2');
        $this->autoridad2 = Autoridad::getTipoAuthority('1');
        $this->autoridad3 = Autoridad::getTipoAuthority('7');
        $this->toDate = Date::now()->format('d F Y');

        $this->cleanView();
        $this->modeView = true;
    }

    public function save()
    {
        $this->validate();
        $this->incident->estudiant_id = $this->estudiant_id;
        $this->incident->user_id = Auth::id();
        $this->incident->save();        
        $this->messegesSendProfesor($this->incident->id);

        $this->close();
        session()->flash('operp_ok', 'Guardado!!!.');
    }

    public function messegesSendProfesor($id)
    {        
        $incident = Incident::findOrFail($id);
        $jobSend = new IncidentController();
        $messegesSend = $jobSend->messegesSendProfesor($incident);
    }

    public function close()
    {
        $this->cleanView();
        $this->modeIndex = true;
        $this->incident = new Incident;
        $this->estudiant_selected = new Estudiant;
    }

    public function destroy($id)
    {
        $incident = Incident::find($id);
        if ($incident) {
            $incident->delete();
            $this->close();
            session()->flash('operp_ok', 'Eliminado!!!.');
        }
    }

    public function sendMail($id)
    {
        $incident = Incident::findOrFail($id); //dd($incident);
        $jobSend = new IncidentController();
        $messegesSend = $jobSend->messegesSend($incident);
        $incident->status_notify = true;
        $incident->date_notify_email = Carbon::now();
        $incident->save();
        $this->close();
        session()->flash('operp_ok', 'Excelecte!!!, buen trabajo. Notificación enviada.');
    }

    public function closeIncidentSave($id)
    {
        $incident = Incident::findOrfail($id);
        $incident->status_close = $this->status_close;
        $incident->status_notify_close = $this->status_notify_close;
        $incident->close_observations = $this->close_observations; //dd($incident);
        $incident->date_close = Carbon::now(); //dd($incident);
        $incident->save();
        $menssege = 'Incidente cerrado.';

        if ($this->status_notify_close) {
            $jobSend = new IncidentAgreementController();
            $messegesSend = $jobSend->messegesSendClose($incident);
            $menssege .= '. Notificación enviada.';
        }

        session()->flash('operp_ok',$menssege);
        $this->close();
    }

    public function closeIncident($id)
    {
        $incident = Incident::findOrfail($id);
        $this->incident = $incident;
        $this->incident_id = $incident->id;

        $this->status_close = $incident->status_close;
        $this->status_notify_close = $incident->status_notify_close;
        $this->close_observations = $incident->close_observations;

        $estudiant = Estudiant::findOrfail($incident->estudiant_id);
        $this->estudiant = $estudiant;
        $this->estudiant_id = $estudiant->id;

        $this->cleanView();
        $this->modeClose = true;
    }

    public function showFilter()
    {
        $this->cleanView();
        $this->modeFilter = true;
    }

    public function clearFilter()
    {
        $this->mount();
    }

    public function show($id)
    {
        $incident = Incident::findOrfail($id); //dd($incident);
        $estudiant = Estudiant::findOrfail($incident->estudiant_id);

        $this->estudiant = $estudiant;
        $this->estudiant_selected = $estudiant;
        $this->estudiant_id = $estudiant->id;
        $this->incident = $incident;
        $this->student_recordt_id = $incident->id;

        $this->cleanView();
        $this->modeShow = true;
    }

}
