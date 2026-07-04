<?php
namespace App\Http\Livewire\Planning\Profesor;

use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Profesor\Activity;
use Livewire\Component;
use Livewire\WithPagination;

class ProfesorTableComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap-4';

    public $search        = '';
    public $peducativo_id = '';
    public $filter_pevaluacions = '';
    public $filter_activities   = '';
    public $sortField     = 'id';
    public $sortDirection = 'asc';

    protected $listeners = ['profesorSaved' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
            $this->sortField     = $field;
        }
    }

    public function render()
    {
        $query = Profesor::active("true")
            ->select('profesors.*')
            ->leftJoin('users', 'profesors.user_id', '=', 'users.id')
            ->withCount('pevaluacions')
            ->addSelect(['activities_count' => Activity::selectRaw('count(*)')
                ->join('pevaluacions', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
                ->whereColumn('pevaluacions.profesor_id', 'profesors.id')
            ]);

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('profesors.name', 'like', '%' . $this->search . '%')
                    ->orWhere('profesors.lastname', 'like', '%' . $this->search . '%')
                    ->orWhere('profesors.ci_profesor', 'like', '%' . $this->search . '%')
                    ->orWhere('users.username', 'like', '%' . $this->search . '%')
                    ->orWhere('profesors.email', 'like', '%' . $this->search . '%');
            });
        }

        if (! empty($this->peducativo_id)) {
            $query->whereHas('pevaluacions', function($q) {
                $q->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
                  ->join('pestudios', 'pensums.pestudio_id', '=', 'pestudios.id')
                  ->where('pestudios.peducativo_id', $this->peducativo_id);
            });
        }

        if ($this->filter_pevaluacions === 'SI') {
            $query->has('pevaluacions');
        } elseif ($this->filter_pevaluacions === 'NO') {
            $query->doesntHave('pevaluacions');
        }

        if ($this->filter_activities === 'SI') {
            $query->having('activities_count', '>', 0);
        } elseif ($this->filter_activities === 'NO') {
            $query->having('activities_count', '=', 0);
        }

        if ($this->sortField === 'username') {
            $query->orderBy('users.username', $this->sortDirection);
        } elseif ($this->sortField === 'pevaluacions_count') {
            $query->orderBy('pevaluacions_count', $this->sortDirection);
        } elseif ($this->sortField === 'activities_count') {
            $query->orderBy('activities_count', $this->sortDirection);
        } elseif ($this->sortField === 'peducativo') {
            $query->orderBy(
                Peducativo::select('peducativos.name')
                    ->join('pestudios', 'peducativos.id', '=', 'pestudios.peducativo_id')
                    ->join('pensums', 'pestudios.id', '=', 'pensums.pestudio_id')
                    ->join('pevaluacions', 'pensums.id', '=', 'pevaluacions.pensum_id')
                    ->whereColumn('pevaluacions.profesor_id', 'profesors.id')
                    ->limit(1),
                $this->sortDirection
            );
        } else {
            $query->orderBy('profesors.' . $this->sortField, $this->sortDirection);
        }

        $profesors    = $query->paginate(10);
        $lapsos       = Lapso::all();
        $lapso_active = Lapso::current();
        $all_peducativos = Peducativo::all();

        return view('livewire.planning.profesor.profesor-table-component', compact('profesors', 'lapsos', 'lapso_active', 'all_peducativos'));
    }
}
