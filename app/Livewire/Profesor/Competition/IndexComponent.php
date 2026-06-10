<?php

namespace App\Livewire\Profesor\Competition;

use App\Models\app\Academy\Profesor;
use App\Models\app\Educational\DebateQuestion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{
    public $profesor;
    public $pensums;
    public $selectedPensumId = null;
    public $modeQuestion = false;
    public $questionCounts = [];

    public function mount()
    {
        $user = Auth::user();
        $this->profesor = Profesor::where('user_id', $user->id)->first();

        if ($this->profesor) {
            $this->pensums = $this->profesor->getPensumsName();

            // Pre-calculate question counts per pensum
            $pensumIds = $this->pensums->pluck('id');
            $counts = DebateQuestion::whereIn('pensum_id', $pensumIds)
                ->selectRaw('pensum_id, count(*) as total')
                ->groupBy('pensum_id')
                ->pluck('total', 'pensum_id');
            $this->questionCounts = $counts->toArray();
        } else {
            $this->pensums = collect();
            $this->questionCounts = [];
        }
    }

    public function getQuestionCount($pensumId)
    {
        return $this->questionCounts[$pensumId] ?? 0;
    }

    public function setModeQuestions($pensumId)
    {
        $this->selectedPensumId = $pensumId;
        $this->modeQuestion = true;
    }

    public function closeQuestions()
    {
        $this->selectedPensumId = null;
        $this->modeQuestion = false;
    }

    public function render()
    {
        return view('livewire.profesor.competition.index-component');
    }
}
