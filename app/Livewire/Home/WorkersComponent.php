<?php

namespace App\Livewire\Home;

use App\Models\app\Academy\Profesor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class WorkersComponent extends Component
{
    public $profesors,$profesor,$modalShow,$workers;

    public function mount()
    {
        $fecha = Carbon::now();
        $this->profesors = Profesor::getProfesorsAcademic(); //dd($this->profesors);
        $this->workers = DB::table('users')
        ->select('users.id','users.username','users.work_id')
        ->selectRaw("CONCAT(profiles.firstname, ' ', profiles.lastname) as fullname")
        ->join('rols', 'users.id', '=', 'rols.user_id')
        ->join('profiles', 'users.id', '=', 'profiles.user_id')
        ->leftjoin('profesors', 'users.id', '=', 'profesors.user_id')
        ->whereNotNull('users.work_id')
        ->whereNull('profesors.id')
        ->where('users.is_active','enable')
        ->whereIn('rols.area',['ADMINISTRATIVO','ADMINISTRACION'])
        ->Where('rols.ffinal','>=',$fecha)
        ->Where('rols.finicial','<=',$fecha)
        ->orderBy('profiles.firstname')
        ->get(); //dd($this->workers);
    }

    public function render()
    {
        return view('livewire.home.workers-component');
    }

    public function showProfesor($id)
    {
        $this->profesor = Profesor::find($id); //dd($this->profesor);
        $this->modalShow = true;
    }
}
