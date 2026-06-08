<?php

namespace App\Http\Livewire\Administracion\Educational;

use App\Models\app\Educational\DebateOption;
use App\Models\app\Educational\DebateQuestion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class OptionComponent extends Component
{

    use WithFileUploads;
    use OptionTrait;

    public DebateOption $option;
    public $question,$question_id,$option_id,$questions;
    public $mode;
    public $attachment;
    public $list_comment,$list_category;
    //public $debate,$debate_id,$question,$question_id,$attachment;

    protected $listeners = [
        'showSwal','alertConfirm','alertQuestionOption','option_delete'
    ];

    public function mount($question_id)
    {
        $question = DebateQuestion::find($question_id); //dd($debate);
        if ($question) {
            $this->mode = 'index';
            $this->list_comment = DebateOption::COLUMN_COMMENTS;
            $this->option = new DebateOption;
            $this->question_id = $question_id;
            $this->question = $question;
        }
    }

    public function render()
    {
        $options = DebateOption::where('question_id',$this->question_id)->get();
        return view('livewire.administracion.educational.option-component',[
            'options'=>$options
        ]);
    }

    public function save()
    {
        $this->validate();
        $this->option->question_id = $this->question_id; 
        $this->option->user_id = Auth::user()->id;
        $this->upAttachment();
        $this->option->save();
        $this->option_id = $this->option->id;

        $title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente';
		$this->showSwal($title,$html);

        $this->attachment = null;
        $this->option = new DebateOption;
        $this->mode = 'index';
    }

    public function create()
    {
        $this->mode = 'create';
        $this->option = new DebateOption;
    }
    public function edit($id)
    {
        $this->option = DebateOption::find($id);
        $this->mode = 'edit';
    }
    public function close()
    {
        $this->mode = 'index';
    }
    public function upAttachment()
    {
        $this->validate([
            'attachment' => 'nullable|image|max:1024', // 1MB Max
        ]);
        $this->option->attachment = ($this->attachment) ? $this->attachment->store('competitions','educationals') : $this->option->attachment;
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
    
    public function alertQuestionOption($id,$method)
    {
        $item = DebateOption::findOrFail($id); //dd($item);
        $this->dispatchBrowserEvent('swal:question', [
            'type' => 'question',
            'message' => 'Estas seguro de crear la cola de correos automatizados ? ',
            'text' => 'Sí realizas esta operación, no la podrás revertir.',
            'id'=>$item->id,
            'method'=>$method
        ]);
    }

    public function option_delete($id)
    {
        $item = DebateOption::findOrFail($id);
        $item->delete();

        $title = '¡Excelente, buen trabajo! ';
        $html = 'Operación realizada exitosamente';
        $this->showSwal($title,$html);
    }
}
