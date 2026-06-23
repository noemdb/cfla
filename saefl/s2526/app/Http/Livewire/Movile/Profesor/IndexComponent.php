<?php

namespace App\Http\Livewire\Movile\Profesor;

use App\Models\app\Incident\Incident;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{
    public $profesor,$lapsos,$lapso_active,$evaluacion_id,$estudiants,$evaluacion,$pevaluacion;
    public $modeMain,$modeLoad;

    public $incidents,$estudiant_id;

    protected $listeners = ['setModeLoadActive'];

    public function mount()
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $profesor = ($user->IsProfesor()) ? $user->profesor : null;
        $fecha = Carbon::Now();
        $this->modeMain = true;
        $this->modeLoad = false;
        $this->profesor = $profesor;
        $this->incidents = ($profesor) ? $profesor->incidents : collect();

        $this->lapsos = Lapso::all();
        $this->lapso_active = Lapso::current();
        // $this->lapsos = ($this->lapso_active) ? Lapso::where('id',$this->lapso_active->id)->get() : Lapso::all() ;

        // $this->lapso_active = Lapso::first();
    }


    public function render()
    {
        return view('livewire.movile.profesor.index-component');
    }

    public function setModeLoad($evaluacion_id)
    {
        $this->evaluacion = Evaluacion::findOrfail($evaluacion_id); //dd($this->evaluacion);
        $this->pevaluacion = $this->evaluacion->pevaluacion; //dd($this->pevaluacion);
        $this->modeMain = false;
        $this->modeLoad = true;
    }
    public function close()
    {
        $this->modeMain = true;
        $this->modeLoad = false;
    }
}
