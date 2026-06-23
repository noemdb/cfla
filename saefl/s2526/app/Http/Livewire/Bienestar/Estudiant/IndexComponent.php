<?php

namespace App\Http\Livewire\Bienestar\Estudiant;

use App\Models\app\Incident\Incident;
use App\Models\app\Estudiant;
use Livewire\Component;
use Livewire\WithPagination;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\Lapso;

class IndexComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '',$paginate=10; //'name',teacher,cource

    public $envents;

    public $estudiant,$lapsos,$lapso_active;

    public $list_grado,$list_seccion,$grado_id,$seccion_id;

    public $modeIndex,$modeInfo,$modeTline,$modeClose,$modeFilter;
    public $finicial,$ffinal,$status_aggression,$status_pedagogical,$status_announcement,$date_announcement;
    public $status_notify,$status_notify_agreement,$status_close_filter;
    public $loadFilter;
    public $list_status_close, $list_comment,$events;

    public function cleanView()
    {
        $this->modeIndex = false;
        $this->modeInfo = false;
        $this->modeTline = false;
        $this->modeClose = false;
        $this->modeFilter = false;
    }

    public function mount()
    {
        $this->cleanView();
        $this->modeIndex = true;
        $this->list_status_close = Incident::list_status_close();
        $this->list_comment = Incident::COLUMN_COMMENTS;

        $this->list_grado = Grado::list_pestudio_grado();
        $this->list_seccion = Array();
        $this->lapsos = Lapso::all();
        $this->lapso_active = Lapso::current();
    }

    public function tline($estudiant_id)
    {
        $estudiant = Estudiant::findOrfail($estudiant_id);
        $this->events = $estudiant->incident_events;
        $this->cleanView();
        $this->modeTline = true;
    }

    public function updatedGradoId($value)
    {
        $this->list_seccion = ($this->grado_id) ? Seccion::where('grado_id',$this->grado_id)->pluck('name','id') : Array() ;
    }

    public function render()
    {
        $search = $this->search;
        $estudiants = Estudiant::select('estudiants.*')
        ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
        ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
        ->join('grados', 'grados.id', '=', 'seccions.grado_id')
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

        return view('livewire.bienestar.estudiant.index-component', [
            'estudiants' => $estudiants,
        ]);
    }

    public function viewInfo($id)
    {
        $this->estudiant = Estudiant::findOrfail($id); //dd($this->estudiant);
        $this->cleanView();
        $this->modeInfo = true;
    }

    public function close()
    {
        $this->cleanView();
        $this->modeIndex = true;
    }

    public function showFilter()
    {
        $this->cleanView();
        $this->modeFilter = true;
    }

    public function applyFilter()
    {
        $this->cleanView();
        $this->modeIndex = true;
    }

    public function clearFilter()
    {
        $this->mount();
    }
}
