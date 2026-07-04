<?php

namespace App\Http\Livewire\Bienestar\Overview;

use Livewire\Component;
use Livewire\WithPagination;

use Jenssegers\Date\Date;

use App\Models\app\Pescolar\Profesor;

class IndexComponent extends Component
{
	use WithPagination;

	protected $paginationTheme = 'bootstrap';

	public $incidents_announcements,$incidentsWeekelys;
	public $modeIndex,$modeEdit,$modeCreate,$modeView,$modeTline,$modeClose,$modeFilter,$modeWeekely,$modeDetails;
	public $incidents,$incident_id,$profesor_id,$profesor;
	public $finicial,$ffinal;
	public $search = '',$paginate=10; //'name',teacher,cource

    public $current,$start,$end;

    public function mount()
    {
        $this->cleanView();
        $this->modeIndex = true;

        $this->current = Date::now()->startOfMonth();
        $this->start = Date::now()->startOfMonth();
        $this->end = Date::now()->endOfMonth();
    }

    public function render()
    {
    	$search = $this->search;
    	$profesors = Profesor::select('profesors.*')
	        ->join('incidents', 'profesors.id', '=', 'incidents.profesor_id')
	        ->where('incidents.status_active',true);

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

        $profesors = $profesors->paginate($this->paginate);

        return view('livewire.bienestar.overview.index-component',[
            'profesors' => $profesors,
        ]);
    }

    public function weekely($profesor_id)
    {
        $profesor = Profesor::findOrFail($profesor_id);
        if ($profesor) {
            $this->profesor = $profesor;
            $this->incidents_announcements = $profesor->incidents_announcements;
            $this->incidents = $profesor->incidents; //dd($this->incidents);
            $this->cleanView();
            $this->modeWeekely = true;
        }        
    }

    public function viewDetailsRange($finicial,$ffinal)
    {
        $this->incidentsWeekelys = $this->profesor->getIncidentsRange($finicial,$ffinal);
        $this->modeWeekely = true;
        $this->modeDetails = true;
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
        $this->modeWeekely = false;
        $this->modeDetails = false;
    }

    public function goModeIndex()
	{
		$this->cleanView();
		$this->modeIndex = true;
		$this->search = null;
		$this->finicial = null;
		$this->ffinal = null;
	}

    public function close()
    {
        $this->cleanView();
        $this->modeIndex = true;
    }
    public function closeDetails()
    {
        $this->cleanView();
        $this->modeWeekely = true;
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
