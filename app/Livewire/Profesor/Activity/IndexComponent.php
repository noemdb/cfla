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
use Livewire\Component;

class IndexComponent extends Component
{
    use ValidateTrait;

    public Activity $activity;
    public Achievement $achievement;

    public $seccion_id, $activity_id, $pevaluacion, $pevaluacion_id, $activities, $achievement_id;
    public $modeCreator, $modeEdit, $modeCreatorAchievement, $modeEditAchievement;
    public $list_comment, $list_seccions;
    public $grado_id, $lapso_id;
    public $enable_edit;
    public $showDetailModal = false;
    public $detailActivity;

    public $list_grado, $list_seccion, $list_lapso;

    public function updatedGradoId($value)
    {
        $this->list_seccion = ($this->grado_id)
            ? Seccion::where('grado_id', $this->grado_id)->pluck('name', 'id')
            : collect();
    }

    public function updatedAchievementStatusQuantitativeWeighting($value)
    {
        $this->achievement->weighting = $value ?: null;
    }

    public function mount($id)
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);

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
        $this->list_comment = Activity::COLUMN_COMMENTS;
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

    public function render()
    {
        $this->activities = Activity::where('pevaluacion_id', $this->pevaluacion_id)
            ->orderBy('finicial')
            ->get();

        return view('livewire.profesor.activity.index-component');
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
    }

    public function addAchievement($id)
    {
        $this->close();
        $this->resetModel();
        $this->activity_id = null;
        $this->modeCreatorAchievement = true;
        $this->activity = Activity::findOrFail($id);
    }

    public function setEditAchievement($id)
    {
        $this->close();
        $this->modeCreatorAchievement = true;
        $this->achievement = Achievement::findOrFail($id);
        $this->activity = $this->achievement->activity;
        $this->activity_id = $this->activity->id;
        $this->achievement_id = $id;
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
        $this->validate();

        $this->activity->pevaluacion_id = $this->pevaluacion->id;
        $this->activity->description   = ($this->activity->description === '')   ? null : $this->activity->description;
        $this->activity->observations  = ($this->activity->observations === '')  ? null : $this->activity->observations;
        $this->activity->references    = ($this->activity->references === '')    ? null : $this->activity->references;
        $this->activity->teaching      = ($this->activity->teaching === '')      ? null : $this->activity->teaching;
        $this->activity->learning      = ($this->activity->learning === '')      ? null : $this->activity->learning;
        $this->activity->topic         = ($this->activity->topic === '')         ? null : $this->activity->topic;
        $this->activity->thematic      = ($this->activity->thematic === '')      ? null : $this->activity->thematic;

        $this->activity->save();

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
        $this->achievement->activity_id = $this->activity->id;

        $this->validate([
            'achievement.name' => 'required|min:6',
            'achievement.weighting' => 'nullable|numeric|min:0|max:100',
            'achievement.status_quantitative_weighting' => 'nullable|boolean',
        ]);

        $this->achievement->save();

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

    public function close()
    {
        $this->modeCreator = false;
        $this->modeEdit = false;
        $this->modeCreatorAchievement = false;
        $this->modeEditAchievement = false;
    }

    public function resetModel()
    {
        $this->activity = new Activity();
        $this->activity->finicial = null;
        $this->activity->ffinal = null;
        $this->activity->topic = null;
        $this->activity->thematic = null;
        $this->activity->references = null;
        $this->activity->teaching = null;
        $this->activity->learning = null;
        $this->activity->observations = null;
        $this->activity->description = null;

        $this->achievement = new Achievement();
        $this->achievement->name = null;
        $this->achievement->weighting = null;
        $this->achievement_id = null;
    }
}
