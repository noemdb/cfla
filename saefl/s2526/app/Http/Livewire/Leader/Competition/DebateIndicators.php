<?php
namespace App\Http\Livewire\Leader\Competition;

use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Leader;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DebateIndicators extends Component
{
    public $competition;
    public $peducativos;
    public $selectedGrado = null;
    public $showDetails   = false;
    public $showStats     = false;
    public $statsGrado    = null;
    public $statsGradoId  = null;
    public $seccions      = null;

    public function mount($competitionId)
    {
        $userId            = Auth::id();
        $this->competition = DebateCompetition::findOrFail($competitionId);

        $leaderGradosIds = Leader::getGradosForLeader($userId)->pluck('id')->toArray();

        $this->peducativos = \App\Models\app\Pescolar\Peducativo::whereHas('pestudios.grados', function ($query) use ($leaderGradosIds, $competitionId) {
            $query->whereIn('grados.id', $leaderGradosIds)
                ->whereHas('debates', function ($q) use ($competitionId) {
                    $q->where('competition_id', $competitionId);
                });
        })
            ->get();

        // Agregamos una propiedad dinámica para filtrar los grados en la vista
        foreach ($this->peducativos as $pe) {
            $pe->authorized_grados = $pe->grados->whereIn('id', $leaderGradosIds);
        }
    }

    public function showGradoDetails($gradoId)
    {
        $userId          = Auth::id();
        $leaderGradosIds = Leader::getGradosForLeader($userId)->pluck('id')->toArray();

        if (! in_array($gradoId, $leaderGradosIds)) {
            return;
        }

        $this->selectedGrado = $gradoId;
        $this->showDetails   = true;
    }

    public function closeDetails()
    {
        $this->showDetails   = false;
        $this->selectedGrado = null;
    }

    public function showGradoStats($gradoId)
    {
        $userId          = Auth::id();
        $leaderGradosIds = Leader::getGradosForLeader($userId)->pluck('id')->toArray();

        if (! in_array($gradoId, $leaderGradosIds)) {
            return;
        }

        $grado              = Grado::findOrFail($gradoId);
        $this->statsGrado   = $grado;
        $this->statsGradoId = $gradoId;
        $this->showStats    = true;
    }

    public function closeStats()
    {
        $this->showStats    = false;
        $this->statsGrado   = null;
        $this->statsGradoId = null;
    }

    public function render()
    {
        return view('livewire.leader.competition.debate-indicators');
    }
}
