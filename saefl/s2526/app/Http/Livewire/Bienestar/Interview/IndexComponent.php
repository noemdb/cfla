<?php

namespace App\Http\Livewire\Bienestar\Interview;

use Carbon\Carbon;
use Jenssegers\Date\Date;
use Livewire\Component;
use Livewire\WithPagination;

use App\Models\app\Incident\Incident;
use App\Models\app\Incident\IncidentReason;
use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;

class IndexComponent extends Component
{
    use WithPagination;
    // public Incident $incident;
    protected $paginationTheme = 'bootstrap';

    public $incidents,$incident_id,$profesor_id,$profesor,$incidents_announcements;

    public $finicial,$ffinal;

    public $search = '',$paginate=10; //'name',teacher,cource
    public $modeIndex,$modeView,$modeCalendar,$modeDetails;
    public $institucion,$autoridad1,$toDate;
    public $list_comment;
    public $status_last,$status_first,$saveInto;
    public $student_records_list,$list_profesor,$list_type,$list_reason;
    public $list_grado,$list_seccion,$grado_id,$seccion_id;

    public $current,$start,$end;

    public function mount()
    {
        $this->modeIndex = true;
        $this->modeView = false;
        $this->modeCalendar = false;
        $this->modeDetails = false;

        $this->current = Date::now()->startOfMonth();
        $this->start = Date::now()->startOfMonth();
        $this->end = Date::now()->endOfMonth();

        $this->list_comment = Incident::COLUMN_COMMENTS;
        $this->list_profesor = Profesor::list_profesors(true);
        $this->list_type = Incident::list_type();
        $this->list_reason = IncidentReason::list_reason_category(); //dd($this->list_reason);
        $this->list_grado = Grado::list_pestudio_grado();
        $this->list_seccion = Array();
    }

    public function render()
    {
        $search = $this->search;
        $profesors = Profesor::select('profesors.*')
            ->join('incidents', 'profesors.id', '=', 'incidents.profesor_id')
            ->groupBy('profesors.id')
        ;
        $profesors = ($this->finicial) ? $profesors->where('incidents.created_at','>=',$this->finicial) : $profesors ;
        $profesors = ($this->ffinal) ? $profesors->where('incidents.created_at','<=',$this->ffinal) : $profesors ;

        $profesors = (!empty($search)) ? $profesors->where(
            function($query) use ($search) {
                $query->orwhere('profesors.lastname','like', '%'.$search.'%')
                    ->orWhere('profesors.name','like','%'.$search.'%')
                    ->orWhere('profesors.ci_profesor','like','%'.$search.'%')
                    ;
            })
            : $profesors;
        $profesors = $profesors->paginate($this->paginate); //dd($profesors);
        return view('livewire.bienestar.interview.index-component', [
            'profesors' => $profesors,
        ]);
    }

    public function viewCalendar($id)
    {
        $profesor = Profesor::findOrfail($id);
        $this->incidents_announcements = $profesor->incidents_announcements;
        $this->profesor = $profesor;
        $this->profesor_id = $profesor->id;
        $this->incidents = $profesor->incidents;
        $this->cleanView();
        $this->modeCalendar = true;
    }

    public function viewInterview($id)
    {
        $profesor = Profesor::findOrfail($id);
        $this->incidents_announcements = $profesor->incidents_announcements;
        $this->profesor = $profesor;
        $this->profesor_id = $profesor->id;
        $this->incidents = $profesor->incidents; //dd($this->incidents);
        $this->cleanView();
        $this->modeView = true;
    }

    public function viewDetails($date)
    {
        $this->incidents = $this->profesor->incidents_announcements->where('date_announcement',$date);
        $this->cleanView();
        $this->modeCalendar = true;
        $this->modeDetails = true;
    }

    public function close()
    {
        $this->cleanView();
        $this->modeIndex = true;
    }

    public function closeDetails()
    {
        $this->cleanView();
        $this->modeCalendar = true;
        $this->modeDetails = false;
    }

    public function cleanView()
    {
        $this->modeIndex = false;
        $this->modeView = false;
        $this->modeCalendar = false;
        $this->modeDetails = false;
    }

    public function lastMonth()
    {
        $month =$this->start->sub('1 month'); //dd($this->current);
        $this->current = $month->copy()->startOfMonth();
        $this->start = $month->copy()->startOfMonth();
        $this->end = $month->copy()->endOfMonth(); //dd($this->current,$this->start,$this->end);
    }

    public function nextMonth()
    {
        $month =$this->start->add('1 month'); //dd($this->current);
        $this->current = $month->copy()->startOfMonth();
        $this->start = $month->copy()->startOfMonth();
        $this->end = $month->copy()->endOfMonth(); //dd($this->current,$this->start,$this->end);
    }

    public function lastYear()
    {
        $month =$this->start->sub('1 year'); //dd($this->current);
        $this->current = $month->copy()->startOfMonth();
        $this->start = $month->copy()->startOfMonth();
        $this->end = $month->copy()->endOfMonth(); //dd($this->current,$this->start,$this->end);
    }

    public function nextYear()
    {
        $month =$this->start->add('1 year'); //dd($this->current);
        $this->current = $month->copy()->startOfMonth();
        $this->start = $month->copy()->startOfMonth();
        $this->end = $month->copy()->endOfMonth(); //dd($this->current,$this->start,$this->end);
    }
    
}
