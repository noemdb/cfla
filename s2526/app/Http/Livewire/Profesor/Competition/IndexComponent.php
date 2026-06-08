<?php

namespace App\Http\Livewire\Profesor\Competition;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateQuestion;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{
    public $profesor,$lapso,$question_id,$debate_id,$pensum_id,$pensum;
    public $list_grado,$list_seccion,$list_lapso;
    public $grado_id,$seccion_id,$lapso_id;
    public $fecha;
    public $modeQuestion = false,$modeOption = false;
    public $question, $option;
    public $debates, $questions, $options, $pensums;

    public function mount()
    {
        $this->profesor = Profesor::where('user_id',Auth::user()->id)->first();
        $this->list_grado = Profesor::list_grado($this->profesor->id);
        $this->list_seccion = Seccion::where('grado_id',$this->grado_id)->pluck('name', 'id');
        $this->list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $this->fecha = Carbon::now();
        $this->lapso = Lapso::current();
        $this->modeQuestion = false;
        $this->modeOption = false;
        //$this->pensums = $this->profesor->getPensumsName(); //dd($this->pensums);
    }

    public function render()
    {
        $this->pensums = $this->profesor->getPensumsName();
        return view('livewire.profesor.competition.index-component');
    }

    public function setModeQuestions($pensum_id)
    {
        $this->pensum = Pensum::findOrFail($pensum_id);
        $this->pensum_id = $this->pensum->id;
        $this->questions = DebateQuestion::where('pensum_id',$this->pensum->id)->get();
        $this->modeQuestion = true;
        $this->modeOption = false;
    }

    public function close()
    {
        $this->modeQuestion = false;
        $this->modeOption = false;
        $this->pensum_id = null;
        $this->questions = collect();
    }
}
