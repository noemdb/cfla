<?php

namespace App\Http\Livewire\Administracion\Mailer;

use Livewire\Component;

use App\Models\app\Pescolar\Grado;
use App\Models\app\SenderMailer\Mailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DeleteComponent extends Component
{
    public $mailers;
    public $mailer_id,$mailer;
    public $list_comment,$list_grado,$list_seccion;
    public $name, $code, $description, $seccion_id, $grado_id,$date, $fecha, $time,$subject, $title, $subtitle, $greeting, $body, $insert, $footer,  $status, $status_adviders;

    protected $listeners = ['remove'];

    public function mount(Mailer $mailer)
    {
        $this->loadMailer($mailer); //dd($this->mailer);
        $this->list_comment = Mailer::COLUMN_COMMENTS;
        $this->list_grado = Grado::list_grado();
        $this->loadSeccion();        
    }

    public function render()
    {
        return view('livewire.administracion.mailer.delete-component');
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

    public function loadMailer($mailer)
    {
        $this->mailer = $mailer;        
        $this->mailer_id = $mailer->id;
        $this->name = $mailer->name;
        $this->code = $mailer->code;
        $this->description = $mailer->description;
        $this->number_id = $mailer->number_id;

        $this->grado_id = $mailer->grado_id;        
        $this->seccion_id = $mailer->seccion_id;
        // $this->date = $mailer->date;
        $this->fecha = $mailer->fecha;
        $this->time = $mailer->time;

        $this->subject = $mailer->subject;
        $this->title = $mailer->title;
        $this->subtitle = $mailer->subtitle;
        $this->greeting = $mailer->greeting;
        $this->body = $mailer->body;
        $this->insert = $mailer->insert;
        $this->footer = $mailer->footer;
        $this->status = $mailer->status;
        $this->status_adviders = $mailer->status_adviders;

    }

    public function closeDeleteMode()
    {
        $this->emitUp('closeDeleteMode');
    }

    public function destroy($id)
    {
        $mailer = Mailer::find($id);
        if ($mailer) {
            $mailer->delete();
            $this->modeDelete=false;
            $this->emitUp('reRenderIndex');
        }
    }


    public function deleteConfirm($id)
    {
        $mailer = Mailer::findOrFail($id);
        $this->emit('swal', 'are u sure?', 'warning');
    } 
    
    public function delete()
    {
       if($this->mailer) {
         $this->mailer->delete();
         $this->mailer = null;
       }
    }

    public function alertSuccess()
    {
        $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'success',  
                'message' => 'User Created Successfully!', 
                'text' => 'It will list on users table soon.'
            ]);
    }

    // public function alertConfirm()
    public function alertConfirm($id)
    {
        $mailer = Mailer::findOrFail($id);
        $this->dispatchBrowserEvent('swal:confirm', [
            'type' => 'warning',  
            'message' => 'Are you sure?', 
            'text' => 'If deleted, you will not be able to recover this imaginary file!',
            'id'=>$id
        ]);
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove($id)
    {
        $mailer = Mailer::findOrFail($id);
        $mailer->delete();
        
        // $this->dispatchBrowserEvent('swal:modal', [
        //         'type' => 'success',  
        //         'message' => 'User Delete Successfully!', 
        //         'text' => 'It will not list on users table soon.'
        // ]);
        $this->emitUp('reRenderIndex');
    }
}
