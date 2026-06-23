<?php

namespace App\Http\Livewire\Leader\Competition;

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
    public $search = '', $paginate = 10, $name;

    public $user_id, $category = 'DEBATE';
    public $pestudios, $pestudio_id, $list_pestudio, $grado_id, $list_grado;
    public $list_category;
    public $leader_id;

    public function updatedGradoId() { $this->resetPage(); }

    public function updatingSearch() { $this->resetPage(); }
    public function updatedPestudioId() { 
        $this->grado_id = null;
        $this->resetPage();

        $leaderGrados = Leader::getGradosForLeader($this->leader_id)
            ->where('pestudio_id', $this->pestudio_id);

        $datas_grados = collect();
        $pestudio = $this->pestudios->find($this->pestudio_id);
        
        if ($pestudio) {
            $datas_grados->put($pestudio->code . '-' . $pestudio->name, $leaderGrados->pluck('name', 'id'));
        }
        
        $this->list_grado = $datas_grados;
    }

    function mount()
    {
        $user = User::find(Auth::id());
        $this->user_id = $user->id;
        $this->leader_id = $user->id;

        $this->pestudios = Leader::getPestudioForLeader($this->leader_id);
        $this->list_pestudio = $this->pestudios->pluck('name', 'id');
        $this->list_grado = collect();
        $this->list_category = DebateQuestion::getListCategory();
    }

    public function render()
    {
        $pensums = Pensum::select('pensums.*')
            ->selectRaw("CONCAT(grados.name, ' - ', asignaturas.name) as asignatura_name")
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
            ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')
            ->where('area_conocimientos.leader_id', $this->leader_id)
            ->where('grados.id', $this->grado_id)
            ->groupby('pensums.id')
            ->paginate($this->paginate);

        return view('livewire.leader.competition.index-component', [
            'pensums' => $pensums
        ]);
    }
}
