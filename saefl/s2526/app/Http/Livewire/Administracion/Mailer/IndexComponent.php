<?php

namespace App\Http\Livewire\Administracion\Mailer;

use Livewire\Component;

use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Pescolar\Grado;
use App\Models\app\SenderMailer\Mailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;

// use App\Http\Controllers\Administracion\Email\MailerController;

use App\Http\Controllers\Administracion\Email\MailerController;

/////////////////////////////////////////////////////////////
use App\Models\app\Estudiante\Enrollment;
use App\User;

class IndexComponent extends Component
{
    // use WithPagination;

    public $mailers;
    public $mailer_id,$mailer;
    public $list_comment,$list_grado,$list_seccion,$list_comment_representant,$list_autoridads;
    public $modeIndex,$modeCreate,$modeEdit,$modePreview,$modeShow;
    public $grado,$seccions,$seccion,$representant,$representants,$director,$autoridad,$institucion,$toDate;

    protected $listeners = [
        'reRenderIndex','closeCreateMode','closeEditMode','closePreviewMode','closeShowMode',
        'showSwal','alertConfirm','alertQuestion','remove','EmailForQueuing',
        'edit','preview','show'
    ];

    public function mount()
    {
        $this->list_comment = Mailer::COLUMN_COMMENTS;
        $this->list_comment_representant = Representant::COLUMN_COMMENTS;
        $this->modeIndex=true;
        $this->modeEdit=false;
        $this->modeCreate=false;
        $this->modePreview=false;
        $this->modeShow=false;
        $this->mailers=Mailer::all();
        $this->list_grado = Grado::list_grado();
        $this->list_seccion = Array();

        $this->toDate = Date::now()->format('d F Y');
        $this->institucion = Institucion::OrderBy('created_at','DESC')->first();
        $this->institucion = Institucion::OrderBy('created_at','DESC')->first();
        $this->director = Autoridad::getTipoAuthority('2');//director
        $this->list_autoridads = Autoridad::list_autoridads();
    }

    public function render()
    {
        $this->mailer = ($this->mailer_id) ? Mailer::find($this->mailer_id) : null ;

        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $mailers = Mailer::all();
        $this->mailers = ( ! $user->IsAdmin() ) ?  $mailers->where('user_id',$user->id) : $mailers;
        return view('livewire.administracion.mailer.index-component');
    }

    public function reRenderIndex($mailer_id = null)
    {
        $this->mount();
        $this->mailer_id = $mailer_id;
        $this->render();
    }

    public function create()
    {
        $this->modeIndex=false;
        $this->modeCreate=true;
        $this->modeShow = false;
        $this->modeEdit = false;
        $this->modePreview = false;
        $this->mailer_id = null;
    }

    public function edit($id)
    {
        $mailer = Mailer::findOrFail($id);
        $this->mailer = $mailer;
        $this->mailer_id = $mailer->id;

        $this->modeIndex=false;
        $this->modeEdit = true;
        $this->modeCreate=false;
        $this->modePreview=false;
        $this->modeShow=false;
    }

    public function preview($id)
    {
        $mailer = Mailer::findOrFail($id);
        $this->mailer = $mailer;
        $this->mailer_id = $mailer->id;
        $this->autoridad = $mailer->autoridad; //dd($this->autoridad);
        $this->representant = $mailer->representant;
        // $this->representant = $mailer->representant_enrollments->random();

        $this->modeIndex=false;
        $this->modePreview = true;
        $this->modeEdit = false;
        $this->modeCreate=false;
        $this->modeShow=false;
    }

    public function show($id)
    {
        $mailer = Mailer::findOrFail($id);
        $this->mailer = $mailer;
        $this->mailer_id = $mailer->id;
        $this->representants = $mailer->representants;
        // $this->representants = $mailer->representant_enrollments;

        $this->modeIndex=false;
        $this->modeShow = true;
        $this->modeEdit = false;
        $this->modeCreate=false;
        $this->modePreview = false;
    }

    public function closeEditMode()
    {
        $this->modeIndex=true;
        $this->modeEdit = false;
    }

    public function closeCreateMode()
    {
        $this->modeIndex=true;
        $this->modeCreate = false;
    }

    public function closePreviewMode()
    {
        $this->modeIndex=true;
        $this->modePreview=false;
    }

    public function closeShowMode()
    {
        $this->modeIndex=true;
        $this->modeShow=false;
    }

    public function remove($id)
    {
        $mailer = Mailer::findOrFail($id);
        $mailer->delete();

        $title = '¡Excelente, buen trabajo! ';
        $html = 'Operación realizada exitosamente';
        $this->showSwal($title,$html);
        $this->reRenderIndex();
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

    public function alertConfirm($id)
    {
        $mailer = Mailer::findOrFail($id); //dd($mailer);
        $this->dispatchBrowserEvent('swal:confirm', [
            'type' => 'question',
            'message' => 'Estas seguro? ',
            'text' => 'Sí realizas esta operación, no la podrás revertir',
            'id'=>$mailer->id
        ]);
    }

    public function alertQuestion($id,$method)
    {
        $mailer = Mailer::findOrFail($id);
        $this->dispatchBrowserEvent('swal:question', [
            'type' => 'question',
            'message' => 'Estas seguro de crear la cola de correos automatizados ? ',
            'text' => 'Sí realizas esta operación, no la podrás revertir.',
            'id'=>$mailer->id,
            'method'=>$method
        ]);
    }

    public function EmailForQueuing($id)
    {
        $mailer = Mailer::findOrFail($id); //dd($mailer);
        if ($mailer->date >= Carbon::now() && $mailer->status >= "true") {
            $jobSend = new MailerController(); //dd($jobSend);
            $mails_send = $jobSend->messegesSend($mailer);
            // $result = $jobSend->messegesSendWithRotation($mailer);
            $title = "Buen trabajo! la cola de correos automatizados fue creada";
            // $html = 'Operación realizada exitosamente.<hr><div><b>'.$result['total_emails'] .' correos en cola.</b></div>';
            $html = 'Operación realizada exitosamente.<hr><div><b>'. count($mails_send) .' correos en cola.</b></div>';
            $icon = 'success';
        } else {
            $title = "No fue creada la cola de correos automatizados.";
            $html = 'Operación <b>NO</b> exitosa, revise la fecha y el estado.';
            $icon = 'error';
        }

        $this->showSwal($title,$html,$icon);
    }

    public function EmailForQueuingWithRotation($id)
    {
        $mailer = Mailer::findOrFail($id);

        if ($mailer->date >= Carbon::now() && $mailer->status >= "true") {
            $jobSend = new MailerController();
            $result = $jobSend->messegesSendWithRotation($mailer);

            if ($result['success']) {
                $title = "¡Excelente! Cola de correos con rotación creada exitosamente";
                $html = 'Operación realizada exitosamente.<hr><div><b>' . $result['total_emails'] . ' correos programados.</b></div>';
                $html .= '<div><small>Servicio utilizado: ' . $result['service'] . '</small></div>';
                $icon = 'success';
            } else {
                $title = "Error al crear la cola de correos";
                $html = 'Error: ' . $result['message'];
                $icon = 'error';
            }
        } else {
            $title = "No fue creada la cola de correos automatizados.";
            $html = 'Operación <b>NO</b> exitosa, revise la fecha y el estado.';
            $icon = 'error';
        }

        $this->showSwal($title, $html, $icon);
    }

    // Agregar también este método para alertas específicas de rotación
    public function alertQuestionRotation($id, $method)
    {
        $mailer = Mailer::findOrFail($id);
        $this->dispatchBrowserEvent('swal:question', [
            'type' => 'question',
            'message' => '¿Estás seguro de crear la cola de correos con rotación automática?',
            'text' => 'Los emails se enviarán usando diferentes proveedores para optimizar la entrega.',
            'id' => $mailer->id,
            'method' => $method
        ]);
    }

}

/*

'user_id','name','code','description','seccion_id','grado_id','fecha','ffinal', 'subject','title','subtitle','greeting','body','footer',  'status','status_adviders',

*/
