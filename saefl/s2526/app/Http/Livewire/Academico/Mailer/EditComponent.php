<?php

namespace App\Http\Livewire\Academico\Mailer;

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
    public $list_comment,$list_grado,$list_seccion;
    public $name, $code, $description,$pestudio_id,$grado_id, $seccion_id, $fecha,$time, $subject, $title,
            $subtitle, $greeting, $body, $insert, $footer, $atte, $status, $status_adviders;
    public $pestudio,$grado,$seccion;

    // protected $listeners = ['reRenderEdit','edit','preview'];

    // public function mount($mailer_id)
    public function mount(Mailer $mailer)    {
        $this->loadMailer($mailer); //dd($this->mailer);
        $this->list_comment = Mailer::COLUMN_COMMENTS;
        $this->list_pestudio = Pestudio::list_pestudio();
        $this->list_grado = Grado::list_pestudio_grado();
        $this->loadSeccion();
    }

    public function render()
    {
        return view('livewire.academico.mailer.edit-component');
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
            'pestudio_id'       => $this->pestudio_id,
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
            'status_adviders'   => $this->status_adviders,
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
                'name'     => 'required',
                'code'  => 'required|max:10|unique:mailers,code,'.$this->mailer_id,
                'fecha'  => 'required|date|after:'.$fecha,
                'subject'  => 'required',
                'title'  => 'required',
                'greeting'  => 'required',
                'body'  => 'required',
                'status'  => 'required',
                'status_adviders'  => 'required',
            ],
            [
                'name.required'     => 'El campo nombre es requerido',
            ],
            [
                'name'              => $this->list_comment['name'],
                'code'              => $this->list_comment['code'],
                'description'       => $this->list_comment['description'],
                'fecha'             => $this->list_comment['fecha'],
                'subject'           => $this->list_comment['subject'],
                'title'             => $this->list_comment['title'],
                'subtitle'          => $this->list_comment['subtitle'],
                'greeting'          => $this->list_comment['greeting'],
                'body'              => $this->list_comment['body'],
                'insert'            => $this->list_comment['insert'],
                'footer'            => $this->list_comment['footer'],
                'atte'              => $this->list_comment['atte'],
                'status'            => $this->list_comment['status'],
                'status_adviders'   => $this->list_comment['status_adviders'],
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

        $this->pestudio_id = $mailer->pestudio_id;
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
        $this->status_adviders = $mailer->status_adviders;
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
        $this->status_adviders = null;
        $this->list_seccion = Array();
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
