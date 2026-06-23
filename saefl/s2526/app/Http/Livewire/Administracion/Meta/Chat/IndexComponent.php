<?php

namespace App\Http\Livewire\Administracion\Meta\Chat;

use App\Models\app\Bot\Bmain;
use App\Models\app\Meta\WebhookMessage;
use App\Models\app\Planpago\Cuentaxpagar;
use Livewire\Component;
use Livewire\WithPagination;

class IndexComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $list_comment,$list_contact,$contact,$help_contact;

    public function mount()
    {
        $this->list_comment = WebhookMessage::COLUMN_COMMENTS;
        $this->list_contact = WebhookMessage::listContacts();
    }

    public function render()
    {
        $messeges = WebhookMessage::query()
        ->where('webhook_messages.from','<>','SAEFL')
        ->OrderBy('id','desc');

        $messeges = ($this->contact) ? $messeges->where('from','LIKE','%'.$this->contact.'%') : $messeges ;

        $messeges = $messeges->paginate(10);

        return view('livewire.administracion.meta.chat.index-component',[
            'messeges'=>$messeges,
        ]);
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

    private function getDefaultMenuResponse()
    {
        // Mensaje de menú por defecto
        $menuMessage = "*Gracias por escribirnos.* 😃 \n\n"
            . "Por favor, elige una de las siguientes opciones: \n\n"
            ."------------------------\n"
            . "1️⃣ : *Tasa de cambio BCV del día* 📈 \n"
            ."------------------------\n"
            . "2️⃣ : *Enlace para reportar pagos* 📄 \n"
            // ."------------------------\n"
            // . "3️⃣ : *Enlace para el botón de pago* 💳 \n"
            ."------------------------\n"
            . "#️⃣ : *Número de cédula de identidad* del representante para ver *información administrativa* detallada \n ej: ```14567234``` \n"
            ."------------------------\n"
            . "🇮 seguida del *número de cédula de identidad* del estudiante para obtener una *Constancia de Inscripción Académica* \n ej: ```i31256734``` \n"
            ."------------------------\n"
            . "🇨 seguida del *número de cédula de identidad* del estudiante para obtener una *Constancia de Estudios* \n ej: ```c33967131``` \n"
            // ."------------------------\n"
            // . "🇦 seguida del *número de cédula de identidad* del estudiante para obtener una *Constancia de Inscripción Administrativa* \n ej: ```a33967131``` \n"
            ."------------------------\n"
            . "🇸 seguida del *número de cédula de identidad* del estudiante para obtener una *Solvencia Administrativa* \n ej: ```s33967131``` \n"
            ."------------------------\n"
            . "🇳 seguida del *número de cédula de identidad* del estudiante para obtener un *Informe de notas* \n ej: ```n33967131``` \n"
            ."------------------------\n"
            . "🇲 🇪 🇳 🇺 para ver el menú de opciones \n ej: ```menu``` \n"
            ."------------------------\n"
            // . "🇷 seguida del *número de cédula de identidad* del estudiante para obtener una *Informe de notas de momento actual* \n ej: ```r33967131``` \n"
            // . "*4*: *Horarios de atención* ⏰\n"
            // . "*5*: *Consultas sobre facturación* 💳\n"
            // . "*6*: *Sugerencias y comentarios* 💬\n"
            // . "*7*: *Consulta sobre cuentas* 🏦\n"
            // . "*8*: *Servicios adicionales* 🛠️\n"
            // . "*9*: *Contactar con un agente* 🤖"
        ;
        return $menuMessage;
    }

}
