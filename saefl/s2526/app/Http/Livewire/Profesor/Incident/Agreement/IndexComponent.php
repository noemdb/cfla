<?php

namespace App\Http\Livewire\Profesor\Incident\Agreement;

use Carbon\Carbon;
use Jenssegers\Date\Date;
use Livewire\Component;
use Livewire\WithPagination;

use App\Http\Controllers\Bienestar\Email\IncidentAgreementController;

use App\Models\app\Incident\Incident;
use App\Models\app\Incident\IncidentAgreement;
use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\User;
use Illuminate\Support\Facades\Auth;

class IndexComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['loadAgreement'];
    public function loadAgreement()
    {
        $this->mount();
        $this->close();
        $this->render();
    }

    use AgreementTrait;
    public IncidentAgreement $incident_agreement;

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

    public function mount()
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $this->profesor = ($user->IsProfesor()) ? $user->profesor : null;
        $this->profesor_id = ($this->profesor) ? $this->profesor->id : null;

        $this->cleanView();
        $this->modeIndex = true;

        $this->list_comment = IncidentAgreement::COLUMN_COMMENTS;
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

        $incidents = $incidents->paginate($this->paginate); //dd($incidents);

        return view('livewire.profesor.incident.agreement.index-component', [
            'incidents' => $incidents,
        ]);
    }

    public function create($incident_id)
    {
        $incident = Incident::findOrfail($incident_id);
        $estudiant = Estudiant::findOrfail($incident->estudiant_id);

        $this->incident = $incident;
        $this->incident_id = $incident->id;

        $this->estudiant_selected = $estudiant;
        $this->estudiant_id = $estudiant->id;
        $this->incident_agreement = new IncidentAgreement;

        $this->cleanView();
        $this->modeCreate = true;
    }

    public function edit($id)
    {
        $incident_agreement = IncidentAgreement::findOrfail($id);
        $incident = Incident::findOrfail($incident_agreement->incident_id);
        $estudiant = Estudiant::findOrfail($incident->estudiant_id);

        $this->incident_agreement = $incident_agreement;
        $this->incident = $incident;
        $this->incident_id = $incident->id;

        $this->estudiant = $estudiant;
        $this->estudiant_id = $estudiant->id;

        $this->cleanView();
        $this->modeEdit = true;
    }

    public function viewMail($id)
    {
        $incident = Incident::findOrfail($id);
        $incident_agreements = $incident->incident_agreements;
        $estudiant = Estudiant::findOrfail($incident->estudiant_id);

        $this->incident = $incident;
        $this->incident_agreements = $incident_agreements;
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
        $this->incident_agreement->incident_id = $this->incident_id;
        $this->incident_agreement->user_id = Auth::id();
        $this->incident_agreement->save();
        $this->cleanView();
        $this->modeIndex = true;
        session()->flash('operp_ok', 'Guardado!!!.');
    }

    public function close()
    {
        $this->cleanView();
        $this->modeIndex = true;
        $this->incident_agreement = new IncidentAgreement;
        $this->estudiant_selected = new Estudiant;
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

    public function destroy($id)
    {
        $agreement = IncidentAgreement::find($id);
        if ($agreement) {
            $agreement->delete();
            $this->close();
            session()->flash('operp_ok', 'Eliminado!!!.');
        }
    }

    public function sendMail($id)
    {
        $incident = Incident::findOrfail($id); //dd($incident);
        $incident_agreements = $incident->incident_agreements;
        if (! $incident_agreements->isEmpty()) {
            $jobSend = new IncidentAgreementController();
            $messegesSend = $jobSend->messegesSend($incident);
            $incident->status_notify_agreement = true;
            $incident->date_notify_agreement_email = Carbon::now();
            $incident->save();
            session()->flash('operp_ok', 'Excelecte!!!, buen trabajo. Notificación enviada.');
        } else {
            session()->flash('operp_ok', 'No hay acuerdos registrados!!!.');
        }
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

}
