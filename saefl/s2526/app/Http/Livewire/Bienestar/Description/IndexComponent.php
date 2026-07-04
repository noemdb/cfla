<?php

namespace App\Http\Livewire\Bienestar\Description;

use App\Models\app\Incident\Incident;
use App\Models\app\Incident\IncidentDescription;
use App\Models\app\Incident\IncidentReason;
use Livewire\Component;
use Livewire\WithPagination;

class IndexComponent extends Component
{
    use WithPagination;
    use ValidateTrait;
    use UpdatedTrait;
    protected $paginationTheme = 'bootstrap';

    public IncidentDescription $incident_description;

    public $incident_description_selected;

    public $search = '',$paginate=10;

    public $modeIndex,$modeEdit,$modeCreate,$modeView,$modeTline,$modeClose,$modeFilter,$modeShow;

    public $incident_description_id;

    public $list_comment,$list_type,$list_reason;

    public function mount()
    {
        $this->modeIndex = true;
        $this->incident_description = new IncidentDescription;
        $this->incident_description_selected = new IncidentDescription;

        $this->list_type = Incident::list_type();
        $this->list_reason = IncidentReason::list_reason_category();//dd($this->list_reason); 
        $this->list_comment = IncidentDescription::COLUMN_COMMENTS;
    }

    public function render()
    {
        $incident_descriptions = IncidentDescription::select('incident_descriptions.*');
        $search = $this->search;
        
        // $incident_descriptions = ($this->search) ? $incident_descriptions->where('incident_descriptions.name','like','%'.$this->search.'%') : $incident_descriptions ;
        $incident_descriptions = ($search) ? $incident_descriptions->where('incident_descriptions.name','like', '%'.$search.'%') : $incident_descriptions ;

        $incident_descriptions = $incident_descriptions->paginate($this->paginate);

        return view('livewire.bienestar.description.index-component',[
            'incident_descriptions' => $incident_descriptions,
        ]);
    }

    public function create()
    {
        $this->cleanView();
        $this->modeCreate = true;
        $this->incident_description = new IncidentDescription;
        $this->incident_description_selected = new IncidentDescription;
    }

    public function edit($id)
    {
        $incident_description = IncidentDescription::findOrfail($id); //dd($incident);

        $this->incident_description_selected = $incident_description;
        $this->incident_description = $incident_description;

        $this->cleanView();
        $this->modeEdit = true;
    }

    public function save()
    {
        $this->validate(); //dd($this->incident_description);
        $this->incident_description->save();        

        $this->close();
        session()->flash('operp_ok', 'Guardado!!!.');
    }

    public function cleanView()
    {
        $this->modeIndex = false;
        $this->modeCreate = false;
        $this->modeEdit = false;        
    }

    public function close()
    {
        $this->cleanView();
        $this->modeIndex = true;

        $this->incident_description = new IncidentDescription;
        $this->incident_description_selected = new IncidentDescription;
    }

    public function destroy($id)
    {
        $incident_description = IncidentDescription::findOrfail($id); //dd($incident);
        if ($incident_description) {
            $incident_description->delete();
            $this->close();
            session()->flash('operp_ok', 'Eliminado!!!.');
        }
    }
}
