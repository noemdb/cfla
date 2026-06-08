<?php

namespace App\Http\Livewire\General\Educational\Competition\Interactive;

use App\Http\Livewire\Administracion\Educational\DebateComponent;
use App\Models\app\Educational\DebateAnswer;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateGroup;
use App\Models\app\Educational\DebateQuestion;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class IndexComponent extends Component
{
    public DebateCompetition $competition;
    public $token,$countDonw;
    public $debates,$debate,$groups,$questions,$question,$options,$option;
    public $time_elapsed,$counter,$status_running_timer;
    public $profesor_name;

    public function mount($token)
    {
        $this->competition = DebateCompetition::where('token',$this->token)->orderBy('created_at','desc')->first(); //dd($this->competition );
        if (empty($this->competition)) abort(403, 'Competición no encontrado');
        $this->groups =$this->competition->groups;
        $this->token = $token;

        $user = $this->competition->user;
        $profesor = ($user) ? $user->profesor : null;
        $this->profesor_name = ($profesor) ? $profesor->fullname : null;
        
        $this->status_running_timer = false;

        $this->nextDebate();
        $this->nextQuestion();

    }

    public function render()
    {
        return view('livewire.general.educational.competition.interactive.index-component');
    }

    public function nextQuestion()
    {
        $this->questions = ($this->debate) ? $this->debate->questions_unfinished : collect();
        $this->question = ($this->questions->count()) ? $this->questions->shuffle()->first() : null;
        $this->options = ($this->question) ? $this->question->options->shuffle() : collect() ;
        $this->countDonw = ($this->question) ? $this->question->time - $this->question->time_elapsed : null;
        if ($this->questions->count() == 00) $this->nextDebate();
        $this->emitUp('newQuestion'); //dd("123");
    }

    public function nextDebate()
    {
        $this->debates = ($this->competition) ? $this->competition->debates_unfinished : collect();
        $this->debate = ($this->debates->count()) ? $this->debates->shuffle()->first() : null;
    }

    public function startTimer()
    {
        $this->status_running_timer = true;
        $this->runningTimer();
    }

    public function pauseTimer()
    {
        $this->status_running_timer = false;
    }

    public function finishTimer()
    {
        $this->question->time_elapsed = $this->question->time;
        $this->question->save();
        $this->status_running_timer = false;
        $this->countDonw = 0;
    }

    public function runningTimer()
    {
        if ($this->status_running_timer) {
            $this->question->time_elapsed = $this->question->time_elapsed + 1;
            if ($this->question->time == $this->question->time_elapsed) {
                $this->status_running_timer = false;
            }
            $this->question->save();
            $this->countDonw = $this->question->time - $this->question->time_elapsed;
        }
    }

    public function score($group_id)
    {
        //dd($this->competition);
        if ($this->competition && $this->question) {
            $group= DebateGroup::findOrFail($group_id);
            $answer = DebateAnswer::query()
            ->where('group_id',$group->id)
            ->where('question_id',$this->question->id)
            ->where('competition_id',$this->competition->id)
            ->first();

            if (! $answer ) {
                $option = $this->question->option_correct;
                if ($option) {
                    $arr = [
                        'competition_id'=>$this->competition->id,
                        'question_id'=>$this->question->id,
                        'option_id'=>$option->id,
                        'group_id'=>$group->id,
                        'score'=>$this->question->weighting,
                    ];
                    DebateAnswer::create($arr);
                    $title = '¡Excelente, buen trabajo! ';
                    $html = 'Puntuación adjudicada exitosamente';
                    $this->showSwal($title,$html);
                    // $this->nextDebate();
                    // $this->nextQuestion();
                }
                
            }
        }
        
    }

    public function showSwal($title,$html,$icon='success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer'=>6000,
            'icon'=>$icon,
            'toast'=>true,
            'position'=> 'top-end',
        ]);
    }

}
