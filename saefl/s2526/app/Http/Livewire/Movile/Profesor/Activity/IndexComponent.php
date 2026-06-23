<?php

namespace App\Http\Livewire\Movile\Profesor\Activity;

use App\Models\app\Pescolar\Lapso;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{
    public $lapso,$profesor,$user,$lessons;
    public $modeCreate,$modeIndex;

    protected $listeners = [ 'setModeDefault','setModeCreate','setModeIndex' ];

    public function mount()
    {
        $user = User::findOrFail(Auth::id());
        $profesor =  $user->profesor;

        $this->user = $user;
        $this->profesor = $profesor;

        $this->lapso = Lapso::current();
    }

    public function render()
    {
        return view('livewire.movile.profesor.activity.index-component');
    }

    public function setModeCreate()
    {
        $this->modeCreate = true;
        $this->modeIndex = false;
    }

    public function setModeIndex()
    {
        $this->modeCreate = false;
        $this->modeIndex = true;
    }

    public function setModeDefault()
    {
        $this->modeCreate = false;
        $this->modeIndex = false;
    }
}
