<?php

namespace App\Http\Livewire\Profesor\Competition;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateQuestion;
use App\Models\app\Pescolar\Pensum;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class QuestionComponent extends Component
{
    use QuestionTrait;
    public DebateQuestion $question;

    public $pensum_id,$questions,$question_id,$category;
    public $list_comment,$list_pensum,$list_category,$list_debates;
    public $mode;
    public $options,$attachment,$grado;

    public function mount($pensum_id)
    {
        $pensum = Pensum::findOrFail($pensum_id);
        $this->pensum_id = $pensum->id;        
        $this->list_comment = DebateQuestion::COLUMN_COMMENTS;
        // $this->list_category = DebateQuestion::CATEGORY;
        $this->list_category = DebateQuestion::getListCategory();
        $this->grado = $pensum->grado;
        $this->list_pensum = Pensum::list_pensum_grado($this->grado->id); //dd($this->list_pensum);
        $this->list_debates = Debate::list_debates($this->grado->id); //dd($this->list_debates);
        $this->mode = 'index';
    }


    public function render()
    {
        $questions = DebateQuestion::where('pensum_id',$this->pensum_id);

        $questions = ($this->category) ? $questions->where('category',$this->category) : $questions ;

        $this->questions = $questions->get();

        return view('livewire.profesor.competition.question-component');
    }

    public function save()
    {
        $this->question->pensum_id = $this->pensum_id;
        $this->question->user_id = Auth::user()->id;
        $this->validate();
        $this->question->save();
        $this->question_id = $this->question->id;

        $title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente';
		$this->showSwal($title,$html);

        $this->attachment = null;
        $this->question = new DebateQuestion;
        $this->mode = 'index';
        $this->category = null;
    }

    public function create()
    {
        $this->mode = 'create';
        $this->question = new DebateQuestion;
        $this->question_id = null;
        $this->category = null;
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
        $this->category = null;
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
