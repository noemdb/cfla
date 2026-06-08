<?php

namespace App\Http\Livewire\Planning\Competition;

use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Pescolar\Grado;
use Livewire\Component;

class DebateIndicators extends Component
{
    public $competition;
    public $peducativos;
    public $selectedGrado = null;
    public $showDetails = false;
    public $showStats = false;
    public $statsGrado = null;
    public $statsGradoId = null;
    public $seccions = null;

    public function mount($competitionId)
    {
        $this->competition = DebateCompetition::findOrFail($competitionId);
        $this->peducativos = $this->competition->peducativos;
    }

    public function showGradoDetails($gradoId)
    {
        $this->selectedGrado = $gradoId;
        $this->showDetails = true;
    }

    public function closeDetails()
    {
        $this->showDetails = false;
        $this->selectedGrado = null;
    }

    public function showGradoStats($gradoId)
    {
        $grado = Grado::findOrFail($gradoId);
        $this->statsGrado = $grado;
        $this->statsGradoId = $gradoId;
        $this->showStats = true;
    }

    public function closeStats()
    {
        $this->showStats = false;
        $this->statsGrado = null;
        $this->statsGradoId = null;
    }

    public function render()
    {
        return view('livewire.planning.competition.debate-indicators');
    }
}
