<?php

namespace App\Http\Livewire\Movile\Evaluacion;

use App\Models\app\Learning\Lesson;
use App\Models\app\Pescolar\Lapso;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{
    public $is_lesson,$peducativos,$lapsos,$lapso_active;
    public $pevaluacion_id,$lapso_id;
    public $list_comment;

    public function mount()
    {
        $user = User::find(Auth::id());
        $this->is_lesson = ($user) ? $user->is_lesson : false ;
        $this->peducativos = ($user) ? $user->peducativos : collect() ;
        $this->lapsos = Lapso::all();
        $this->lapso_active = Lapso::current();
        $this->list_comment = Lesson::COLUMN_COMMENTS;
    }
    public function render()
    {      
        return view('livewire.movile.evaluacion.index-component');
    }
}
