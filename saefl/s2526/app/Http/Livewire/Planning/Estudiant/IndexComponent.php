<?php

namespace App\Http\Livewire\Planning\Estudiant;

use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use App\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;


class IndexComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '',$paginate=10; //'name',teacher,cource

    public $modeIndex,$modeSumamaries;
    public $grado_id,$seccion_id;
    public $user_id,$manager_id,$pestudio_id;

    public $list_grado,$list_seccion;
    public $estudiant,$lapsos,$lapso_active;

    public function updatedGradoId($value)
    {
        $this->list_seccion = ($value) ? Seccion::active("true")->where('grado_id',$value)->pluck('name','id') : Array() ;
        $this->resetPage();
    }

    public function mount()
    {       
        $user = User::find(Auth::id());

        $this->close();
        $this->modeIndex = true;

        $this->list_grado = Grado::list_pestudio_grado($this->pestudio_id);
        $this->list_seccion = Array();
        $this->lapsos = Lapso::all();
        $this->lapso_active = Lapso::current();
    }

    public function render()
    {
        $search = $this->search;

        $estudiants = Estudiant::select('estudiants.*')
        ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
        ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
        ->join('grados', 'grados.id', '=', 'seccions.grado_id')
        ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
        ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
        ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
        ->join('peducativos', 'peducativos.id', '=', 'pestudios.peducativo_id')
        ->where('seccions.status_inscription_affects', 'true')
        ->where('planpagos.status_inscription_affects','true')
        ;

        $estudiants = ($this->grado_id) ? $estudiants->where('grados.id',$this->grado_id) : $estudiants ;
        $estudiants = ($this->seccion_id) ? $estudiants->where('seccions.id',$this->seccion_id) : $estudiants ;

        $estudiants = (!empty($search)) ? $estudiants->where(
            function($query) use ($search) {
                $query
                    ->orwhere('estudiants.ci_estudiant','like', '%'.$search.'%')
                    ->orWhere('estudiants.name','like','%'.$search.'%')
                    ->orWhere('estudiants.lastname','like','%'.$search.'%')
                    ;
            })
            : $estudiants; //dd($estudiants->get());

        $estudiants = $estudiants->paginate($this->paginate);

        return view('livewire.planning.estudiant.index-component', [
            'estudiants' => $estudiants,
        ]);
    }

    public function viewSumaries($id)
    {
        $this->estudiant = Estudiant::findOrfail($id);
        $this->modeSumamaries = true;
        $this->modeIndex = false;
    }

    public function close()
    {
        $this->modeIndex = true;
        $this->modeSumamaries = false;
        $this->estudiant = null;
        $this->resetPage();
    }
}
