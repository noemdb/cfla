<?php

namespace App\Http\Livewire\Movile\Profesor\Incident;

use App\Models\app\Incident\Incident;
use App\Models\app\Estudiant;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{
    public $profesor,$incidents,$profesor_id;
    public $estudiant_id,$search = '';

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

        if ($search) {
            $this->incidents = Incident::select('incidents.*')
            ->selectRaw("CONCAT(estudiants.ci_estudiant,' - ',estudiants.name,' ',estudiants.lastname) as estudiant_fullname")
            ->join('estudiants', 'estudiants.id', '=', 'incidents.estudiant_id')
            ->where('incidents.profesor_id',$this->profesor_id)
            ->where(
                function($query) use ($search) {
                    $query->orwhere('estudiants.lastname','like', '%'.$search.'%')
                        ->orWhere('estudiants.name','like','%'.$search.'%')
                        ->orWhere('estudiants.ci_estudiant','like','%'.$search.'%')
                        ;
                })

            ->get()
            ;
        } else {
            $this->updateIndex();
        }
        return view('livewire.movile.profesor.incident.index-component');
    }

    public function updateIndex()
    {
        $this->incidents = ($this->profesor) ? $this->profesor->incidents : collect();
    }
}
