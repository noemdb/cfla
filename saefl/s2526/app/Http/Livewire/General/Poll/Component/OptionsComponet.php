<?php

namespace App\Http\Livewire\General\Poll\Component;

use App\Http\Controllers\General\Email\PollController;

use App\Models\app\Poll\PollAnswer;
use App\Models\app\Poll\PollMain;
use Livewire\Component;

use App\Models\app\Poll\PollQuestion;
use App\Models\app\Poll\PollOption;
use App\Models\app\Poll\PollToken;

class OptionsComponet extends Component
{
	public $poll_token,$token,$poll_question,$question_id;
	public $poll_main,$status_ready;
	public $poll_option,$option_id,$poll_options,$list_options,$poll_answer;
    public $modeSelect,$modeSelected;
    public $status_solvent,$stored;
    public $list_question;

	protected $listeners = ['updateOptions','showSwal','optionsFocus'];

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

	public function mount($token)
	{
        $poll_token =PollToken::where('token',$token)->first();
		$this->poll_token = ($poll_token) ? $poll_token : abort(403, 'Acción no autorizada');

        $poll_main = ($poll_token) ? PollMain::find($poll_token->poll_main_id) : null ;
        $this->poll_main = ($poll_main) ? $poll_main : abort(403, 'Acción no autorizada');

		$this->poll_options = collect();
		$this->list_options = collect();

        $this->modeSelect = true;
        $this->modeSelected = false;
	}

	public function updateOptions($question_id)
	{
        if (! empty($question_id)) {
            $this->poll_question = PollQuestion::findOrFail($question_id);
            $this->question_id = $this->poll_question->id;

            $this->poll_options = $this->poll_question->poll_options;
            $this->list_options = $this->poll_question->list_options;

            $this->poll_option = null;
            $this->modeSelect = true;
            $this->modeSelected = false;
        }
        else {
            $this->poll_question = New PollQuestion;
            $this->question_id = null;
            $this->poll_options = collect();
            $this->list_options = collect();
            $this->modeSelect = false;
            $this->modeSelected = false;
        }

	}

    public function updatedOptionId($value)
    {
        $this->resetValidation();
        if ($value) {
            $option = PollOption::findOrFail($value);
            $this->poll_option = $option;
        } else {
            $this->poll_option = null;
            $this->modeSelect = true;
            $this->modeSelected = false;
        }
    }

    public function render()
    {
        return view('livewire.general.poll.component.options-componet');
    }

    public function setOption($option_id)
    {
        $this->poll_option = PollOption::findOrFail($option_id); //dd($this->poll_option);
        $this->option_id = $this->poll_option->id;

        $this->modeSelect = false;
        $this->modeSelected = true;
        $this->resetValidation();
        $this->emit('QuestionsFocus');
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

        // $this->validate();

        $this->validateOnly("question_id");
        $this->validateOnly("option_id");
        $this->validateOnly("token");

        // dd($this->status_solvent);

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
            ]; //dd();
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
                    $jobSend = new PollController(); //dd($jobSend);
                    $dataEmail = $jobSend->messegesSend($this->token);
                }
                else {
                    session()->flash('operp_ok', 'Operación realizada exitosamente, continua avanzando, aún tienes pregunstas por responder!!!.');
                }

                $this->emit('updateQuestions');
                $this->emit('updateAnswers', $this->token);
                $this->emit('updateIndex', $this->token);

                $this->poll_option = null;
                $this->modeSelect = false;
                $this->modeSelected = false;

                $this->poll_options = collect();
                $this->list_options = collect();
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

    public function close()
    {
        $this->poll_option = null;
        $this->modeSelect = true;
        $this->modeSelected = false;
        $this->emit('QuestionsFocus');
    }

}
