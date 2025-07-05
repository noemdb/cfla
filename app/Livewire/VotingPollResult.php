<?php

namespace App\Livewire;

use App\Models\VotingPoll;
use App\Models\VotingVote;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class VotingPollResult extends Component
{
    public $poll;
    public $access_token;
    public $totalVotes = 0;
    public $results = [];
    public $lastUpdated;
    public $showTitle = true;

    public function mount($access_token, $showTitle = true)
    {
        $this->access_token = $access_token;
        $this->showTitle = $showTitle;

        $this->poll = VotingPoll::where('access_token', $access_token)
            ->where('enable', true)
            ->with(['options'])
            ->first();

        if (!$this->poll) {
            abort(404, 'Encuesta no encontrada o no está activa');
        }

        $this->loadResults();
    }

    public function loadResults()
    {
        // Obtener el total de votos para esta encuesta
        $this->totalVotes = VotingVote::whereHas('option', function ($query) {
            $query->where('poll_id', $this->poll->id);
        })->count();

        // Obtener resultados por opción
        $this->results = $this->poll->options->map(function ($option) {
            $votes = VotingVote::where('option_id', $option->id)->count();
            $percentage = $this->totalVotes > 0 ? round(($votes / $this->totalVotes) * 100, 1) : 0;

            return [
                'id' => $option->id,
                'label' => $option->label,
                'votes' => $votes,
                'percentage' => $percentage,
                'color' => $this->getColorForOption($option->id)
            ];
        })->sortByDesc('votes')->values()->toArray();

        $this->lastUpdated = now()->format('H:i:s');
    }

    private function getColorForOption($optionId)
    {
        $colors = [
            'from-emerald-500 to-green-500',
            'from-green-500 to-emerald-600',
            'from-teal-500 to-green-600',
            'from-emerald-600 to-green-700',
            'from-green-600 to-teal-600',
            'from-teal-600 to-emerald-700'
        ];

        return $colors[$optionId % count($colors)];
    }

    public function getMaxVotes()
    {
        return collect($this->results)->max('votes') ?: 1;
    }

    public function render()
    {
        return view('livewire.voting-poll-result');
    }
}
