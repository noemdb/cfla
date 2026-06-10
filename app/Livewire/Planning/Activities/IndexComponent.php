<?php

namespace App\Livewire\Planning\Activities;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\app\Academy\Asignatura;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WithPagination, WireUiActions;

    public $activity, $activity_id, $pevaluacion_id, $objetivo, $comments, $status;
    public $previewActivity;
    public $pevaluacion, $observations, $pestudios;
    public $lapso_current;

    // Modal modes
    public $modeIndex = true;
    public $modeObservation = false;
    public $modeComments = false;
    public $modePreview = false;

    // Filters
    public $pestudio_id, $grado_id, $seccion_id, $lapso_id, $profesor_id;
    public $status_activities, $search, $paginate = 10;

    // Select lists
    public $list_pestudio, $list_grado, $list_seccion, $list_lapso;
    public $list_profesors, $list_pensum, $list_comment;

    // Tab data (lapsos collection for the Alpine.js tab navigation)
    public $tabsLapsos;

    // Leader context
    public $leader_id;

    public function mount()
    {
        $user = User::findOrFail(Auth::id());
        $this->leader_id = $user->id;

        $this->close();
        $this->modeIndex = true;

        // Cargar listas para filtros
        $this->pestudios = Pestudio::whereHas('grados.pensums.pevaluacions')
            ->where('planning_module', true)
            ->where('status_active', 'true')
            ->orderBy('order')
            ->get();
        $this->list_pestudio = $this->pestudios->pluck('name', 'id');
        $this->list_grado = Grado::active('true')->pluck('name', 'id');
        $this->list_seccion = collect();
        $this->list_pensum = collect();
        $this->setProfesorLists();

        $this->list_lapso = Lapso::select('name', 'id')->orderBy('name')->pluck('name', 'id');
        $this->tabsLapsos = Lapso::orderBy('name')->orderBy('id')->get();
        $this->list_comment = Pevaluacion::COLUMN_COMMENTS;

        // Lapso actual por defecto
        $this->lapso_current = Lapso::current();
        $this->lapso_id = $this->lapso_current->id ?? null;

        $this->paginate = 10;
    }

    public function render()
    {
        $filters = array_filter([
            'pestudio_id' => $this->pestudio_id,
            'grado_id' => $this->grado_id,
            'seccion_id' => $this->seccion_id,
            'lapso_id' => $this->lapso_id,
            'profesor_id' => $this->profesor_id,
            'status_activities' => $this->status_activities,
        ], fn($v) => $v !== null && $v !== '');

        $pevaluacions = $this->getPevaluaciones($filters);

        // Compute the active tab index from the current lapso_id
        $activeTabIndex = 1;
        if ($this->tabsLapsos && $this->lapso_id) {
            $found = $this->tabsLapsos->search(fn($lapso) => $lapso->id == $this->lapso_id);
            if ($found !== false) {
                $activeTabIndex = $found + 1;
            }
        }

        return view('livewire.planning.activities.index-component', [
            'pevaluacions' => $pevaluacions,
            'activeTabIndex' => $activeTabIndex,
        ]);
    }

    // ─── FILTERS CASCADE ────────────────────────────────────────

    public function updatedPestudioId($value)
    {
        $this->resetPage();
        if ($value) {
            $this->list_grado = Grado::where('pestudio_id', $value)
                ->where('status_active', 'true')
                ->pluck('name', 'id');
            $this->list_profesors = Profesor::list_profesors_pestudio($value);
        } else {
            $this->list_grado = Grado::active('true')->pluck('name', 'id');
        }
        $this->grado_id = null;
        $this->seccion_id = null;
        $this->list_seccion = collect();
    }

    public function updatedGradoId($value)
    {
        $this->resetPage();
        if ($value) {
            $this->list_seccion = Seccion::list_seccion_grado($value);
            $this->list_pensum = Pensum::where('grado_id', $value)
                ->whereHas('pevaluacions')
                ->with('asignatura')
                ->get()
                ->mapWithKeys(fn($p) => [$p->id => '[' . ($p->asignatura->code ?? '') . '] ' . ($p->asignatura->name ?? '')]);
        } else {
            $this->list_seccion = collect();
            $this->list_pensum = collect();
        }
        $this->seccion_id = null;
    }

    public function updatedSeccionId($value) { $this->resetPage(); }

    public function updatedLapsoId($value) { $this->resetPage(); }

    public function selectLapso($id)
    {
        $this->lapso_id = $id;
        $this->resetPage();
    }

    public function updatedProfesorId($value) { $this->resetPage(); }

    public function updatedStatusActivities($value) { $this->resetPage(); }

    public function updatedPaginate($value) { $this->resetPage(); }

    public function updatingSearch() { $this->resetPage(); }

    // ─── DATA ───────────────────────────────────────────────────

    protected function getPevaluaciones(array $filters)
    {
        $query = Pevaluacion::with([
            'pensum.asignatura',
            'seccion.grado',
            'profesor',
            'lapso',
            'activities',
        ])
        ->withCount('activities')
        ->whereHas('pensum.pestudio', fn($q) => $q->where('planning_module', true))
        ->whereNull('pevaluacions.deleted_at');

        if (isset($filters['pestudio_id'])) {
            $query->whereHas('pensum.pestudio', fn($q) => $q->where('id', $filters['pestudio_id']));
        }
        if (isset($filters['grado_id'])) {
            $query->whereHas('seccion.grado', fn($q) => $q->where('id', $filters['grado_id']));
        }
        if (isset($filters['seccion_id'])) {
            $query->where('seccion_id', $filters['seccion_id']);
        }
        if (isset($filters['lapso_id'])) {
            $query->where('lapso_id', $filters['lapso_id']);
        }
        if (isset($filters['profesor_id'])) {
            $query->where('profesor_id', $filters['profesor_id']);
        }
        if (isset($filters['status_activities'])) {
            if ($filters['status_activities'] === 'SI') {
                $query->having('activities_count', '>', 0);
            } elseif ($filters['status_activities'] === 'NO') {
                $query->having('activities_count', '=', 0);
            }
        }

        $query->orderBy('created_at', 'desc');

        if ((int) $this->paginate === 9999) {
            $all = $query->get();
            return new \Illuminate\Pagination\LengthAwarePaginator(
                $all,
                $all->count(),
                max($all->count(), 1),
                1,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
        }

        return $query->paginate($this->paginate);
    }

    public function setProfesorLists($value = null)
    {
        $profesors = Profesor::select('profesors.id')
            ->selectRaw("CONCAT(profesors.lastname,' ',profesors.name) as profesor_fullname")
            ->where('profesors.status_active', true)
            ->orderBy('profesors.lastname');

        if ($value) {
            $profesors->where(function ($q) use ($value) {
                $q->where('profesors.name', 'like', "%{$value}%")
                  ->orWhere('profesors.lastname', 'like', "%{$value}%");
            });
        }

        $this->list_profesors = $profesors->pluck('profesor_fullname', 'id');
    }

    // ─── MODAL OBSERVATIONS ─────────────────────────────────────

    public function createObservation($id)
    {
        $this->pevaluacion = Pevaluacion::findOrFail($id);
        $this->pevaluacion_id = $id;
        $this->observations = $this->pevaluacion->observations;
        $this->close();
        $this->modeObservation = true;
    }

    public function saveObservation()
    {
        $this->pevaluacion->observations = $this->observations;
        $this->pevaluacion->save();
        $this->pevaluacion = null;
        $this->pevaluacion_id = null;
        $this->close();
        $this->modeIndex = true;

        $this->notification()->success(
            title: 'Observación Guardada',
            description: 'Las observaciones del plan de evaluación se actualizaron correctamente.'
        );
    }

    // ─── MODAL COMMENTS ─────────────────────────────────────────

    public function setModeComment($activitie_id)
    {
        $this->activity = Activity::findOrFail($activitie_id);
        $this->activity_id = $this->activity->id;
        $this->comments = $this->activity->comments;
        $this->status = $this->activity->status;
        $this->close();
        $this->modeComments = true;
    }

    public function saveComent()
    {
        $this->validate([
            'comments' => 'nullable|string|max:65535',
            'status' => 'required|boolean',
        ]);

        $this->activity->comments = $this->comments;
        $this->activity->status = $this->status;
        $this->activity->save();
        $this->activity = null;
        $this->activity_id = null;
        $this->close();
        $this->modeIndex = true;

        $this->notification()->success(
            title: 'Comentario Guardado',
            description: 'El comentario y estado de la actividad se actualizaron correctamente.'
        );
    }

    // ─── MODAL PREVIEW ─────────────────────────────────────────

    public function showPreview($activitie_id)
    {
        $this->previewActivity = Activity::with('achievements')->findOrFail($activitie_id);
        $this->close();
        $this->modePreview = true;
    }

    // ─── MODE MANAGEMENT ────────────────────────────────────────

    public function close()
    {
        $this->modeIndex = false;
        $this->modeObservation = false;
        $this->modeComments = false;
        $this->modePreview = false;
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
