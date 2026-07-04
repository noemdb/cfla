<?php

namespace App\Http\Livewire\Administracion\Poll\Question;

use Livewire\Component;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;

use App\Models\app\Poll\PollMain;
use App\Models\app\Poll\PollQuestion;

class IndexComponent extends Component
{
	use PollQuestionTrait;

	protected $listeners = [
        'showSwal','alertConfirm','alertQuestion','remove',
        'create','edit','preview','show'
    ];

    public PollQuestion $poll_question;

	public $modeIndex,$modeEdit,$modeCreate,$modePreview,$modeShow;

	public $poll_mains,$poll_main,$poll_questions,$poll_options,$poll_token,$poll_question_id;
	public $poll_id;
	public $list_comment,$list_status;
    public $list_poll_main;

    public function save()
    {
        $this->validate();

        $this->poll_question->save();
        $this->poll_question_id = $this->poll_question->id;
        $this->modeReset();
        $this->modeIndex=true;

		$title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente';
		$this->showSwal($title,$html);
    }

    public function modeReset()
    {
        $this->modeIndex=false;
        $this->modeEdit=false;
        $this->modePreview=false;
        $this->modeShow=false;
        $this->modeCreate=false;
    }
    public function close()
    {
        $this->mount();
    }


    public function mount()
    {
        $this->poll_question = New PollQuestion;
        $this->list_comment = PollQuestion::COLUMN_COMMENTS;
        $this->list_status = ['true'=>'SI','false'=>'NO'];
        $this->list_poll_main = PollMain::list_poll_main_enable();

        $this->modeReset();
        $this->modeIndex=true;
    }

    public function render()
    {
        $questions=PollQuestion::select('poll_questions.*');

        $user = User::findOrFail(Auth::id());

        $questions = ($user->IsAdmin()) ? $questions :
            $questions
            ->join('poll_mains', 'poll_mains.id', '=', 'poll_questions.poll_main_id')
            ->where('poll_mains.user_id',$user->id)
            ;

        $questions = $questions->get(); // dd($polls);

        //$this->list_poll_main = PollMain::list_poll_main_enable();

        return view('livewire.administracion.poll.question.index-component',[
            'questions'=>$questions
        ]);
    }

    public function create()
    {
        $this->modeReset();
        $this->modeCreate=true;
        $this->poll_question = New PollQuestion;
    }

    public function edit($id)
    {
        $poll_question = PollQuestion::findOrFail($id);
        $this->poll_main = $poll_question->poll_main;
        $this->poll_question = $poll_question;
        $this->poll_question_id = $poll_question->id;

        $this->modeReset();
        $this->modeEdit = true;
    }

    public function preview($id)
    {
        $poll_main = PollMain::findOrFail($id);
        $this->poll_questions = $poll_main->poll_questions;
        $poll_tokens = $poll_main->poll_tokens;
        $this->poll_token = ($poll_tokens->isNotEmpty()) ? $poll_tokens->first() : null;
        // $this->poll_token = New PollToken;

        $this->poll_question = $poll_main;
        $this->poll_id = $poll_main->id;

        //$this->representant = $poll_main->representants->random()->first(); //dd($this->representant);

        $this->modeReset();
        $this->modePreview = true;
    }

    public function alertConfirm($id)
    {
        $question = PollQuestion::findOrFail($id); //dd($mailer);
        $this->dispatchBrowserEvent('swal:confirm', [
            'type' => 'question',
            'message' => 'Estas seguro? ',
            'text' => 'Sí realizas esta operación, no la podrás revertir',
            'id'=>$question->id
        ]);
    }

    public function remove($id)
    {
        $question = PollQuestion::findOrFail($id);
        $question->delete();

        $title = '¡Excelente, buen trabajo! ';
        $html = 'Operación realizada exitosamente';
        $this->showSwal($title,$html);
        // $this->render();
    }

    public function showSwal($title,$html,$icon='success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer'=>6000,
            'icon'=>$icon,
            'toast'=>false,
            'position'=>'center',
            'type' => 'warning',
        ]);
    }

}
