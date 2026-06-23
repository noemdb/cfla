<?php

namespace App\Http\Livewire\General\Poll\Component;

use Livewire\Component;

use App\Models\app\Estudiante\Representant;
use App\Models\app\Poll\PollAnswer;
use App\Models\app\Poll\PollMain;
use App\Models\app\Poll\PollQuestion;
use App\Models\app\Poll\PollOption;
use App\Models\app\Poll\PollToken;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class QuestionsComponet extends Component
{
	public $poll_token,$token,$question_id;
	public $poll_main,$status_ready;
	public $poll_questions,$poll_question_id,$poll_options,$poll_option;
	public $list_question,$list_options;

	protected $listeners = ['updateQuestions','QuestionsFocus' => 'focusOpcionsList'];


	public function mount($token)
	{
		$poll_token =PollToken::where('token',$token)->first();
		$this->poll_token = ($poll_token) ? $poll_token : abort(403, 'Acción no autorizada');

        $poll_main = ($poll_token) ? PollMain::find($poll_token->poll_main_id) : null ;
        $this->poll_main = ($poll_main) ? $poll_main : abort(403, 'Acción no autorizada');

        $this->updateQuestions();
	}

	public function updatedQuestionId($value)
    {
        $this->resetValidation();
        if (! empty($value)) {
            $question = PollQuestion::findOrFail($value);
            $this->emit('updateOptions', $question->id);
            $this->emit('QuestionsFocus');
        } else {
            $this->emit('updateOptions', null);
        }
    }

    public function render()
    {
        return view('livewire.general.poll.component.questions-componet');
    }

    public function updateQuestions()
    {
        $this->poll_questions = $this->poll_main->poll_questions;
		$this->list_question = PollQuestion::list_question_enable_token($this->token);
		$this->status_ready = $this->list_question->isEmpty();
    }

    public function focusOpcionsList()
    {
        //
    }

    // public function updatedQuestionId($value)
    // {
    //     $this->emit('QuestionsFocus');
    // }
}

