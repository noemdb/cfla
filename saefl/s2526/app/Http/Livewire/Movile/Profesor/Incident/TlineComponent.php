<?php

namespace App\Http\Livewire\Movile\Profesor\Incident;

use App\Models\app\Estudiant;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TlineComponent extends Component
{
    public $profesor,$incidents,$profesor_id;
    public $incident_estudiants,$estudiant_id,$search = '';
    public $modeAddInsident;

    protected $listeners = ['addIncident' => 'updateIndex'];

    public function mount()
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $this->profesor = ($user->IsProfesor()) ? $user->profesor : null;
        $this->profesor_id = ($this->profesor) ? $this->profesor->id : null;
        $this->updateIndex();
    }

    public function render()
    {
        $search = $this->search;
        $estudiants = Estudiant::select('estudiants.*','incidents.created_at as incident_created_at')
            ->selectRaw("CONCAT(estudiants.ci_estudiant,' - ',estudiants.name,' ',estudiants.lastname) as estudiant_fullname")
            ->join('incidents', 'estudiants.id', '=', 'incidents.estudiant_id')
            ->where('incidents.profesor_id',$this->profesor_id)
            ->groupBy('estudiants.id');

        $estudiants = ($this->search) ?
            $estudiants->where(
                function($query) use ($search) {
                    $query->orwhere('estudiants.lastname','like', '%'.$search.'%')
                        ->orWhere('estudiants.name','like','%'.$search.'%')
                        ->orWhere('estudiants.ci_estudiant','like','%'.$search.'%')
                        ;
                })
        : $estudiants ;

        $estudiants = $estudiants->get(); //dd($estudiants);

        return view('livewire.movile.profesor.incident.tline-component',['estudiants'=>$estudiants]);
    }

    public function updateIndex()
    {
        $this->modeAddInsident = true;
    }
}
