<?php

namespace App\Http\Livewire\Administracion\Educational;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateQuestion;
use App\Models\app\Pescolar\Pensum;
use Livewire\Component;
use Livewire\WithFileUploads;

class QuestionComponent extends Component
{
    use WithFileUploads;
    use QuestionTrait;

    public DebateQuestion $question;
    public $question_id,$options,$pensum_id;
    public $mode;
    public $list_comment,$list_category,$list_pensum;
    public $debate,$debate_id,$attachment,$grado;

    protected $listeners = [
        'showSwal','alertConfirm','alertQuestion','question_delete'
    ];

    public function mount($debate_id)
    {
        $this->question_id = null;
        $debate = Debate::find($debate_id); //dd($debate);
        if ($debate) {
            $this->mode = 'index';
            $this->list_comment = DebateQuestion::COLUMN_COMMENTS;
            // $this->list_category = DebateQuestion::CATEGORY;
            $this->list_category = DebateQuestion::getListCategory();
            $this->question = new DebateQuestion;
            $this->debate_id = $debate_id;
            $this->debate = $debate;
            $this->grado = $debate->grado;
            $this->list_pensum = Pensum::list_pensum_grado($this->grado->id); //dd($this->list_pensum);
        }
    }

    public function render()
    {
        $questions = DebateQuestion::where('debate_id',$this->debate_id)->get();
        return view('livewire.administracion.educational.question-component',[
            'questions'=>$questions,
        ]);
    }

    public function save()
    {
        $this->validate();
        $this->question->debate_id = $this->debate_id; 
        $this->upAttachment();
        $this->question->save();
        $this->question_id = $this->question->id;

        $title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente';
		$this->showSwal($title,$html);

        $this->attachment = null;
        $this->question = new DebateQuestion;
        $this->mode = 'index';
    }

    public function create()
    {
        $this->mode = 'create';
        $this->question = new DebateQuestion;
        $this->question_id = null;
    }

    public function edit($id)
    {
        $this->question = DebateQuestion::find($id);
        $this->question_id = $this->question->id;
        $this->mode = 'edit';
    }

    public function setModeOptions($id)
    {
        $question = DebateQuestion::find($id);
        if ($question) {
            $this->question = $question;
            $this->options = $question->options;
            $this->mode = 'options';
            $this->question_id = $id;
        }
    }

    public function close()
    {
        $this->mode = 'index';
        $this->question_id = null;
    }   

    public function upAttachment()
    {
        $this->validate([
            'attachment' => 'nullable|image|max:1024', // 1MB Max
        ]);
        $this->question->attachment = ($this->attachment) ? $this->attachment->store('competitions','educationals') : $this->question->attachment;
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

    public function alertQuestion($id,$method)
    {
        $item = DebateQuestion::findOrFail($id); //dd($mailer);
        $this->dispatchBrowserEvent('swal:question', [
            'type' => 'question',
            'message' => 'Estas seguro de crear la cola de correos automatizados ? ',
            'text' => 'Sí realizas esta operación, no la podrás revertir.',
            'id'=>$item->id,
            'method'=>$method
        ]);
    }

    public function question_delete($id)
    {
        $item = DebateQuestion::findOrFail($id);
        $item->delete();
        $this->question_id = null;

        $title = '¡Excelente, buen trabajo! ';
        $html = 'Operación realizada exitosamente';
        $this->showSwal($title,$html);
    }

    
}
