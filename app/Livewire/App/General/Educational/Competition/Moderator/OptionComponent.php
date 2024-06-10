<?php

namespace App\Livewire\App\General\Educational\Competition\Moderator;
//livewire.app.general.educational.competition.moderator.option-component

use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Seccion;
use App\Models\app\Educational\DebateAnswer;
use App\Models\app\Educational\DebateOption;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;
use Livewire\Attributes\On;
use WireUi\Traits\Actions;


class OptionComponent extends Component
{
    use Actions;

    public $question,$options,$answer,$active_id,$literal,$colors;
    public $grado,$seccions,$timeRemaining,$timerActive,$pollingInterval,$timeElapsed;

    #[On('question-active')] 
    public function updateOptionList($id)
    {
        $this->question = DebateQuestion::findOrFail($id);
        $this->options = DebateOption::where('question_id',$id)->inRandomOrder()->get();        
        $this->active_id = null;

        //timer
        $this->setGradoSeccions();
        $this->timerActive = false;
        $this->timeRemaining = $this->question->TimeRemaining;

        //answer
        $this->answer = DebateAnswer::where('question_id',$this->question->id)->first();
    }

    public function mount($question_id)
    {
        $this->updateOptionList($question_id);
        $this->literal = ['A','B','C','D','E','F'];     
        $this->colors = ['primary','secondary','positive','negative','warning','info'];  
        
        //timer
        $this->timerActive = false;
        $this->pollingInterval = 1000; //milisecond
    }

    public function render()
    {
        return view('livewire.app.general.educational.competition.moderator.option-component');
    }

    //timer
    public function active($id)
    {
        $question = DebateQuestion::findOrFail($id);
        $this->question = $question;
        $this->active_id = $question->id ;
        $this->setGradoSeccions();
        $this->timeRemaining = $this->question->TimeRemaining;
        // $this->dispatch('question-active',id: $id);
    }
    public function start()
    {
        $this->timerActive = true;
        $this->timeElapsed = $this->question->time_elapsed;
        // $this->dispatch('startTimer', active: $this->timerActive); 
    }
    public function pause()
    {
        $this->timerActive = false;
        $this->question->time_elapsed = $this->timeElapsed;
        $this->question->save();
        // $this->dispatch('startTimer', active: $this->timerActive); 
    }

    public function finished()
    {
        $this->timerActive = false;
        $this->question->time_elapsed = $this->question->time;
        $this->question->save();
    }
    public function decrementCount()
    {
        if ($this->timeRemaining > 0) {
            $this->timeRemaining--;
            $this->timeElapsed++;
            $this->question->time_elapsed = $this->timeElapsed;
            $this->question->save();
        } else {
            $this->stopPolling();
        }
    }
    public function stopPolling()
    {
        $this->timerActive = false;
    }
    public function setGradoSeccions()
    {
        $this->grado = ($this->question) ? $this->question->grado : null ;
        $this->seccions = ($this->question) ? $this->question->seccions->where('status_inscription_affects','true') : null ;
    }

    public function saveAnswer($grado_id,$correct)
    {
        $grado = Grado::findOrFail($grado_id);
        // $seccion = $grado->seccions->first();
        $question = DebateQuestion::findOrFail($this->question->id); //dd($question);
        $option = DebateOption::option_correct($this->question->id);

        $answers = DebateAnswer::where('question_id',$this->question->id)->where('option_id',$option->id)->get();

        if ($answers->isNotEmpty()) {
            foreach ($answers as $answer) {
                $answer->delete();
            }
        }

        $weighting = ($correct) ? $question->weighting : null ;

        $arr = [
            'question_id'=>$question->id,
            'option_id'=>$option->id,
            'grado_id'=>$grado->id,
            // 'seccion_id'=>$seccion->id,
            'status_claim'=>false,
            'score'=>$weighting,
        ]; //dd($arr);

        $this->answer = DebateAnswer::create($arr);

        $question->status_answer = true;
        $question->save();

        $this->notification()->success(
            $title = 'Respuesta registrada',
            $description = 'El puntaje fué adjudicado correctamente!'
        );

        $this->updateOptionList($question->id);

        $this->dispatch('update-score');

    }

    public function setOnline($id)
    {
        $this->question = DebateQuestion::setActive($id);
        $this->active_id = $this->question->id ;
        $this->dispatch('question-online',id: $id);
    }

    public function setOffline($id)
    {
        $this->question = DebateQuestion::setDesActive($id);
        $this->active_id = null;
        $this->dispatch('question-online',id: $id);
    }

}
