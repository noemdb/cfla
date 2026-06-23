<?php

namespace App\Http\Livewire\Administracion\Mailer;

use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\SenderMailer\Mailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateComponent extends Component
{
    public $list_comment,$list_grado,$list_seccion,$list_autoridads,$list_pestudio,$list_template;
    public $name, $code, $description,$pestudio_id,$grado_id, $seccion_id, $autoridad_id, $fecha,$time, $subject, $title;
    public $subtitle, $greeting, $body, $insert, $footer,$atte,$status,$status_test,$status_exclude_last,$status_adviders;
    public $seccions,$mailers,$pestudio,$grado,$seccion,$ci_list;

    public $cuotas,$status_quota=false,$status_email=true,$status_whatsapp=false,$template,$general;

    public $mailer_id,$mailer,$stored;

    public $modeIndex,$modeCreate,$modeEdit,$modePreview,$modeShow,$modeList,$modeDelete;

    public $statusStored;

    public function updatedCuotas($value)
    {
        $representant = Representant::getRepresentantsForCoutas($value); 
        $array = (count($representant)) ? $representant->pluck('ci_representant')->toArray(): Array(); //dd($representant,$arr);
        $this->ci_list = (count($array)) ? implode(',', $array) : null;
        $this->status_quota = true;
    }

    public function mount()
    {
        $this->list_comment = Mailer::COLUMN_COMMENTS;
        $this->modeList=true;
        $this->modeEdit=false;
        $this->modeCreate=false;
        $this->modeDelete=false;
        $this->mailers=Mailer::all();
        $this->list_pestudio = Pestudio::list_pestudio();
        $this->list_grado = Grado::list_pestudio_grado();
        $this->list_seccion = Array();
        $this->list_autoridads = Autoridad::list_autoridads();
        // $this->list_template = ['1'=>'Notificación de Cobro','2'=>'Notificación de Académica','3'=>'Notificación de A. Convivencia']; //notification_agree
        $this->list_template = Mailer::LIST_TEMPLATE;
    }

    public function render()
    {
        return view('livewire.administracion.mailer.create-component');
    }

    public function loadSeccion()
    {
        $this->list_seccion = array();
        if ($this->grado_id) {
            $this->grado = Grado::find($this->grado_id);
            if ($this->grado) {
                $this->seccions = $this->grado->seccions;
                if ($this->seccions->isNotEmpty()) {
                    $this->list_seccion = $this->seccions->pluck('name', 'id');
                }
            }
        }
    }

    public function loadGrado()
    {
        $this->list_grado = Grado::list_pestudio_grado();
        $this->list_seccion = array();
        if (isset($this->pestudio_id)) {
            $pestudio = Pestudio::find($this->pestudio_id);
            if ($pestudio) {
                $this->pestudio = $pestudio;
                $this->list_grado = Grado::where('pestudio_id',$this->pestudio_id)->pluck('name', 'id');
            }
        }
    }

    public function store()
    {
        $this->validatingRequest();

        $this->seccion_id = ($this->grado_id) ? $this->seccion_id : null;
        $arr = [
            'user_id'           => Auth::user()->id,
            'name'              => $this->name,
            'code'              => $this->code,
            'description'       => $this->description,
            'autoridad_id'       => $this->autoridad_id,
            'pestudio_id'       => $this->pestudio_id,
            'grado_id'          => $this->grado_id,
            'seccion_id'        => $this->seccion_id,
            'ci_list'           => $this->ci_list,
            'fecha'             => $this->fecha,
            'time'              => $this->time,
            'subject'           => $this->subject,
            'title'             => $this->title,
            'subtitle'          => $this->subtitle,
            'greeting'          => $this->greeting,
            'body'              => $this->body,
            'insert'            => $this->insert,
            'footer'            => $this->footer,
            'atte'              => $this->atte,
            'status'            => $this->status,
            'status_test'       => $this->status_test,
            'status_adviders'   => $this->status_adviders,
            'status_quota'      => $this->status_quota,
            'status_email'      => $this->status_email,
            'status_whatsapp'   => $this->status_whatsapp,
            'template'          => $this->template,
            'general'           => $this->general,
        ];  
        $this->mailer = Mailer::create($arr);
        $this->stored = true;
        $this->modeCreate = false;

        $title = '¡Excelente, buen trabajo! ';
        $html = 'Operación realizada exitosamente';
        $this->emitUp('showSwal',$title,$html);

        $mailer_id = $this->mailer->id;
        $this->resetInput();
        $this->emitUp('reRenderIndex',$mailer_id);
    }

    public function validatingRequest()
    {
        $fecha = Carbon::now()->subDay()->format('d-m-Y');
        $this->validate([
                'name'     => 'required',
                'autoridad_id'     => 'required',
                'code'  => 'required|max:10|unique:mailers',
                'fecha'  => 'required|date|after:'.$fecha,
                'subject'  => 'required',
                'title'  => 'required',
                'greeting'  => 'required',
                'body'  => 'required',
                'atte'  => 'required',
                'status'  => 'required',
                'status_adviders'  => 'required',
                'status_test'  => 'required',
                'status_exclude_last'  => 'required', 
                'template'  => 'nullable|string', 
                'general'  => 'nullable|string', 
            ],
            [
                'name.required'     => 'El campo nombre es requerido',
            ],
            [
                'name'              => $this->list_comment['name'],
                'code'              => $this->list_comment['code'],
                'description'       => $this->list_comment['description'],
                'autoridad_id'       => $this->list_comment['autoridad_id'],
                'fecha'             => $this->list_comment['fecha'],
                'subject'           => $this->list_comment['subject'],
                'title'             => $this->list_comment['title'],
                'subtitle'          => $this->list_comment['subtitle'],
                'greeting'          => $this->list_comment['greeting'],
                'body'              => $this->list_comment['body'],
                'insert'            => $this->list_comment['insert'],
                'footer'            => $this->list_comment['footer'],
                'atte'            => $this->list_comment['atte'],
                'status'            => $this->list_comment['status'],
                'status_adviders'   => $this->list_comment['status_adviders'],
                'status_test'   => $this->list_comment['status_test'],
                'template'   => $this->list_comment['template'],
                'general'   => $this->list_comment['general'],
                $this->list_comment['status_exclude_last'], 
            ]
        );
    }

    private function resetInput()
    {
        $this->name = null;
        $this->code = null;
        $this->description = null;
        $this->pestudio_id = null;
        $this->seccion_id = null;
        $this->grado_id = null;
        $this->fecha = null;
        $this->time = null;
        $this->subject = null;
        $this->title = null;
        $this->subtitle = null;
        $this->greeting = null;
        $this->body = null;
        $this->insert = null;
        $this->footer = null;
        $this->atte = null;
        $this->status = null;
        $this->status_adviders = null;
        $this->list_seccion = Array();
        $this->cuotas = null;
        $this->status_quota = null;
        $this->status_email = null;
        $this->status_whatsapp = null;
        $this->template = null;
        $this->status_exclude_last = null;
        $this->general = null;
    }

    public function deleteConfirm()
    {
        $this->emit('swal', 'are u sure?', 'warning');
    }

    public function closeCreateMode()
    {
        $this->emitUp('closeCreateMode'); //dd('emit');
    }

}
