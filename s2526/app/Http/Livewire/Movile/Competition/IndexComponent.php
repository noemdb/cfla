<?php

namespace App\Http\Livewire\Movile\Competition;

use App\Models\app\Educational\DebateCompetition;
use Carbon\Carbon;
use Livewire\Component;

class IndexComponent extends Component
{
    public $competitionTitle,$competition_id,$debateTitle,$gradoName,$activeQuestion,$status_time_elapsed,$status_answer,$options;
    public $status_active_competition,$status_active_debate,$status_active_question,$date;

    public function mount()
    {
       $this->updateDate();
    }   

    public function render()
    {
        return view('livewire.movile.competition.index-component');
    }

    public function updateDate()
    {
        $this->date = Carbon::now();
        $competition = DebateCompetition::active()->where('id', 1)->first();
        if ($competition) {
            $this->status_active_competition = true;
            $this->status_active_debate = false;
            $this->status_active_question = false;
            $this->status_time_elapsed = false;
            $this->status_answer = false; 
            $this->options = collect();           

            $this->competition_id = $competition->id;
            $this->competitionTitle = $competition->name;
            $debate = $competition->debates()->where('status_active', true)->first();
            if ($debate) {
                $this->debateTitle = $debate->name;
                $this->gradoName = $debate->grado->name;
                $this->status_active_debate = true;
                $question = $debate->getActiveQuestion();
                if ($question) {
                    $this->activeQuestion = $question->text;
                    $this->status_active_question = true;
                    $this->options = $question->options;
                    $this->status_time_elapsed = $question->status_time_elapsed;
                    $this->status_answer = $question->status_answer;
                } else {
                    $this->activeQuestion = 'No hay pregunta activa';
                    $this->status_active_question = false;
                    $this->options = collect();
                    $this->status_time_elapsed = false;
                    $this->status_answer = false;
                }
                
            } else {
                $this->debateTitle = 'No hay debates activos';
                $this->gradoName = null;
                $this->status_active_debate = false;
                $this->status_active_question = false;
                $this->activeQuestion = 'No hay pregunta activa';
                $this->options = collect();
            }
        } else {
            $this->competitionTitle = 'No hay competiciones activas';
            $this->status_active_competition = false;
            $this->competition_id = null;
        }
    }

}
