<?php

namespace App\Livewire\App\General\Educational\Competition\Moderator;
//livewire.app.general.educational.competition.moderator.index-component

use App\Events\OrderStatusUpdated;
use App\Models\app\Academy\Grado;
use App\Models\app\Educational\DebateCompetition;
use Livewire\Component;
use Livewire\Attributes\On;

class IndexComponent extends Component
{
    public $token,$competition,$list_grado,$grado_id,$grado,$seccions;

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
        $this->list_grado = Grado::list_grado_iu();
        event(new OrderStatusUpdated($competition));
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
