<?php

namespace App\Http\Livewire\Profesor\Incident\Main;
//livewire.profesor.incident.main.index-component

use Livewire\Component;

use Livewire\WithPagination;

use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;

use Carbon\Carbon;
use Jenssegers\Date\Date;

use App\User;
use App\Models\app\Estudiant;
use Illuminate\Support\Facades\Auth;
use App\Models\app\Bienestar\IncidentReason;

use App\Http\Controllers\Bienestar\Email\IncidentController;
use App\Http\Controllers\Bienestar\Email\IncidentAgreementController;
use App\Models\app\Incident\Incident;
use App\Models\app\Incident\IncidentAction;
use App\Models\app\Incident\IncidentCorrective;
use App\Models\app\Incident\IncidentDuty;
use App\Models\app\Incident\IncidentFault;
use App\Models\app\Pescolar\Lapso;

class IndexComponent extends Component
{
	use WithPagination;
	protected $paginationTheme = 'bootstrap';

	use IncidentTrait;
	use UpdatedTrait;

	public Incident $incident;
	public IncidentAction $incident_action;
    public User $user;

    public $incident_correctives,$incident_actions;

    public $help_description;

    public $profesor,$incidents,$profesor_id;
    public $estudiant_id,$search = null,$paginate=10;

    public $estudiant_selected,$student_recordt_id;
    public $incident_id,$status_close,$status_notify_close,$close_observations,$estudiant,$representant,$events,$institucion,$autoridad1,$autoridad2,$autoridad3,$toDate;

    public $modeIndex,$modeEdit,$modeCreate,$modeView,$modeTline,$modeClose,$modeFilter,$modeShow,$modeAction;
    public $list_comment,$student_records_list,$list_profesor,$list_duty,$list_fault,$list_corrective,$list_correctives,$list_status_close;
    public $list_grado,$list_seccion,$grado_id,$seccion_id,$list_description,$list_valoration,$list_description_tab,$list_reason;

    public $lapso_current,$status_valoration;

    public $filter, $filter_list;

    protected $listeners = ['loadIncident'];

    public function mount()
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $this->user = $user;
        $this->profesor = ($user->IsProfesor()) ? $user->profesor : null;
        $this->profesor_id = ($this->profesor) ? $this->profesor->id : null;
        $this->modeIndex = true;
        $this->updateIndex();

        $this->list_comment = Incident::COLUMN_COMMENTS;
        $this->list_duty = IncidentDuty::list_duty();
        $this->list_fault = IncidentFault::list_fault();
        $this->list_corrective = collect();
        // $this->list_correctives = collect();
        $this->lapso_current = Lapso::getCurrentOrFirst();
        $this->filter_list = ['Guiatura'=>'Guiatura','Todos'=>'Todos'];
    }

    public function loadIncident()
    {
        $this->close();
        $this->render();
    }        

    public function render()
    {
        $search = $this->search;

        $estudiants = collect(New Estudiant);//dd($estudiants);

        if ($search && strlen($search)>=3) {
            $estudiants = Estudiant::select('estudiants.*')
            ->selectRaw("CONCAT(estudiants.ci_estudiant,' - ',estudiants.name,' ',estudiants.lastname) as estudiant_fullname")
            ->where(
                function($query) use ($search) {
                    $query->orwhere('estudiants.lastname','like', '%'.$search.'%')
                        ->orWhere('estudiants.name','like','%'.$search.'%')
                        ->orWhere('estudiants.ci_estudiant','like','%'.$search.'%')
                        ;
                })
            ;

            if ($this->filter=="Guiatura") {
                $arr_id = $this->profesor->guia_estudiants->pluck('id'); //dd($arr_id);
                $estudiants = $estudiants->WhereIn('estudiants.id',$arr_id);
            }

            $estudiants = $estudiants->paginate($this->paginate) ;
        } else {
            $this->updateIndex();
        }

        return view('livewire.profesor.incident.main.index-component', [
            'estudiants' => $estudiants,
        ]);
    }

    public function updateIndex()
    {
        $this->incidents = ($this->profesor) ? $this->profesor->incidents : collect();
    }

    public function create($estudiant_id)
    {
        $estudiant = Estudiant::findOrfail($estudiant_id);
        $this->estudiant_selected = $estudiant;
        $this->estudiant_id = $estudiant->id;
        $this->incident = new Incident;
        $this->incident->estudiant_id = $estudiant->id;

        $this->cleanView();
        $this->modeCreate = true; //dd($this->list_corrective);
    }

    public function edit($id)
    {
        $incident = Incident::findOrfail($id); //dd($incident);
        $estudiant = Estudiant::findOrfail($incident->estudiant_id);
        $fault_id = $incident->fault_id;

        $this->estudiant_selected = $estudiant;
        $this->estudiant_id = $estudiant->id;
        $this->incident = $incident;
        $this->student_recordt_id = $incident->id;

        $this->cleanView();
        $this->modeEdit = true;
        $this->list_fault = IncidentFault::list_fault($fault_id);
        $this->list_corrective = IncidentCorrective::list_corrective($fault_id); 
    }

    public function save()
    {        
        $this->incident->estudiant_id = $this->estudiant_id;
        $this->incident->profesor_id = $this->profesor_id;
        $this->incident->user_id = Auth::id();

        $this->validate();        

        $this->incident->save();

        $estudiant = $this->incident->estudiant;
        $profesor = $this->incident->profesor;
        $lapso = Lapso::current();
        $status_profesor_guia = $estudiant->isProfesorGuia($profesor->id,$lapso->id);

        if (! $status_profesor_guia ) {
            $id = $this->incident->id;
            $incident = Incident::findOrFail($id);
            $jobSend = new IncidentController();
            $messegesSend = $jobSend->messegesSendProfesor($incident);
        }

        $this->close();
        // session()->flash('operp_ok', 'Guardado!!!.');

        $title = '¡Excelente, buen trabajo. Guardado!!!';
		$html = 'Operación realizada exitosamente!';
		$this->showSwal($title,$html); 
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

    public function cleanView()
    {
        $this->resetValidation();
        $this->list_corrective = collect();
        $this->modeIndex = false;
        $this->modeCreate = false;
        $this->modeShow = false;
        $this->modeEdit = false;
        $this->modeView = false;
        $this->modeTline = false;
        $this->modeClose = false;
        $this->modeFilter = false;
        $this->modeAction = false;
    }

    public function close()
    {
        $this->cleanView();
        $this->modeIndex = true;
        $this->incident = new Incident;
        $this->estudiant_selected = new Estudiant;
        $this->list_corrective = collect();
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

    public function closeIncidentSave($id)
    {
        $incident = Incident::findOrfail($id);
        $incident->status_close = true;
        $incident->status_notify_close = $this->status_notify_close;
        $incident->close_observations = $this->close_observations; //dd($incident);
        $incident->date_close = Carbon::now(); //dd($incident);
        $this->validate([
        	'close_observations' => 'required|min:6',
        ],[],['close_observations' => 'Observación de cierre',]);
        $incident->save();
        $menssege = 'Incidente cerrado.';

        if ($this->status_notify_close) {
            $jobSend = new IncidentAgreementController();
            $messegesSend = $jobSend->messegesSendClose($incident);
            $menssege .= '. Notificación enviada.';
        }
        $this->close();

        // session()->flash('operp_ok',$menssege);

        $title = '¡Excelente, buen trabajo!'.$menssege;
		$html = 'Operación realizada exitosamente!';
		$this->showSwal($title,$html);        
    }

    public function closeIncident($id)
    {
        $incident = Incident::findOrfail($id);
        $this->incident = $incident;
        $this->incident_id = $incident->id;

        $this->status_close = true;
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

    public function tline($estudiant_id)
    {
        $estudiant = Estudiant::findOrfail($estudiant_id);
        $this->estudiant_selected = $estudiant;
        $this->estudiant_id = $estudiant->id;
        $this->events = $estudiant->incident_events;
        $this->cleanView();
        $this->modeTline = true;
    }

    public function viewMail($id)
    {
        $incident = Incident::findOrfail($id); //dd($incident);
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

    public function createdActions($id)
    {
        $this->cleanView();
        
        $this->modeAction = true;
        $this->incident_action = new IncidentAction;
        $incident = Incident::findOrfail($id); 
        $this->incident_actions = $incident->incident_actions;
        $this->incident = $incident;
        $this->list_correctives = IncidentCorrective::list_corrective($incident->fault_id);

        $estudiant = Estudiant::findOrfail($incident->estudiant_id);
        $this->estudiant = $estudiant;
        $this->estudiant_id = $estudiant->id;
    }

    public function saveAction($id)
    {
        $incident = Incident::findOrfail($id); 
        $this->incident = $incident;
        $this->incident_action->incident_id = $incident->id;
        $this->incident_action->status_selected = true;
        $this->incident_action->description = $this->incident->description; 
        $this->validate([
        	'incident_action.incident_id' => 'required',
        	'incident_action.corrective_id' => 'required',
        ],[],['ncident_action.incident_id' => 'Incidencia','ncident_action.corrective_id' => 'Correctivo',]);
        $this->incident_action->save();

        $this->incident_action = new IncidentAction;

        $this->incident_actions = $incident->incident_actions;
        $menssege = 'Correctivo agregado.';
        $title = '¡Excelente, buen trabajo!, Correctivo agregado.';
		$html = 'Operación realizada exitosamente!';
		$this->showSwal($title,$html);
        //$this->close();
    }

    public function deleteAction($id)
    {
        $incident_action = IncidentAction::findOrfail($id);
        $incident = $incident_action->incident;

        $this->incident_action = $incident_action;
        $this->incident_action->delete();

        $this->incident_actions = $incident->incident_actions;

        $this->incident_action = new IncidentAction;
        $menssege = 'Correctivo eliminado.';
        session()->flash('operp_ok',$menssege);        
        
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente!';
		$this->showSwal($title,$html);
        // $this->close();
    }

    public function showSwal($title,$html,$icon='success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer'=>60000,
            'icon'=>$icon,
            'toast'=>false,
            'position'=>'center',
            'icon' => 'success',
            'allowOutsideClick' => false,
        ]);
    }

}


