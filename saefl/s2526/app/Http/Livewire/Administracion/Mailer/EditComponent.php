<?php

namespace App\Http\Livewire\Administracion\Mailer;

use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion\Autoridad;
use Livewire\Component;

use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\SenderMailer\Mailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditComponent extends Component
{
    public $mailers;
    public $mailer_id,$mailer;
    public $list_comment,$list_grado,$list_seccion,$list_autoridads,$list_pestudio,$list_template;
    public $name, $code, $description,$pestudio_id,$ci_list,$grado_id, $seccion_id, $fecha,$time, $subject, $title,$autoridad_id,$status_exclude_last;
    public $subtitle, $greeting, $body, $insert, $footer,  $atte,$status,$status_test, $status_adviders;
    public $pestudio,$grado,$seccion,$number_id;
    public $cuotas,$status_quota=false,$status_email=true,$status_whatsapp=false,$template,$general;

    public function updatedCuotas($value)
    {
        $representant = Representant::getRepresentantsForCoutas($value); 
        $array = (count($representant)) ? $representant->pluck('ci_representant')->toArray(): Array(); //dd($representant,$arr);
        $this->ci_list = (count($array)) ? implode(',', $array) : null;
        $this->status_quota = true;
    }

    public function mount(Mailer $mailer)
    {
        $this->loadMailer($mailer); //dd($this->mailer);
        $this->list_comment = Mailer::COLUMN_COMMENTS;
        $this->list_pestudio = Pestudio::list_pestudio();
        $this->list_grado = Grado::list_pestudio_grado();
        $this->loadSeccion();
        $this->list_autoridads = Autoridad::list_autoridads();
        $this->list_template = Mailer::LIST_TEMPLATE;
    }

    public function render()
    {
        return view('livewire.administracion.mailer.edit-component');
    }

    public function update()
    {
        $mailer = Mailer::findOrFail($this->mailer_id);

        $this->validatingRequest();

        $this->seccion_id = ($this->grado_id) ? $this->seccion_id : null;

        $arr = [
            'user_id'           => Auth::user()->id,
            'name'              => $this->name,
            'code'              => $this->code,
            'description'       => $this->description,
            'autoridad_id'      => $this->autoridad_id,
            'pestudio_id'       => $this->pestudio_id,
            'ci_list'           => $this->ci_list,
            'grado_id'          => $this->grado_id,
            'seccion_id'        => $this->seccion_id,
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
            'status_exclude_last'   => $this->status_exclude_last,
            'status_quota'      => $this->status_quota,
            'status_email'      => $this->status_email,
            'status_whatsapp'   => $this->status_whatsapp,
            'template'          => $this->template,
            'general'           => $this->general,
        ];

        //dd( $mailer, $arr);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $mailer->update($arr);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        DB::commit();
        $this->resetInput();
        $this->loadMailer($mailer);

        $title = '¡Excelente, buen trabajo! ';
        $html = 'Operación realizada exitosamente';
        $this->emitUp('showSwal',$title,$html);
        $this->emitUp('reRenderIndex',$mailer->id);
    }

    public function validatingRequest()
    {
        $fecha = Carbon::now()->subDay()->format('d-m-Y');
        $this->validate([
                'autoridad_id'     => 'required',
                'name'     => 'required',
                'code'  => 'required|max:10|unique:mailers,code,'.$this->mailer_id,
                'fecha'  => 'required|date|after:'.$fecha,
                'subject'  => 'required',
                'title'  => 'required',
                'greeting'  => 'required',
                'body'  => 'required',
                'atte'  => 'required',
                'status'  => 'required',
                'status_adviders'  => 'required',
                'status_test'     => 'required',
                'status_exclude_last'     => 'required',
                'template'     => 'nullable|string',
                'general'     => 'nullable|string',
            ],
            [
                'name.required'     => 'El campo nombre es requerido',
            ],
            [
                'name'              => $this->list_comment['name'],
                'code'              => $this->list_comment['code'],
                'description'       => $this->list_comment['description'],
                'autoridad_id'      => $this->list_comment['autoridad_id'],
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
                'status_test'       => $this->list_comment['status_test'],
                'status_exclude_last'       => $this->list_comment['status_exclude_last'],
                'template'          => $this->list_comment['template'],
                'general'           => $this->list_comment['general'],
            ]
        );
    }

    public function loadMailer($mailer)
    {
        $this->mailer = $mailer;
        $this->mailer_id = $mailer->id;
        $this->name = $mailer->name;
        $this->code = $mailer->code;
        $this->description = $mailer->description;
        $this->number_id = $mailer->number_id;

        $this->autoridad_id = $mailer->autoridad_id;
        $this->pestudio_id = $mailer->pestudio_id;
        $this->ci_list = $mailer->ci_list;
        $this->grado_id = $mailer->grado_id;
        $this->seccion_id = $mailer->seccion_id;
        $this->fecha = $mailer->fecha;
        $this->time = $mailer->time;

        $this->subject = $mailer->subject;
        $this->title = $mailer->title;
        $this->subtitle = $mailer->subtitle;
        $this->greeting = $mailer->greeting;
        $this->body = $mailer->body;
        $this->insert = $mailer->insert;
        $this->footer = $mailer->footer;
        $this->atte = $mailer->atte;
        $this->status = $mailer->status;
        $this->status_test = $mailer->status_test;
        $this->status_adviders = $mailer->status_adviders;
        $this->status_exclude_last = $mailer->status_exclude_last;
        $this->status_quota = $mailer->status_quota;
        $this->status_whatsapp = $mailer->status_whatsapp;
        $this->status_email = $mailer->status_email;
        $this->template = $mailer->template;
        $this->general = $mailer->general;
    }

    public function closeEditMode()
    {
        $this->emitUp('closeEditMode');
    }

    private function resetInput()
    {
        $this->name = null;
        $this->code = null;
        $this->description = null;
        $this->seccion_id = null;
        $this->pestudio_id = null;
        $this->ci_list = null;
        $this->grado_id = null;
        $this->fecha = null;
        $this->subject = null;
        $this->title = null;
        $this->subtitle = null;
        $this->greeting = null;
        $this->body = null;
        $this->insert = null;
        $this->footer = null;
        $this->atte = null;
        $this->status = null;
        $this->status_exclude_last = null;
        $this->status_adviders = null;
        $this->list_seccion = Array();
        $this->cuotas = null;
        $this->status_quota = null;
        $this->status_email = null;
        $this->status_whatsapp = null;
        $this->template = null;
        $this->general = null;
    }

    public function loadSeccion()
    {
        $this->list_seccion = array();
        if ($this->grado_id) {
            $grado = Grado::find($this->grado_id);
            if ($grado) {
                $this->grado = $grado;
                $this->list_seccion = Seccion::where('grado_id',$this->grado_id)->pluck('name', 'id');
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

    public function edit($id)
    {
        $this->emitUp('edit',$id);
    }

    public function preview($id)
    {
        $this->emitUp('preview',$id);
    }

    public function show($id)
    {
        $this->emitUp('show',$id);
    }

}
