<?php

namespace App\Http\Livewire\Administracion\Meta\Chat;

use App\Models\app\Meta\WebhookLog;
use App\Models\app\Meta\WebhookMessage;
use Livewire\Component;
use Livewire\WithPagination;

class IndexWSComponent extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $list_comment,$list_contact,$contact,$help_contact;

    public function mount()
    {
        $this->list_comment = WebhookLog::COLUMN_COMMENTS;
        // $this->list_contact = WebhookLog::listContacts();
    }

    public function render()
    {
        $messeges = WebhookLog::query()
        // ->where('webhook_messages.from','<>','SAEFL')
        ->OrderBy('id','desc');

        // $messeges = ($this->contact) ? $messeges->where('from','LIKE','%'.$this->contact.'%') : $messeges ;

        $messeges = $messeges->paginate(10);

        return view('livewire.administracion.meta.chat.index-w-s-component',[
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
}
