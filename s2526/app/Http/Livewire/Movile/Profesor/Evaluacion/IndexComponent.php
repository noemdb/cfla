<?php

namespace App\Http\Livewire\Movile\Profesor\Evaluacion;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{
    public $user_id;
    public $profesor,$lapsos,$lapso_active,$evaluacion_id,$estudiants,$pevaluacion,$pevaluacions,$evaluacions;
    public $modeMain,$modeLoad;
    public $estudiant,$estudiant_id,$list_estudiants;
    public $index;
    public $pevaluacion_list_nota;
    public Evaluacion $evaluacion;
    public $nota;

    public function mount($evaluacions)
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $fecha = Carbon::Now();
        $this->index = 0;

        if ($evaluacions->isNotEmpty()) {
            $this->evaluacions = $evaluacions;
            $evaluacion = $evaluacions->first();
            $pevaluacion = $evaluacion->pevaluacion;
            $this->list_estudiants = $pevaluacion->estudiants->sortBy('ci_estudiant')->pluck('id');
            $this->pevaluacion_list_nota = $pevaluacion->pevaluacion_list_nota(); //dd($this->list_nota);
        }        

        $this->modeMain = true;
        $this->modeLoad = false;
        $this->profesor = $user->profesor;
        $this->lapsos = Lapso::all();
        $this->lapso_active = Lapso::WhereDate('finicial','<=', $fecha)->WhereDate('ffinal','>=', $fecha)->first();
        // $this->lapso_active = Lapso::first();

    }

    public function render()
    {
        return view('livewire.movile.profesor.evaluacion.index-component');
    }

    public function setModeLoad($evaluacion_id)
    {
        $this->evaluacion = Evaluacion::findOrfail($evaluacion_id); //dd($this->evaluacion);

        $this->lapso_active = $this->evaluacion->lapso;

        $this->evaluacion_id = $evaluacion_id;

        $this->estudiant_id = (count($this->list_estudiants)) ? $this->list_estudiants[0] : null; //dd($this->estudiant_id,$this->list_estudiants);

        $this->estudiant = Estudiant::find($this->estudiant_id);

        $this->nota = $this->estudiant->getNotaEvaluacion($this->evaluacion->id);

        $this->modeMain = false;
        $this->modeLoad = true;
        $this->index = 0;
    }

    public function close()
    {
        $this->modeMain = true;
        $this->modeLoad = false;
    }

    public function next()
    {
        $this->index = $this->index + 1;
        if ($this->index >= count($this->list_estudiants)) {
            $this->index = 0;
        }
        $this->setEstudiant();
    }

    public function previus()
    {
        $this->index = $this->index - 1;
        if ($this->index <= 0) {
            $this->index = 0;
        }
        $this->setEstudiant();
    }

    public function home()
    {
        $this->index = 0;
        $this->setEstudiant();        
    }

    public function last()
    {
        $count = count($this->list_estudiants);
        $this->index = ($count) ? ($count - 1) : 0;
        $this->setEstudiant();
    }

    public function setEstudiant()
    {
        $this->estudiant_id = $this->list_estudiants[$this->index];
        $this->estudiant = Estudiant::find($this->estudiant_id);
        $this->nota = $this->estudiant->getNotaEvaluacion($this->evaluacion_id);
    }

    public function updatedNota($value)
    {
        $boletin = Boletin::where('estudiant_id',$this->estudiant_id)->where('evaluacion_id',$this->evaluacion_id)->first(); //dd($boletin,$this->estudiant_id,$this->evaluacion_id);

        $arr = [
            'nota'=>$value,
            'estudiant_id'=>$this->estudiant->id,
            'evaluacion_id'=>$this->evaluacion->id,
        ];

        if ($boletin) {
            $boletin->update($arr);
        } else {
            $boleti = Boletin::create($arr);
        }
        session()->flash('operp_ok', 'Guardado!!!.'.' ['.$value.']');
        // session()->flash('operp_ok', 'Guardado!!!.', array('timeout' => 10000));

    }
}
