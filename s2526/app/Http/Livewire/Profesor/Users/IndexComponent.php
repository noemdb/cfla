<?php

namespace App\Http\Livewire\Profesor\Users;

use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{
    use UserTrait;

    public $list_comment;

    public User $user;

    public function mount()
    {
        $this->list_comment = User::COLUMN_COMMENTS; //dd($this->list_comment);
    }

    public function render()
    {
        $id = Auth::id();
        $user = User::findOrFail($id); //dd($user);
        $this->user = $user; //dd($user,$this->user);
        // $representant = $this->user->representant;
        // $this->updateEmailAll($representant->email);
        return view('livewire.representant.users.index-component');
    }

    public function save()
    {
        $this->validate();
        $this->user->save();
        $this->updateEmailUserToProfesor();

        session()->flash('operp_ok', 'Guardado!!!.');

        $title = '¡Excelente, buen trabajo! ';
		$html = 'Operación realizada exitosamente';
		$this->showSwal($title,$html);
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

    public function updateEmailUserToProfesor()
    {
        if ($this->user) {
            if ($this->user->IsProfesor()) {
                $profesor = $this->user->profesor;
                if ($profesor) {
                    $profesor->email = $this->user->email;
                    $profesor->save();
                }
            }
        }
    }

}
