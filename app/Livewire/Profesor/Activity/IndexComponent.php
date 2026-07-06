<?php

namespace App\Livewire\Profesor\Activity;

use App\Models\app\Academy\Achievement;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Grado;
use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class IndexComponent extends Component
{
    use WireUiActions, WithPagination;

    /** @var Activity */
    public $activity;
    public $achievement;

    public $seccion_id, $activity_id, $pevaluacion, $pevaluacion_id, $achievement_id;
    public $modeCreator, $modeEdit, $modeCreatorAchievement, $modeEditAchievement;
    public $list_comment, $list_seccions;
    public $grado_id, $lapso_id;
    public $enable_edit;
    public $showDetailModal = false;
    public $detailActivity;
    public $showAchievementModal = false;

    // S2526: Actividades de periodo anterior
    public $showS2526Modal = false;
    public $s2526Activities = [];
    public $s2526PendingAchievements = [];
    public $showS2526DetailModal = false;
    public $s2526DetailActivity = [];
    public $s2526DetailAchievements = [];
    public $s2526Search = '';
    public $s2526Lapso = '';
    public $s2526SortField = 'finicial';
    public $s2526SortDir = 'asc';
    public $s2526Lapsos = [];
    public $s2526Page = 1;
    public $s2526PerPage = 15;
    public $s2526Total = 0;
    public $s2526LastPage = 1;
    public $s2526From = 0;

    // Filters & Pagination
    public $search = '';
    public $paginate = 10;

    /** @var ActivityForm Form Object para los campos del formulario de actividad */
    public ActivityForm $activityForm;

    /** @var AchievementForm Form Object para el modal de indicadores */
    public AchievementForm $achievementForm;

    /** @var \Illuminate\Support\Collection */
    public $list_grado, $list_seccion, $list_lapso;

    public function updatedGradoId($value)
    {
        $this->list_seccion = ($this->grado_id)
            ? Seccion::where('grado_id', $this->grado_id)->pluck('name', 'id')
            : collect();
    }

    public function mount($id)
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);

        $this->activity = new Activity();
        $this->achievement = new Achievement();

        $this->pevaluacion = Pevaluacion::with([
            'pensum.asignatura',
            'pensum.grado',
            'pensum.pestudio',
            'seccion',
            'lapso',
            'profesor.user',
            'escala',
        ])->findOrFail($id);

        $this->pevaluacion_id = $id;
        $this->list_comment = array_merge(Activity::COLUMN_COMMENTS, Achievement::COLUMN_COMMENTS);
        $grado = $this->pevaluacion->grado;

        // Descartar la sección actual de la lista de clonación
        $this->list_seccions = Seccion::where('grado_id', $grado?->id)
            ->where('id', '<>', $this->pevaluacion->seccion_id)
            ->pluck('name', 'id');

        // Listas para filtros
        $profesor = Profesor::where('user_id', $user_id)->first();
        $this->list_grado = $profesor
            ? Grado::whereHas('pensums.pevaluacions', fn($q) => $q->where('profesor_id', $profesor->id))
                ->pluck('name', 'id')
            : collect();
        $this->list_seccion = collect();
        $this->list_lapso = Lapso::orderBy('name', 'asc')->pluck('name', 'id');

        // Bloqueo por precierre
        $lapso = $this->pevaluacion->lapso;
        if ($lapso && $lapso->date_preclosing && $lapso->time_preclosing) {
            $preclosingDateTime = Carbon::parse($lapso->date_preclosing->format('Y-m-d') . ' ' . $lapso->time_preclosing);
            $this->enable_edit = now()->lt($preclosingDateTime);
        } else {
            $this->enable_edit = true;
        }
    }

    // ─── FILTER RESET PAGE ───────────────────────────────────────

    public function updatingSearch() { $this->resetPage(); }

    public function updatingPaginate() { $this->resetPage(); }

    public function render()
    {
        $query = Activity::where('pevaluacion_id', $this->pevaluacion_id);

        // Filtro: búsqueda por texto
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('topic', 'like', '%' . $this->search . '%')
                  ->orWhere('thematic', 'like', '%' . $this->search . '%')
                  ->orWhere('references', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('teaching', 'like', '%' . $this->search . '%')
                  ->orWhere('learning', 'like', '%' . $this->search . '%')
                  ->orWhere('observations', 'like', '%' . $this->search . '%');
            });
        }

        $activities = $query->orderBy('finicial')
            ->paginate($this->paginate);

        return view('livewire.profesor.activity.index-component', [
            'activities' => $activities,
        ]);
    }

    // ─── MODOS ─────────────────────────────────────────────

    public function setCreate()
    {
        $this->close();
        $this->resetModel();
        $this->activity_id = null;
        $this->modeCreator = true;
    }

    public function setEditActivity($id)
    {
        $this->close();
        $this->modeEdit = true;
        $this->activity = Activity::findOrFail($id);
        $this->activity_id = $id;
        $this->activityForm->fillFromModel($this->activity);
    }

    public function addAchievement($id)
    {
        $this->close();
        $this->resetModel();
        $this->activity_id = null;
        $this->modeCreatorAchievement = true;
        $this->activity = Activity::findOrFail($id);
        $this->achievementForm->activity_id = $this->activity->id;
        $this->showAchievementModal = true;
    }

    public function setEditAchievement($id)
    {
        $this->close();
        $this->modeCreatorAchievement = true;
        $achievement = Achievement::findOrFail($id);
        $this->achievement_id = $id;
        $this->activity = $achievement->activity;
        $this->activity_id = $this->activity->id;
        // Poblar el Form Object con los datos del modelo
        $this->achievementForm->fillFromModel($achievement);
        $this->showAchievementModal = true;
    }

    // ─── DETALLE (MODAL) ──────────────────────────────────

    public function viewDetail($id)
    {
        $this->detailActivity = Activity::with('achievements')
            ->where('pevaluacion_id', $this->pevaluacion_id)
            ->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailActivity = null;
    }

    // ─── CRUD ──────────────────────────────────────────────

    public function save()
    {
        try {
            $this->activityForm->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Mostrar errores como toast notifications
            foreach ($e->validator->errors()->all() as $msg) {
                $this->notification()->error('Error de validación', $msg);
            }
            // Re-lanzar para que Livewire gestione los errores inline
            throw $e;
        }

        $this->activityForm->pevaluacion_id = $this->pevaluacion->id;
        $this->activityForm->applyToModel($this->activity);

        $this->activity->save();

        // Crear achievements pendientes (copiados desde s2526)
        if (!empty($this->s2526PendingAchievements)) {
            foreach ($this->s2526PendingAchievements as $ach) {
                $ach['activity_id'] = $this->activity->id;
                Achievement::create($ach);
            }
            $this->s2526PendingAchievements = [];
        }

        $this->notification()->success(
            '¡Excelente, buen trabajo!',
            'Registro realizado exitosamente'
        );

        $this->resetModel();
        $this->activity_id = null;
        $this->close();
    }

    public function saveAchievement()
    {
        try {
            $this->achievementForm->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            foreach ($e->validator->errors()->all() as $msg) {
                $this->notification()->error('Error de validación', $msg);
            }
            throw $e;
        }

        if ($this->achievement_id) {
            $achievement = Achievement::findOrFail($this->achievement_id);
        } else {
            $achievement = new Achievement();
        }

        $this->achievementForm->applyToModel($achievement);
        $achievement->save();

        $this->notification()->success(
            '¡Excelente, buen trabajo!',
            'Registro realizado exitosamente'
        );

        $this->resetModel();
        $this->close();
    }

    public function delActivity($id)
    {
        $activity = Activity::findOrFail($id);
        if ($activity) {
            $activity->delete();
            $this->close();
            $this->resetModel();
            $this->activity_id = null;

            $this->notification()->success(
                '¡Excelente, buen trabajo!',
                'Registro eliminado exitosamente'
            );
        }
    }

    public function deleteAchievement($id)
    {
        $achievement = Achievement::findOrFail($id);
        if ($achievement) {
            $achievement->delete();
            $this->close();
            $this->achievement = new Achievement();
            $this->achievement_id = null;
            $this->activity = new Activity();
            $this->activity_id = null;

            $this->notification()->success(
                '¡Excelente, buen trabajo!',
                'El registro fue eliminado exitosamente'
            );
        }
    }

    public function emptyActivities()
    {
        $deletedRows = Activity::where('pevaluacion_id', $this->pevaluacion_id)->delete();
        if ($deletedRows > 0) {
            $this->notification()->success(
                '¡Excelente, buen trabajo!',
                'Los registros fueron eliminados exitosamente'
            );
        } else {
            $this->notification()->error(
                '¡Ocurrieron errores!',
                'NO se eliminó ningún registro'
            );
        }
    }

    // ─── CLONACIÓN ─────────────────────────────────────────

    public function clone()
    {
        $pevaluacion = Pevaluacion::findOrFail($this->pevaluacion_id);
        $seccion = Seccion::find($this->seccion_id);

        if (!$seccion) {
            $this->notification()->error(
                '¡Ocurrieron errores!',
                'Seleccione una sección que contenga actividades'
            );
            return;
        }

        $pevaluacion_new = Pevaluacion::query()
            ->where('profesor_id', $pevaluacion->profesor_id)
            ->where('lapso_id', $pevaluacion->lapso_id)
            ->where('pensum_id', $pevaluacion->pensum_id)
            ->where('seccion_id', $seccion->id);

        $pevaluacion_new = ($pevaluacion->grupo_estable_id)
            ? $pevaluacion_new->where('grupo_estable_id', $pevaluacion->grupo_estable_id)
            : $pevaluacion_new;

        $pevaluacion_new = $pevaluacion_new->first();

        if (!$pevaluacion_new) {
            $this->notification()->error(
                '¡Ocurrieron errores!',
                'NO se registró ninguna operación'
            );
            return;
        }

        $activities = $pevaluacion_new->activities;

        if ($activities->isEmpty()) {
            $this->notification()->error(
                '¡Ocurrieron errores!',
                'NO se registró ninguna operación'
            );
            return;
        }

        foreach ($activities as $activity) {
            $arr = $activity->toArray();
            $arr['pevaluacion_id'] = $pevaluacion->id;
            $arr['comments'] = null;
            $activity_new = Activity::create($arr);

            foreach ($activity->achievements as $achievement) {
                $ach_arr = $achievement->toArray();
                $ach_arr['activity_id'] = $activity_new->id;
                Achievement::create($ach_arr);
            }
        }

        $this->notification()->success(
            '¡Excelente, buen trabajo!',
            'Registro realizado exitosamente'
        );
    }

    // ─── HELPERS ───────────────────────────────────────────

    public function toggleWeighting()
    {
        $this->achievementForm->toggleWeighting();
    }

    public function close()
    {
        $this->modeCreator = false;
        $this->modeEdit = false;
        $this->modeCreatorAchievement = false;
        $this->modeEditAchievement = false;
        $this->showAchievementModal = false;
        $this->activityForm->reset();
        $this->achievementForm->resetForm();
        $this->s2526PendingAchievements = [];
    }

    // ─── S2526: ACTIVIDADES PERIODO ANTERIOR ──────────────────

    public function openS2526Modal()
    {
        $this->s2526Search = '';
        $this->s2526Lapso = '';
        $this->s2526SortField = 'finicial';
        $this->s2526SortDir = 'asc';
        $this->s2526Page = 1;
        $this->loadS2526Lapsos();
        $this->loadS2526Activities();
        $this->showS2526Modal = true;
    }

    public function closeS2526Modal()
    {
        $this->showS2526Modal = false;
        $this->s2526Activities = null;
    }

    public function loadS2526Lapsos()
    {
        $this->s2526Lapsos = DB::connection('s2526')
            ->table('lapsos')
            ->join('pevaluacions', 'pevaluacions.lapso_id', '=', 'lapsos.id')
            ->where('pevaluacions.pensum_id', $this->pevaluacion->pensum_id)
            ->distinct()
            ->orderBy('lapsos.name')
            ->pluck('lapsos.name', 'lapsos.id')
            ->toArray();
    }

    public function loadS2526Activities()
    {
        $query = DB::connection('s2526')
            ->table('activities')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('lapsos', 'pevaluacions.lapso_id', '=', 'lapsos.id')
            ->join('seccions', 'pevaluacions.seccion_id', '=', 'seccions.id')
            ->where('pevaluacions.pensum_id', $this->pevaluacion->pensum_id)
            ->select(
                'activities.*',
                'lapsos.name as lapso_name',
                'seccions.name as seccion_name'
            );

        // Búsqueda por texto
        if ($this->s2526Search) {
            $query->where(function ($q) {
                $q->where('activities.topic', 'like', '%' . $this->s2526Search . '%')
                  ->orWhere('activities.teaching', 'like', '%' . $this->s2526Search . '%')
                  ->orWhere('activities.description', 'like', '%' . $this->s2526Search . '%');
            });
        }

        // Filtro por lapso
        if ($this->s2526Lapso) {
            $query->where('pevaluacions.lapso_id', $this->s2526Lapso);
        }

        // Ordenamiento
        $query->orderBy('activities.' . $this->s2526SortField, $this->s2526SortDir);

        // Paginate manually to avoid Livewire serialization issues with stdClass
        $total = $query->count();
        $this->s2526Total = $total;
        $this->s2526LastPage = max(1, (int) ceil($total / $this->s2526PerPage));
        $this->s2526Page = min($this->s2526Page, $this->s2526LastPage);

        $this->s2526From = ($this->s2526Page - 1) * $this->s2526PerPage + 1;

        $items = $query
            ->skip(($this->s2526Page - 1) * $this->s2526PerPage)
            ->take($this->s2526PerPage)
            ->get()
            ->map(function ($item) {
                return (array) $item;
            })
            ->toArray();

        $this->s2526Activities = $items;
    }

    public function updatedS2526Search()
    {
        $this->s2526Page = 1;
        $this->loadS2526Activities();
    }

    public function updatedS2526Lapso()
    {
        $this->s2526Page = 1;
        $this->loadS2526Activities();
    }

    public function sortS2526($field)
    {
        if ($this->s2526SortField === $field) {
            $this->s2526SortDir = $this->s2526SortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->s2526SortField = $field;
            $this->s2526SortDir = 'asc';
        }
        $this->s2526Page = 1;
        $this->loadS2526Activities();
    }

    public function gotoPageS2526($page)
    {
        $this->s2526Page = $page;
        $this->loadS2526Activities();
    }

    public function s2526ViewDetail($index)
    {
        if (isset($this->s2526Activities[$index])) {
            $this->s2526DetailActivity = $this->s2526Activities[$index];
            $this->s2526DetailAchievements = DB::connection('s2526')
                ->table('achievements')
                ->where('activity_id', $this->s2526DetailActivity['id'])
                ->get()
                ->map(fn($item) => (array) $item)
                ->toArray();
            $this->showS2526DetailModal = true;
        }
    }

    public function closeS2526DetailModal()
    {
        $this->showS2526DetailModal = false;
        $this->s2526DetailActivity = [];
        $this->s2526DetailAchievements = [];
    }

    public function s2526CopyToPlan($index)
    {
        if (!isset($this->s2526Activities[$index])) {
            return;
        }

        $act = $this->s2526Activities[$index];

        // Cerrar modales s2526
        $this->showS2526Modal = false;
        $this->showS2526DetailModal = false;

        // Reset y preparar form de creación
        $this->close();
        $this->resetModel();
        $this->activity_id = null;

        // Poblar el form con los datos de la actividad del periodo anterior
        $act['pevaluacion_id'] = $this->pevaluacion_id;
        $this->activityForm->fillFromArray($act);

        // Limpiar fechas para que el usuario establezca las nuevas
        $this->activityForm->finicial = null;
        $this->activityForm->ffinal = null;

        // Cargar achievements de la actividad fuente desde s2526
        $this->s2526PendingAchievements = DB::connection('s2526')
            ->table('achievements')
            ->where('activity_id', $act['id'])
            ->get()
            ->map(fn($item) => [
                'name' => $item->name,
                'weighting' => $item->weighting,
                'status_quantitative_weighting' => $item->status_quantitative_weighting ?? false,
            ])
            ->toArray();

        // Mostrar el formulario de creación
        $this->modeCreator = true;
    }

    /**
     * Copiar desde la actividad mostrada en el modal de detalle s2526.
     */
    public function s2526CopyFromDetail()
    {
        if (empty($this->s2526DetailActivity)) {
            return;
        }

        $act = $this->s2526DetailActivity;

        // Cerrar modales s2526
        $this->showS2526Modal = false;
        $this->showS2526DetailModal = false;

        // Reset y preparar form de creación
        $this->close();
        $this->resetModel();
        $this->activity_id = null;

        // Poblar el form con los datos de la actividad del periodo anterior
        $act['pevaluacion_id'] = $this->pevaluacion_id;
        $this->activityForm->fillFromArray($act);

        // Limpiar fechas para que el usuario establezca las nuevas
        $this->activityForm->finicial = null;
        $this->activityForm->ffinal = null;

        // Cargar achievements de la actividad fuente desde s2526
        $this->s2526PendingAchievements = DB::connection('s2526')
            ->table('achievements')
            ->where('activity_id', $act['id'])
            ->get()
            ->map(fn($item) => [
                'name' => $item->name,
                'weighting' => $item->weighting,
                'status_quantitative_weighting' => $item->status_quantitative_weighting ?? false,
            ])
            ->toArray();

        // Mostrar el formulario de creación
        $this->modeCreator = true;
    }

    public function resetModel()
    {
        $this->activity = new Activity();

        $this->achievement_id = null;
        $this->activityForm->reset();
        $this->achievementForm->resetForm();
    }
}
