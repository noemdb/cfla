<?php

namespace App\Http\Livewire\Movile\Profesor\Incident;

use Livewire\Component;

use App\Models\app\Incident\Incident;
use App\Models\app\Estudiant;
use App\User;
use Illuminate\Support\Facades\Auth;

class CreateComponent extends Component
{
    use IncidentTrait;

    public Incident $incident;

    public $incidents;

    public $status_aggression,$status_pedagogical,$status_announcement,$date_announcement;

    public $profesor_id,$estudiant_id,$search = '';

    public $list_comment,$list_estudiants,$list_type,$list_reason,$list_status_close;

    public function mount()
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $profesor = ($user->IsProfesor()) ? $user->profesor : null;

        $this->profesor_id = ($profesor) ? $profesor->id : null;
        $this->incidents = ($profesor) ? $profesor->incidents : collect();
        $this->list_estudiants = collect();

        $this->incident = New Incident;

        $this->list_type = Incident::list_type();
        $this->list_status_close = Incident::list_status_close();
        $this->list_comment = Incident::COLUMN_COMMENTS;
    }

    public function save()
    {
        //dd($this->incident);

        $this->incident->estudiant_id = $this->estudiant_id;
        $this->incident->profesor_id = $this->profesor_id;
        $this->incident->user_id = Auth::id(); //dd($this->incident);
        $this->validate();
        $this->incident->save(); //dd($this->incident);
        session()->flash('operp_ok', 'Guardado!!!.');
        $this->incident = New Incident;
        $this->search = null;
        $this->list_estudiants = collect();
        $this->incident->estudiant_id = null;

        $this->emit('addIncident');
    }

    public function render()
    {
        $search = $this->search;

        if ($search) {
            $this->list_estudiants = Estudiant::select('estudiants.*')
            ->selectRaw("CONCAT(estudiants.ci_estudiant,' - ',estudiants.name,' ',estudiants.lastname) as estudiant_fullname")
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->where(
            function($query) use ($search) {
                $query->orwhere('estudiants.lastname','like', '%'.$search.'%')
                    ->orWhere('estudiants.name','like','%'.$search.'%')
                    ->orWhere('estudiants.ci_estudiant','like','%'.$search.'%')
                    ;
            })
            ->pluck('estudiant_fullname','id')
            ;
        }

        return view('livewire.movile.profesor.incident.create-component');
    }
}
