<?php

namespace App\Http\Livewire\Evaluacion\Competition;

use App\Models\app\Educational\DebateQuestion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Leader;
use App\Models\app\Pescolar\Pensum;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class IndexComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $search = '',$paginate = 10, $name;

    public $user_id,$category = 'DEBATE';
    public $pestudios,$pestudio_id, $list_pestudio, $grado_id, $list_grado;
    public $list_category;
    public $leader_id;

    public function updatingSearch() { $this->resetPage(); }
    public function updatedPestudioId() { 
        $this->list_grado = Grado::list_pestudio_grado($this->pestudio_id); //dd($this->list_grado);
    }

    function mount()
    {
        $user = User::find(Auth::id());
        $this->user_id = $user->id;
        $this->leader_id = $user->id;

        $this->pestudios = Leader::getPestudioForLeader($this->leader_id);
        $this->list_pestudio = $this->pestudios->pluck('name','id');
        $this->list_grado = collect();
        // $this->list_category = DebateQuestion::CATEGORY;
        $this->list_category = DebateQuestion::getListCategory();
    }

    public function render()
    {
        $pensums = Pensum::select('pensums.*')
        ->selectRaw("CONCAT(grados.name, ' - ', asignaturas.name) as asignatura_name")
        ->join('grados', 'grados.id', '=', 'pensums.grado_id')
        ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
        ->where('grados.id',$this->grado_id)
        ->groupby('pensums.id')
        ->paginate($this->paginate);

        return view('livewire.evaluacion.competition.index-component',[
            'pensums' => $pensums
        ]);
    }
}
