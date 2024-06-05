<?php

namespace App\Livewire\App\General\Educational\Competition\Dashboard;

use App\Models\app\Academy\Grado;
use App\Models\app\Educational\DebateCompetition;
use Livewire\Component;

class IndexComponent extends Component
{
    public $competition,$list_grado,$grado_id;
    
    public function mount($token)
    {
        $competition = DebateCompetition::where('token',$token)->first();
        $this->competition = ($competition) ? $competition : null ;
        $this->list_grado = Grado::list_grado();
        $this->list_grado = Grado::list_grado_iu(); //dd($this->list_grado);
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.dashboard.index-component');
    }

    public function updatedGradoId($id)
    {
        $grado = Grado::find($id); //dd($grado);
        $grado_id = ($grado) ? $grado->id : null ;
        $this->dispatch('grado-active',id: $grado_id);
    }
}
