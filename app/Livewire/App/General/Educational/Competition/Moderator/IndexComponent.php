<?php

namespace App\Livewire\App\General\Educational\Competition\Moderator;
//livewire.app.general.educational.competition.moderator.index-component

use App\Events\OrderStatusUpdated;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;
use Livewire\Attributes\On;

class IndexComponent extends Component
{
    public $token,$competition,$peducativos,$list_grado,$grado_id,$grado,$seccions;

    #[On('update-score')] 
    public function updateQuestionsList()
    {
        $this->mount($this->token);
    }

    public function mount($token)
    {
        $this->token = $token;
        $competition = DebateCompetition::where('token',$token)->first();
        $this->competition = ($competition) ? $competition : null ;
        // $this->list_grado = Grado::list_grado_iu();
        $this->list_grado = Grado::list_grado_iu2();
        $this->peducativos = Peducativo::active()->get(); //dd($peducativos);
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.moderator.index-component');
    }

    public function updatedGradoId($id)
    {
        $this->grado = Grado::findOrFail($id); //dd($grado);
        $this->grado_id = $this->grado->id ;
        $this->seccions = $this->grado->seccions->where('status_inscription_affects','true');
        $this->dispatch('grado-active',id: $this->grado_id);
    }

    public function setGrado($id)
    {
        $this->grado = Grado::findOrFail($id); //dd($this->grado);
        $this->grado_id = $this->grado->id ;
        $this->seccions = $this->grado->seccions->where('status_inscription_affects','true');
        Debate::setDesActiveAll();
        $this->dispatch('grado-active',id: $this->grado_id);
    }

    public function setOnline($id)
    {
        DebateCompetition::setDesActiveAll();
        $this->competition = DebateCompetition::setActive($id); //dd($this->competition);
    }

    public function setOffline($id)
    {
        $this->competition = DebateCompetition::setDesActive($id); //dd($this->competition);
    }
}
