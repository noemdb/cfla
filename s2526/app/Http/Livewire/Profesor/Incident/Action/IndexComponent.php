<?php

namespace App\Http\Livewire\Profesor\Incident\Action;

use App\Models\app\Estudiant;
use App\Models\app\Incident\Incident;
use App\Models\app\Incident\IncidentAction;
use App\Models\app\Incident\IncidentCorrective;
use App\Models\app\Pescolar\Grado;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class IndexComponent extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    use ValidationTrait;
    
    public IncidentAction $incident_action;
    public $incident_actions;

    protected $listeners = ['loadAction'];
    public function loadAction() { $this->mount(); $this->close(); $this->render(); }

    public $incident,$incident_id,$profesor,$profesor_id,$estudiant_id,$estudiant,$estudiant_selected;
    public $incident_agreements,$representant,$autoridad2,$autoridad3,$status_notify_close;
    public $status_close,$close_observations;
    public $search = '',$paginate=10;
    public $student_records;
    public $modeIndex,$modeEdit,$modeCreate,$modeView,$modeClose;
    public $institucion,$autoridad1,$toDate;
    public $list_comment;
    public $status_last,$status_first,$saveInto;
    public $student_records_list,$list_profesor,$list_type,$list_reason;
    public $list_grado,$list_seccion,$grado_id,$seccion_id;
    public $list_status_close;
    public  $list_corrective,$list_correctives;

    public function create($incident_id)
    {
        $incident = Incident::findOrfail($incident_id);
        $estudiant = Estudiant::findOrfail($incident->estudiant_id);

        $this->incident = $incident;
        $this->incident_actions = $incident->incident_actions;
        $this->incident_id = $incident->id;
        $fault_id = $incident->fault_id;
        $this->list_correctives = IncidentCorrective::list_corrective($fault_id);

        $this->estudiant_selected = $estudiant;
        $this->estudiant_id = $estudiant->id;
        $this->incident_action = new IncidentAction;

        $this->cleanView();
        $this->modeCreate = true;
    }

    public function save($id)
    {
        $incident = Incident::findOrfail($id); 
        $this->incident = $incident;
        $this->incident_action->incident_id = $incident->id;
        $this->incident_action->status_selected = true;
        $this->incident_action->description = $this->incident->description;
        $this->validate();
        $this->incident_action->save();

        $this->incident_action = new IncidentAction;

        $this->incident_actions = $incident->incident_actions;
        $menssege = 'Correctivo agregado.';
        $title = '¡Excelente, buen trabajo! Correctivo agregado.';
		$html = 'Operación realizada exitosamente!';
		$this->showSwal($title,$html);
        //$this->close();
    }

    public function delete($id)
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

    public function mount()
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $this->profesor = ($user->IsProfesor()) ? $user->profesor : null;
        $this->profesor_id = ($this->profesor) ? $this->profesor->id : null;

        $this->cleanView();
        $this->modeIndex = true;

        $this->list_corrective = collect();

        $this->list_comment = IncidentAction::COLUMN_COMMENTS;
        $this->list_status_close = Incident::list_status_close();

        $this->list_grado = Grado::list_pestudio_grado();
        $this->list_seccion = Array();
    }

    public function render()
    {
        $profesor = $this->profesor;
        $search = $this->search;
        $incidents = Incident::select('incidents.*')
            ->join('estudiants', 'estudiants.id', '=', 'incidents.estudiant_id')
            ;

        $incidents = (!empty($search)) ? $incidents->where(
            function($query) use ($search) {
                $query->orwhere('estudiants.lastname','like', '%'.$search.'%')
                    ->orWhere('estudiants.name','like','%'.$search.'%')
                    ->orWhere('estudiants.ci_estudiant','like','%'.$search.'%')
                    ;
            })
            : $incidents;


        $incidents = (!$profesor->is_profesor_guia) ? $incidents->where('incidents.profesor_id',$this->profesor_id) : $incidents;

        $incidents = $incidents->orderBy('incidents.created_at','desc');

        $incidents = $incidents->paginate($this->paginate);

        return view('livewire.profesor.incident.action.index-component', [
            'incidents' => $incidents,
        ]);
    }

    public function close()
    {
        $this->cleanView();
        $this->modeIndex = true;
        $this->incident_action = new IncidentAction;
        $this->estudiant_selected = new Estudiant();
    }
    public function cleanView()
    {
        $this->modeIndex = false;
        $this->modeCreate = false;
        $this->modeEdit = false;
        $this->modeEdit = false;
        $this->modeView = false;
        $this->modeClose = false;
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
