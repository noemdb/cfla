<?php

namespace App\Http\Livewire\General\Interview;

use App\Http\Controllers\General\Email\Interrogation\InterviewController;
use App\User;
use App\Models\app\Estudiante\Representant;

use App\Models\app\interrogation\Interview;
use App\Models\app\interrogation\InterviewAnswer;
use App\Models\app\interrogation\InterviewAttendee;
use App\Models\app\interrogation\InterviewQuestion;

use Livewire\Component;
use Livewire\WithFileUploads;

class IndexComponent extends Component
{
    use WithFileUploads;
    public $photo;

    public Interview $interview;
    public InterviewQuestion $question;
    public InterviewAnswer $interview_answer;
    public InterviewAttendee $interview_attendee;

    public $step;
    public $questions, $unansweredQuestions, $answeredQuestions;
    public $representant,$user_id;
    public $ci;
    public $modeStart,$modeAnswer;
    public $statusLoad;
    public $list_comment;

    protected $rules = [
        'ci' => 'required|exists:representants,ci_representant',
        'photo' => 'image|max:1024|nullable',
        'interview_answer.user_id' => 'required',
        'interview_answer.question_id' => 'required',
        'interview_answer.text' => 'required|string',
    ];

    protected function validationAttributes()
    {
        return [
            'ci' => 'Cédula del representnte',
            'interview_answer.user_id' => $this->list_comment['user_id'],
            'interview_answer.question_id' => $this->list_comment['question_id'],
            'interview_answer.text' => $this->list_comment['text'],
            'photo' => 'Imagen del participante',
        ];
    }

    public function messages() 
    {
        return [
            'ci.exists' => 'La cédula ingresada no se encuentra registrada'
        ];
    }

    public function mount()
    {
        $this->interview = new Interview;
        $this->question = new InterviewQuestion;
        $this->interview_answer = new InterviewAnswer;
        $this->interview_attendee = new InterviewAttendee;
        $this->list_comment = InterviewAnswer::COLUMN_COMMENTS;

        $this->step = 0;
        $this->modeStart = true;
        $this->modeAnswer = false;
        $this->ci = null;
        // $this->ci = '14608133';
        $this->user_id = null;
        $this->representant = null;
        $this->statusLoad = true;
    }

    public function render()
    {
        $interview = Interview::where('status',true)->orderBy('created_at','desc')->first(); //dd($interview);
        $this->interview = ($interview) ? $interview : new Interview ; //dd($this->interview->questions);
        $this->questions = ($interview) ? $interview->interview_questions : null ; //dd($this->questions);
        return view('livewire.general.interview.index-component');
    }

    public function loadUser()
    {
        $this->validateOnly('ci');

        $representant = Representant::where('ci_representant',$this->ci)->first();

        if ($representant) {
            $this->statusLoad = true;

            $this->representant = $representant ;
            $this->user_id = ($representant) ? $representant->user_id : null ;

            $this->unansweredQuestions = $this->interview->getUnansweredQuestions($this->user_id); //dd($this->unansweredQuestions);
            $this->question = ($this->unansweredQuestions->isNotEmpty()) ? $this->unansweredQuestions->first() : new InterviewQuestion;      
            $this->answeredQuestions = $this->interview->getAnsweredQuestions($this->user_id);

            $this->modeStart = false;
            $this->modeAnswer = true; 
        }
               
    }

    public function save()
    {
        $photoUrl = null;
        $this->interview_answer->user_id = $this->user_id;
        $this->interview_answer->question_id = $this->question->id;
        $this->validate();
        $this->interview_answer->save();
        $this->step++;

        if ($this->step == 1) { //guarda la participacion
            if ($this->photo) {
                $this->validateOnly('photo');
                $photoUrl = $this->photo->store('images','interviews');            
            }            
            $this->interview_attendee->user_id = $this->user_id;
            $this->interview_attendee->interview_id = $this->interview->id;
            $this->interview_attendee->photo = $photoUrl;
            $this->interview_attendee->save();
        }        
        
        $this->interview_answer = new InterviewAnswer;
        $this->interview_attendee = new InterviewAttendee;
        $this->unansweredQuestions = $this->interview->getUnansweredQuestions($this->user_id);
        $this->question = ($this->unansweredQuestions->isNotEmpty()) ? $this->unansweredQuestions->first() : new InterviewQuestion;        
        $this->answeredQuestions = $this->interview->getAnsweredQuestions($this->user_id);

        if ($this->unansweredQuestions->count()==0) {
            $title = '¡Excelente, buen trabajo! ';
            $html = 'Operación realizada exitosamente.';
            $this->showSwal($title,$html);

            $jobSend = new InterviewController(); //dd($jobSend);
            $dataEmail = $jobSend->messegesSend($this->user_id,$this->interview->id);
        }
    }

    public function goToStart()
    {
        $this->mount();
    }

    public function showSwal($title,$html,$icon='success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer'=>60000,
            'icon'=>$icon,
            'toast'=>false,
            'position'=>'center',
            'icon' => 'success',
            'allowOutsideClick' => false,
        ]);
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:1024',
        ]);
    }
}
