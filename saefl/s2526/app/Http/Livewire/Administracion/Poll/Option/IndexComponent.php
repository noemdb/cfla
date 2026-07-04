<?php

namespace App\Http\Livewire\Administracion\Poll\Option;

use Livewire\Component;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;

use App\Models\app\Poll\PollMain;
use App\Models\app\Poll\PollQuestion;
use App\Models\app\Poll\PollOption;
use Livewire\WithFileUploads;

class IndexComponent extends Component
{
    use WithFileUploads;
	use PollOptionTrait;

	protected $listeners = [
        'showSwal','alertConfirm','alertQuestion','remove',
        'create','edit','preview','show'
    ];

    public PollOption $poll_option;

	public $modeIndex,$modeEdit,$modeCreate,$modePreview,$modeShow;
    public $image;
	public $poll_mains,$poll_main,$poll_options,$poll_token,$poll_option_id;
	public $poll_id;
	public $list_comment;
    public $list_poll_question;

    public function save()
    {
        $this->validate();

        $this->uploadImage();

        $this->poll_option->save();
        $this->poll_option_id = $this->poll_option->id;
        $this->modeReset();
        $this->modeIndex=true;

		$title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente';
		$this->showSwal($title,$html);
    }

    public function uploadImage()
    {
        $this->validate([
            'image' => 'nullable|image|max:4096', // 4MB Max
        ]);
        $this->poll_option->image = ($this->image) ? $this->image->store('poll','public') : $this->poll_option->image;
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
        $this->poll_option = New PollOption;
        $this->list_comment = PollOption::COLUMN_COMMENTS;
        //$this->list_poll_question = PollQuestion::list_poll_question_enable(); //dd($this->list_poll_question);
        $this->modeReset();
        $this->modeIndex=true;
    }

    public function render()
    {
        $options=PollOption::select('poll_options.*');
        $user = User::findOrFail(Auth::id()); //dd($user);
        $options = ($user->IsAdmin()) ? $options :
            $options
            ->join('poll_questions', 'poll_questions.id', '=', 'poll_options.poll_question_id')
            ->join('poll_mains', 'poll_mains.id', '=', 'poll_questions.poll_main_id')
            ->where('poll_mains.user_id',$user->id) ;

        $options = $options->get(); //dd($options);

        $this->list_poll_question = PollQuestion::list_poll_question_enable_user($user->id);

        return view('livewire.administracion.poll.option.index-component',[
            'options'=>$options
        ]);
    }

    public function create()
    {
        $this->modeReset();
        $this->modeCreate=true;
        $this->poll_option = New PollOption;
    }

    public function edit($id)
    {
        $poll_option = PollOption::findOrFail($id);
        $this->poll_option = $poll_option;
        $this->poll_main = $poll_option->poll_question;
        $this->poll_main = $poll_option->poll_main;
        $this->poll_option_id = $poll_option->id;

        $this->modeReset();
        $this->modeEdit = true;
    }

    public function preview($id)
    {
        $poll_main = PollMain::findOrFail($id);
        $this->poll_options = $poll_main->poll_options;
        $poll_tokens = $poll_main->poll_tokens;
        $this->poll_token = ($poll_tokens->isNotEmpty()) ? $poll_tokens->first() : null;
        // $this->poll_token = New PollToken;

        $this->poll_option = $poll_main;
        $this->poll_id = $poll_main->id;

        //$this->representant = $poll_main->representants->random()->first(); //dd($this->representant);

        $this->modeReset();
        $this->modePreview = true;
    }

    public function alertConfirm($id)
    {
        $question = PollOption::findOrFail($id); //dd($mailer);
        $this->dispatchBrowserEvent('swal:confirm', [
            'type' => 'question',
            'message' => 'Estas seguro? ',
            'text' => 'Sí realizas esta operación, no la podrás revertir',
            'id'=>$question->id
        ]);
    }

    public function remove($id)
    {
        $option = PollOption::findOrFail($id);
        $option->delete();

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
