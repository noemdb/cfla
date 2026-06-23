<?php

namespace App\Http\Livewire\Profesor\Activity;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Achievement;
use App\Models\app\Profesor\Activity;
use App\Models\app\Profesor\Pevaluacion;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{
    use ValidateTrait;

    public Activity $activity;
    public Achievement $achievement;

    public $seccion_id, $activity_id, $pevaluacion, $pevaluacion_id, $activities, $achievement_id;
    public $modeCreator, $modeEdit, $modeCreatorAchievement;
    public $list_comment, $list_seccions;
    public $grado_id, $lapso_id;
    public $enable_edit;

    public $list_grado, $list_seccion, $list_lapso;

    public function updatedGradoId($value)
    {
        $this->list_seccion = ($this->grado_id) ? Seccion::list_seccion_grado($this->grado_id) : array();
    }

    public function updatedAchievementStatusQuantitativeWeighting($value)
    {
        $this->achievement->weighting = $value ?: null;
        //$this->list_seccion = ($this->grado_id) ? Seccion::list_seccion_grado($this->grado_id) : Array() ;
    }


    public function clone($id)
    {
        $pevaluacion = Pevaluacion::findOrFail($id);
        $seccion = Seccion::find($this->seccion_id);

        if ($seccion) {
            $pevaluacion_new = Pevaluacion::query()
                ->where('profesor_id', $pevaluacion->profesor_id)
                ->where('lapso_id', $pevaluacion->lapso_id)
                ->where('pensum_id', $pevaluacion->pensum_id)
                ->where('seccion_id', $seccion->id);

            $pevaluacion_new = ($pevaluacion->grupo_estable_id) ? $pevaluacion_new->where('grupo_estable_id', $pevaluacion->grupo_estable_id) : $pevaluacion_new;

            $pevaluacion_new = $pevaluacion_new->first();

            if ($pevaluacion_new) {

                $activities = $pevaluacion_new->activities; //dd($pevaluacion,$pevaluacion_new,$activities); grupo_estable_id

                if ($activities->isNotEmpty()) {
                    foreach ($activities as $activity) {
                        $arr = $activity->toArray();
                        $arr['pevaluacion_id'] = $pevaluacion->id;
                        $arr['comments'] = null;
                        $activity_new = Activity::create($arr);
                        $achievements = $activity->achievements; //dd($achievements);
                        foreach ($achievements as $achievement) {
                            $arr = $achievement->toArray();
                            $arr['activity_id'] = $activity_new->id; //dd($arr);
                            $achievement_new = Achievement::create($arr);
                        }
                    }
                    $title = '¡Excelente, buen trabajo! ';
                    $html = 'Registro realizado exitosamente';
                    $this->showSwal($title, $html);
                } else {
                    $title = '¡Ocurrieron errores! ';
                    $html = 'NO se registró ninguna operación';
                    $this->showSwal($title, $html, 'error');
                }
            } else {
                $title = '¡Ocurrieron errores! ';
                $html = 'NO se registró ninguna operación';
                $this->showSwal($title, $html, 'error');
            }
        } else {
            $title = '¡Ocurrieron errores! ';
            $html = 'Seleccione una sección que contenga actividades';
            $this->showSwal($title, $html, 'error');
        }
    }

    public function save()
    {
        $this->activity->pevaluacion_id = $this->pevaluacion->id;

        $this->validate([
            'activity.pevaluacion_id' => 'required|integer',
            'activity.finicial' => 'required|date',
            'activity.ffinal' => 'required|date',
            'activity.topic' => 'required|string',
            'activity.thematic' => 'required|string',
            'activity.references' => 'required|string',
            'activity.teaching' => 'nullable|string',
            'activity.learning' => 'nullable|string',
            'activity.observations' => 'required|string',
            'activity.description' => 'nullable|string',
        ]);

        $this->activity->description = ($this->activity->description === '') ? null : $this->activity->description;
        $this->activity->observations = ($this->activity->observations === '') ? null : $this->activity->observations;
        $this->activity->references = ($this->activity->references === '') ? null : $this->activity->references;

        $this->activity->save();

        $this->resetModel();
        $this->activity_id = null;

        $title = '¡Excelente, buen trabajo! ';
        $html = 'Registro realizado exitosamente';
        $this->showSwal($title, $html);

        $this->close();
    }

    public function mount($id)
    {
        $user_id = Auth::id();
        $user = User::findOrFail($user_id);
        $profesor = ($user->IsProfesor()) ? $user->profesor : null;

        $this->pevaluacion = Pevaluacion::findOrFail($id);
        $this->pevaluacion_id = $id;
        $this->list_comment = Activity::COLUMN_COMMENTS;
        $grado = $this->pevaluacion->grado;
        $this->list_seccions = $grado->getSeccionsActiveInscriptionAffect()->where('id', '<>', $this->pevaluacion->seccion_id)->pluck('name', 'id');

        $this->list_grado = Profesor::list_grado($profesor->id,true);  //modulo de planificacion
        $this->list_seccion = collect();
        $this->list_lapso = Lapso::select('name', 'id')->orderby('name', 'asc')->pluck('name', 'id');

        $lapso = $this->pevaluacion->lapso;
        $now = Carbon::now()->format('Y-m-d');
        $this->enable_edit = $lapso->status_preclosing;
    }

    public function render()
    {
        $activities = Activity::query()
            ->where('activities.pevaluacion_id', $this->pevaluacion_id)->orderBy('finicial');
        $this->activities = $activities->get();

        return view('livewire.profesor.activity.index-component');
    }

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

    public function saveAchievement()
    {
        $this->achievement->activity_id = $this->activity->id;
        $validatedData = $this->validate([
            'achievement.name' => 'required|min:6',
            'achievement.weighting' => 'nullable|numeric|min:0|max:100',
            // 'achievement.weighting' => 'required_if:achievement.status_quantitative_weighting,true|numeric|min:0|max:100',
            'achievement.status_quantitative_weighting' => 'nullable|boolean',
        ]);

        $this->achievement->save();

        $this->resetModel();

        $title = '¡Excelente, buen trabajo! ';
        $html = 'Registro realizado exitosamente';
        $this->showSwal($title, $html);

        $this->close();
    }

    public function close()
    {
        $this->modeCreator = false;
        $this->modeEdit = false;
        $this->modeCreatorAchievement = false;
    }

    public function showSwal($title, $html, $icon = 'success')
    {
        $this->dispatchBrowserEvent('swal', [
            'title' => $title,
            'html' => $html,
            'timer' => 6000,
            'icon' => $icon,
        ]);
    }

    public function emptyActivities($id)
    {
        $deletedRows = Activity::where('pevaluacion_id', $id)->delete();
        if ($deletedRows > 0) {
            $title = '¡Excelente, buen trabajo! ';
            $html = 'Los registros fueron eliminados exitosamente';
            $this->showSwal($title, $html);
        } else {
            $title = '¡Ocurrieron errores! ';
            $html = 'NO se eliminó ningún registro';
            $this->showSwal($title, $html, 'error');
        }
    }

    public function delActivity($id)
    {
        $activity = Activity::findOrFail($id);
        if ($activity) {
            $activity->delete();
            $this->close();
            $this->resetModel();
            $this->activity_id = null;
            $title = '¡Excelente, buen trabajo! ';
            $html = 'Registro fue eliminado exitosamente';
            $this->showSwal($title, $html);
        }
    }

    public function deleteAchievement($id)
    {
        $achievement = Achievement::findOrFail($id);
        if ($achievement) {
            $achievement->delete();
            $this->close();
            $this->achievement = new Achievement;
            $this->achievement_id = null;
            $this->activity = new Activity;
            $this->activity_id = null;
            $title = '¡Excelente, buen trabajo! ';
            $html = 'El registro fué eliminado exitosamente';
            $this->showSwal($title, $html);
        }
    }

    public function resetModel()
    {
        $this->activity = new Activity;
        $this->activity->finicial = null;
        $this->activity->ffinal = null;
        $this->activity->topic = null;
        $this->activity->thematic = null;
        $this->activity->references = null;
        $this->activity->teaching = null;
        $this->activity->learning = null;
        $this->activity->observations = null;
        $this->activity->description = null;

        $this->achievement = new Achievement;
        $this->achievement->name = null;
        $this->achievement->weighting = null;
    }

    public function resetActivity()
    {
        $this->activity = new Activity;
        $this->activity->finicial = null;
        $this->activity->ffinal = null;
        $this->activity->topic = null;
        $this->activity->thematic = null;
        $this->activity->references = null;
        $this->activity->teaching = null;
        $this->activity->learning = null;
        $this->activity->observations = null;
        $this->activity->description = null;
    }

    public function resetAchievement()
    {
        $this->achievement = new Achievement;
        $this->achievement->name = null;
        $this->achievement->weighting = null;
    }
}
