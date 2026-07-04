<?php

namespace App\Http\Livewire\Administracion\Poll;

use App\Http\Controllers\Administracion\Email\PollController;
use App\Http\Controllers\General\Email\PollController as PollGeneralController;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Poll\PollMain;
use App\Models\app\Poll\PollToken;
use App\Models\app\Poll\PollAnswer;
use App\Models\app\Poll\PollGroup;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;
use Livewire\Component;
use Livewire\WithFileUploads;

class IndexComponent extends Component
{
    use WithFileUploads;
    use PollMainTrait;

    protected $listeners = [
        'showSwal','alertConfirm','alertQuestion','remove','EenerateToken','EmailForQueuing',
        'edit','preview','show',
    ];

    public PollMain $poll_main;

    public $modeIndex,$modeEdit,$modeCreate,$modePreview,$modePreviewSend,$modeShow,$modeResult;
    public $image,$grados,$pestudios;
    public $poll_questions,$poll_options,$poll_token;
    public $poll_main_id;
    public $poll_group_list;
    public $list_comment,$list_autoridads,$toDate,$institucion,$director,$representants,$representant;
    public $attendees,$attendee;
    public $list_status;

    public function save()
    {
        $this->validate();

        $this->uploadImage();

        $this->poll_main->save();
        $this->poll_main_id = $this->poll_main->id;
        $this->modeReset();
        $this->modeIndex=true;

		$title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente';
		$this->showSwal($title,$html);
    }

    public function uploadImage()
    {
        $this->validate([
            'image' => 'nullable|image|max:4096', // 1MB Max
        ]);
        $this->poll_main->image = ($this->image) ? $this->image->store('images','polls') : $this->poll_main->image;
    }

    public function modeReset()
    {
        $this->modeIndex=false;
        $this->modeEdit=false;
        $this->modePreview=false;
        $this->modePreviewSend=false;
        $this->modeShow=false;
        $this->modeCreate=false;
        $this->modeResult=false;
    }
    public function close()
    {
        $this->mount();
    }

    public function mount()
    {
        $this->poll_main = New PollMain;
        $this->image = null;
        $this->list_comment = PollMain::COLUMN_COMMENTS;
        $this->modeReset();
        $this->modeIndex=true;

        $this->toDate = Date::now()->format('d F Y');
        $this->institucion = Institucion::OrderBy('created_at','DESC')->first();
        $this->director = Autoridad::getTipoAuthority('2');//director
        $this->list_autoridads = Autoridad::list_autoridads();
        $this->poll_group_list = PollGroup::poll_group_list();
        $this->list_status = ['true'=>'SI','false'=>'NO'];
    }

    public function render()
    {
        $polls=PollMain::select('poll_mains.*');
        $user = User::findOrFail(Auth::id());
        $polls = ($user->IsAdmin()) ? $polls : $polls->where('user_id',$user->id) ;
        $polls = $polls->get(); // dd($polls);
        return view('livewire.administracion.poll.index-component',[
            'polls'=>$polls
        ]);
    }

    public function create()
    {
        $this->modeReset();
        $this->modeCreate=true;
        $this->poll_main = New PollMain;
        $this->poll_main->user_id = Auth::id();
    }

    public function edit($id)
    {
        $poll_main = PollMain::findOrFail($id); $attendees = $poll_main->attendees; //dd($attendees->skip(100)->take(50)->toArray());
        $this->poll_main = $poll_main;
        $this->poll_main_id = $poll_main->id;

        $this->modeReset();
        $this->modeEdit = true;
    }

    public function preview($id)
    {
        $poll_main = PollMain::findOrFail($id);
        $this->poll_questions = $poll_main->poll_questions;

        $this->poll_main = $poll_main;
        $this->poll_main_id = $poll_main->id;

        $attendees = $poll_main->attendees; //dd($this->attendee);
        $this->attendee = ($attendees->isNotEmpty()) ? $attendees->random() : null; //dd($this->representant);
        $this->poll_token = $this->attendee->getPollTokenId($poll_main->id);

        $this->modeReset();
        $this->modePreview = true;
    }

    public function previewSend($id)
    {
        $poll_main = PollMain::findOrFail($id);
        $this->poll_questions = $poll_main->poll_questions;
        // $poll_tokens = $poll_main->poll_tokens;
        // $this->poll_token = ($poll_tokens->isNotEmpty()) ? $poll_tokens->first() : null;

        $this->poll_main = $poll_main;
        $this->poll_main_id = $poll_main->id;

        $attendees = $poll_main->attendees; //dd($this->attendee);
        $this->attendee = ($attendees->isNotEmpty()) ? $attendees->random() : null; //dd($this->representant);

        $this->poll_token = New PollToken; //'poll_main_id','user_id','token','email','status_notifiled'
        $this->poll_token->poll_main_id=$poll_main->id;
        $this->poll_token->user_id=$this->attendee->id;
        $this->poll_token->token=substr(str_replace(['+', '/', '=', '&'], '', password_hash(bin2hex(random_bytes(45)), PASSWORD_BCRYPT)), 0, 32);
        // $this->poll_token->token='qpwoei0198234019ipojqpweoi01928301982poeqwepouiq';
        $this->poll_token->email='saefl.test@saefl.com';
        $this->poll_token->status_notifiled='true';

        $this->modeReset();
        $this->modePreviewSend = true;
    }

    /* --------------------------------------------------------- */

    public function results($id)
    {
        $poll_main = PollMain::findOrFail($id);
        $poll_questions = $poll_main->poll_questions;

        $this->poll_main = $poll_main;
        $this->poll_questions = $poll_questions;
        //$this->grados = $poll_main->grados; //dd($poll_main->grados);
        $this->pestudios = $poll_main->pestudios; //dd($poll_main->pestudios);

        $this->modeIndex=false;
        $this->modeResult = true;
        $this->modeShow = false;
        $this->modeEdit = false;
        $this->modeCreate=false;
        $this->modePreview = false;
    }

    public function show($id)
    {
        $poll_main = PollMain::findOrFail($id); 
        $this->poll_main_id = $poll_main->id;
        $attendees = $poll_main->attendees;

        $this->poll_main = $poll_main;
        $this->attendees = $attendees;

        $this->modeIndex=false;
        $this->modeResult = false;
        $this->modeShow = true;
        $this->modeEdit = false;
        $this->modeCreate=false;
        $this->modePreview = false;
    }

    public function showModeIndex()
    {
        $this->modeIndex=true;
        $this->modeResult = false;
        $this->modeShow = false;
        $this->modeEdit = false;
        $this->modeCreate=false;
        $this->modePreview = false;
    }

    /* --------------------------------------------------------- */

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

    public function alertConfirm($id)
    {
        $poll = PollMain::findOrFail($id); //dd($poll);
        $this->dispatchBrowserEvent('swal:confirm', [
            'type' => 'question',
            'message' => 'Estas seguro? ',
            'text' => 'Sí realizas esta operación, no la podrás revertir',
            'id'=>$poll->id
        ]);
    }

    public function alertQuestion($id,$method)
    {
        $poll = PollMain::findOrFail($id); //dd($poll);
        $this->dispatchBrowserEvent('swal:question', [
            'type' => 'question',
            'message' => 'Estas seguro de crear la cola de correos automatizados ? ',
            'text' => 'Sí realizas esta operación, no la podrás revertir.',
            'id'=>$poll->id,
            'method'=>$method
        ]);
    }

    public function EmailForQueuing($id)
    {
        //dd($id);
        $poll_main = PollMain::findOrFail($id); //dd($poll_main);
        $mails_send = collect();
        if ($poll_main) {
            $jobSend = new PollController;
            $mails_send = $jobSend->notifySend($poll_main); //dd($mails_send);
            $title = "Buen trabajo! la cola de correos automatizados fue creada";
            $html = 'Operación realizada exitosamente.<hr><div><b>'.$mails_send->count() .' correos en cola.</b></div>';
            $icon = 'success';
            $poll_main->update(['status_notifiled'=>'true']);
        } else {
            $title = "No fue creada la cola de correos automatizados.";
            $html = 'Operación <b>NO</b> exitosa.';
            $icon = 'error';
        }

        $this->showSwal($title,$html,$icon);
    }

    public function GenerateToken($id)
    {
        $poll_main = PollMain::findOrFail($id);
        if ($poll_main ) {
            $tokens = $poll_main->generate_token; //dd($tokens);

            $title = "Buen trabajo! Se han generado los tokens de participación";
            $html = 'Operación realizada exitosamente.<hr><div><b>'.$tokens->count() .' tokens.</b></div>';
            $icon = 'success';
        } else {
            $title = "No fue creada la cola de correos automatizados.";
            $html = 'Operación <b>NO</b> exitosa, revise la fecha y el estado.';
            $icon = 'error';
        }

        $this->showSwal($title,$html,$icon);
    }

    public function EmailForQueuingIndividual($id,$user_id)
    {
        $poll_main = PollMain::findOrFail($id); //dd($poll_main);
        $this->attendees = $poll_main->attendees;
        $mails_send = collect();
        if ($poll_main) {
            $jobSend = new PollController; //dd($jobSend);
            $mails_send = $jobSend->sendIndividual($poll_main,$user_id);
            $title = "Buen trabajo! la cola de correos automatizados fue creada";
            $html = 'Operación realizada exitosamente.<hr><div><b>'.$mails_send->count() .' correo(s) enviado(s).</b></div>';
            $icon = 'success';
        } else {
            $title = "No fue creada la cola de correos automatizados.";
            $html = 'Operación <b>NO</b> exitosa, revise la fecha y el estado.';
            $icon = 'error';
        }
        $this->showSwal($title,$html,$icon);
    }

    public function EmailForTicketIndividual($id,$user_id)
    {
        $poll_main = PollMain::findOrFail($id); //dd($poll_main); // Undefined variable: mailer
        $this->attendees = $poll_main->attendees;
        $attendee = ($this->attendees->count()) ? $this->attendees->where('id',$user_id)->first() : null;
        if ($attendee) {
            $poll_token = PollToken::where('poll_main_id',$poll_main->id)->where('user_id',$attendee->id)->first(); //dd($poll_token);
            if ($poll_token ) {
                $poll_answer = PollAnswer::where('token',$poll_token->token)->first(); //dd($poll_answer);
                if ($poll_answer) {
                    $jobSend = new PollGeneralController();
                    $dataEmail = $jobSend->messegesSend($poll_token->token);
                    $title = '¡Excelente, buen trabajo! ';
                    $html = 'Operación realizada exitosamente, se enviará un ticket de participación al correo electrónico respectivo';
                    $icon = 'success';
                    $this->showSwal($title,$html,$icon);
                }
            }
        }
    }

}
