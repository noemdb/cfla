<?php

namespace App\Livewire\Home;

use App\Models\app\Blog\Contact;
use Livewire\Component;
use WireUi\Traits\Actions;
use Livewire\Attributes\Validate;

class ContactsComponent extends Component
{
    use Actions;

    // #[Validate('name|required','email|required','message|required')] // 1MB Max

    public $name,$email,$message;    

    public function render()
    {
        return view('livewire.home.contacts-component');
    }

    public function save()
    {

        $data = $this->validate([
            'name' => 'required',
            'email' => 'email|required',
            'message' => 'required',
        ]); 

        $contact = Contact::create($data);
        
        $title = "Datos guardados";
        $description = "Toda la información ha sido guardada éxitosamente!";
        $icon = "success";

        $this->notification()->send([
            'title'       => $title,
            'description' => $description,
            'icon'        => $icon
        ]);

        $this->reset();

    }
}
