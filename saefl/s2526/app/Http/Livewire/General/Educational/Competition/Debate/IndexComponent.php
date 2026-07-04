<?php

namespace App\Http\Livewire\General\Educational\Competition\Debate;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateOption;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;

class IndexComponent extends Component
{
    use DebateTrait;

    public DebateQuestion $question;
    public DebateOption $option;
    public $token,$debate,$questions,$options;
    public $list_comment,$list_comment_option,$list_category,$filter,$weighting;
    public $mode;

    public function updatedFilter($value)
    {        
        $this->loadQuestions();    
    }

    public function updatedWeighting($value)
    {        
        $this->loadQuestions();    
    }

    public function loadQuestions()
    {  
        $this->questions = DebateQuestion::where('debate_id',$this->debate->id)->orderBy('created_at','desc');
        $this->questions = ($this->weighting) ? $this->questions->where('weighting',$this->weighting) : $this->questions ;
        $this->questions = ($this->filter) ? $this->questions->where('category',$this->filter) : $this->questions ;
        $this->questions = $this->questions->get();
    }

    public function updatedQuestionCategory($value)
    {        
        $this->validateCategory($value);     
    }

    public function validateCategory($category)
    {
        $count = DebateQuestion::where('debate_id',$this->debate->id)->where('category',$category)->count();
        $debate = Debate::findOrFail($this->debate->id);

        if ($count >= $debate->question_max) {
            $this->addError('question.category', 'Ya se han registrado todas las preguntas para la categoría seleccionada');
        } else {
            $this->resetErrorBag();
        }  
    }

    public function mount($token)
    {
        $this->token = $token;
        $this->debate = Debate::where('token',$token)->orderBy('created_at','desc')->first(); //dd();
        if (empty($this->debate)) abort(403, 'Debate no encontrado');
        $this->loadQuestions(); 

        $this->list_comment = DebateQuestion::COLUMN_COMMENTS;
        $this->list_comment_option = DebateOption::COLUMN_COMMENTS;
        // $this->list_category = DebateQuestion::CATEGORY; //ksort($this->list_category);
        $this->list_category = DebateQuestion::getListCategory();
        $this->question = new DebateQuestion;
        $this->option = new DebateOption;
        $this->mode = 'question';
        
        $pestudio = $this->debate->pestudio;
        $filteredArray = [];
        foreach ($this->list_category as $key => $value) {
            if (strpos($key, '['.$pestudio->code_oficial.']') !== false) {
                $filteredArray[$key] = $value;
            }
        }
        $this->list_category = $filteredArray;
    }

    public function render()
    {
        return view('livewire.general.educational.competition.debate.index-component');
    }

    public function save()
    {
        $this->validateOnly('question.category');
        $this->validateOnly('question.text');
        $this->validateOnly('question.time');
        $this->validateOnly('question.weighting');
        $this->validateOnly('question.observation');
        $this->validateCategory($this->question->category); 
        $this->question->debate_id = $this->debate->id;  
        $this->question->save();

        $title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente';
		$this->showSwal($title,$html);

        $this->mode = 'option';
        $this->option = new DebateOption;
        // $this->questions = DebateQuestion::where('debate_id',$this->debate->id)->orderBy('created_at','desc')->get();
        $this->loadQuestions(); 
        $this->question = DebateQuestion::find($this->question->id);
    }

    public function saveOption()
    {
        $this->validateOnly('option.text');
        $this->validateOnly('option.observation');
        $this->validateOnly('option.status_option_correct');
        $this->option->question_id = $this->question->id;  
        $this->option->save();

        $title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente';
		$this->showSwal($title,$html);

        $this->mode = 'option';
        $this->option = new DebateOption;
        $this->question = DebateQuestion::find($this->question->id);
        // $this->questions = DebateQuestion::where('debate_id',$this->debate->id)->orderBy('created_at','desc')->get();
        $this->loadQuestions(); 
    }

    public function close()
    {
        $category = $this->question->category;
        $time = $this->question->time;
        $weighting = $this->question->weighting;
        $this->mode = 'question';
        $this->question = new DebateQuestion;
        $this->question->category = $category;
        $this->question->time = $time;
        $this->question->weighting = $weighting;
        $this->option = new DebateOption;
    }

    public function mark($id)
    {
        $option = DebateOption::findOrFail($id);
        $options = DebateOption::where('question_id',$option->question_id)->get();
        foreach ($options as $option) {
            $option->status_option_correct = ($option->id==$id) ? true : false;
            $option->save();
        }
    }

    public function editQuestion($id)
    {
        $this->question = DebateQuestion::findOrFail($id);
        $this->mode = 'editQuestion';
    }

    public function deleteQuestion($id)
    {
        $question = DebateQuestion::findOrFail($id);
        $question->delete();
        // $this->questions = DebateQuestion::where('debate_id',$this->debate->id)->orderBy('created_at','desc')->get();
        $this->loadQuestions(); 
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente';
		$this->showSwal($title,$html);
    }

    public function editOption($id)
    {
        $this->option = DebateOption::findOrFail($id);
        $this->question = DebateQuestion::find($this->option->question_id);
        $this->mode = 'editOption';
    }

    public function deleteOption($id)
    {
        $option = DebateOption::findOrFail($id);
        $option->delete();
        // $this->questions = DebateQuestion::where('debate_id',$this->debate->id)->orderBy('created_at','desc')->get();
        $this->loadQuestions(); 
        $title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente';
		$this->showSwal($title,$html);
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
