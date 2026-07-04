<?php

namespace App\Http\Livewire\Movile\Profesor\Pevaluacion;

use App\Models\app\Pescolar\Lapso;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{
    public $profesor,$lapsos,$lapso_active,$evaluacion_id,$estudiants,$evaluacion,$pevaluacion,$pevaluacions;
    public $modeMain,$modeLoad;

    public function mount($pevaluacions)
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $fecha = Carbon::Now();
        $this->pevaluacions = $pevaluacions;
        $this->modeMain = true;
        $this->modeLoad = false;
        $this->profesor = $user->profesor;
        $this->lapsos = Lapso::all();
        // $this->lapso_active = Lapso::WhereDate('finicial','<=', $fecha)->WhereDate('ffinal','>=', $fecha)->first();
        $this->lapso_active = Lapso::first();
    }

    public function render()
    {
        return view('livewire.movile.profesor.pevaluacion.index-component');
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
