<?php

namespace App\Http\Livewire\General\Poll;

use App\Http\Controllers\General\Email\PollController;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Poll\PollAnswer;
use App\Models\app\Poll\PollMain;
use App\Models\app\Poll\PollQuestion;
use App\Models\app\Poll\PollOption;
use App\Models\app\Poll\PollToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{
    protected $rules = [
        'question_id' => 'required',
        'option_id' => 'required',
        'status_solvent' => 'accepted',
    ];
    protected $validationAttributes = [
        'question_id' => 'Pregunta',
        'option_id' => 'Opción',
        'status_solvent' => 'Solvencia Administrativa',
    ];

    protected $messages = [
        'status_solvent.accepted' => 'El participante asociado a este ticket, no está solvente administrativamente.',
    ];

    public $poll_group_id,$name,$description,$observations;
    public $poll_main_id,$token;
    public $poll_answer;
    public $poll_main,$poll_token,$poll_questions,$poll_answers,$representant,$estudiants,$list_comment_poll_answer;
    public $poll_question_id,$poll_options,$poll_option;
    public $stored,$status_ready,$status_active;
    public $list_question,$list_options;
    public $question_id,$option_id;
    public $competiror;
    public $status_solvent;

    protected $listeners = ['showSwal','updateIndex'];

    public function updateIndex($token)
    {
        $poll_token =PollToken::where('token',$token)->first();
        if (empty($poll_token)) abort(403, 'Acción no autorizada');
    }

    public function updatedQuestionId($value)
    {
        $this->resetValidation();
        if ($value) {
            $question = PollQuestion::findOrFail($value);
            $this->list_options = $question->list_options;
            $this->poll_option = null;
        } else {
            $this->list_options = null;
            $this->poll_option = null;
        }
        $this->emit('updateAnswers', $this->token);
    }

    public function updatedOptionId($value)
    {
        $this->resetValidation();
        if ($value) {
            $option = PollOption::findOrFail($value);
            $this->poll_option = $option;
        } else {
            $this->poll_option = null;
        }
    }

    public function mount($token)
    {
        $poll_token =PollToken::where('token',$token)->first();

        if (empty($poll_token)) abort(403, 'Acción no autorizada');

        $this->token = ($poll_token) ? $token : null;

        $poll_main = ($poll_token) ? PollMain::find($poll_token->poll_main_id) : null ;
        $status_active = $poll_main->status_active;

        $representant = ($poll_token) ? $poll_token->representant : null ;
        $estudiants = ($representant) ? $representant->estudiants : null ;

        $this->poll_token = $poll_token;
        $this->poll_main = $poll_main;
        $this->status_active = $status_active;

        $this->list_comment_poll_answer = PollAnswer::COLUMN_COMMENTS;
        $this->status_solvent = false;

        $this->emit('QuestionsFocus');
    }

    public function render()
    {
        $this->poll_questions = $this->poll_main->poll_questions; //dd($this->poll_questions);

        $this->list_question = PollQuestion::list_question_enable_token($this->token);

        $this->poll_answers = PollAnswer::where('token',$this->token)->get();

        $this->status_ready = $this->list_question->isEmpty();

        return view('livewire.general.poll.index-component');
    }

    public function save()
    {
        $poll_token =PollToken::where('token',$this->token)->first(); //dd($poll_token);
        $user = ($poll_token) ? $poll_token->user : null ; //dd($representant);
        $exchange_ammount_expire_bill = 0;

        if ($user->isRepresentant()) {
            $representant = $user->representant; //dd($representant);
            $exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill,2); //dd($exchange_ammount_expire_bill);
        }

        $this->status_solvent = ($exchange_ammount_expire_bill > 0) ? false : true ; //dd($poll_token,$user,$this->status_solvent);

        $this->validate();

        $poll_answer = PollAnswer::select('poll_answers.*')
            ->where('poll_question_id',$this->question_id)
            ->where('token',$this->token)
            ->where('poll_option_id',$this->option_id)
            ->first();

        if ( is_null($poll_answer) ) {
            $arr = [
                'poll_question_id'  => $this->question_id,
                'poll_option_id'    => $this->option_id,
                'token'             => $this->token
            ];
            $this->poll_answer = PollAnswer::create($arr);

            if ($this->poll_answer) {

                $this->stored = true;
                $this->list_options = null;
                $this->poll_option = null;
                $this->question_id = null;
                $this->option_id = null;

                $this->list_question = PollQuestion::list_question_enable_token($this->token);

                if ( $this->list_question->isEmpty() ) {
                    $title = '¡Excelente, buen trabajo! ';
                    $html = 'Operación realizada exitosamente, se enviará un ticket de participación a su correo electrónico';
                    $icon = 'success';
                    $this->showSwal($title,$html,$icon);
                    // $jobSend = new PollController(); //dd($jobSend);
                    // $dataEmail = $jobSend->messegesSend($this->token);
                }
                else {
                    session()->flash('operp_ok', 'Operación realizada exitosamente, continua avanzando, aún tienes pregunstas por responder!!!.');
                }
                $this->emit('updateAnswers', $this->token);
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
            'toast'=>false,
            'position'=>'center',
            'type' => 'warning',
        ]);
    }

}
