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

class AnswersComponet extends Component
{
	public $poll_token,$poll_main,$poll_questions,$poll_answers;

	protected $listeners = ['updateAnswers'];

	public function mount($token)
	{
		$poll_token =PollToken::where('token',$token)->first(); 
		$this->poll_token = ($poll_token) ? $poll_token : abort(403, 'Acción no autorizada');

        $poll_main = ($poll_token) ? PollMain::find($poll_token->poll_main_id) : null ;
        $this->poll_main = ($poll_main) ? $poll_main : abort(403, 'Acción no autorizada');

        $this->getAnswer();		
	}

	public function updateAnswers($token)
	{
		$this->getAnswer();
	}

	public function getAnswer()
	{
		$token = $this->poll_token->token;
		$this->poll_questions = $this->poll_main->poll_questions;
		$this->poll_answers = $this->poll_main->getPollAnswerByToken($token);
	}

    public function render()
    {
        return view('livewire.general.poll.component.answers-componet');
    }
}
