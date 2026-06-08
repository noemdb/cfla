<?php

namespace App\Http\Livewire\Planning\Competition;

use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateOption;
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

    public $clone_pensum_id, $clone_competition_id = 1, $clone_source_grado;
    public $clone_target_grado, $clone_target_debate_id, $clone_target_pensum_id, $clone_source_category, $clone_target_category;
    public $list_target_grados = [], $list_target_debates = [], $list_target_pensums = [];
    public $step = 1, $source_questions_count = 0;

    // --- CRUD Preguntas / Opciones ---
    public $q_pensum_id, $q_list = [], $q_active_question_id;
    public $q_form_mode = null; // 'create'|'edit'
    public $q_id, $q_text, $q_category_field, $q_time = 30, $q_weighting = 1;
    public $q_observation, $q_option_max = 4, $q_status_active = 1;

    public $opt_list = [], $opt_form_mode = null; // 'create'|'edit'
    public $opt_id, $opt_text, $opt_observation, $opt_status_option_correct = false;

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

    public function setClonePensum($pensum_id)
    {
        $this->clone_pensum_id = $pensum_id;
        $pensum = Pensum::find($pensum_id);
        $this->clone_source_grado = $pensum ? $pensum->grado_id : null;
        $this->clone_competition_id = 1;

        $this->list_target_grados = Grado::list_pestudio_grado($this->pestudio_id)->toArray();
        $this->clone_target_grado = null;
        $this->clone_target_debate_id = null;
        $this->clone_target_pensum_id = null;
        $this->clone_source_category = ($this->category != 'DEBATE') ? $this->category : null;
        $this->clone_target_category = null;
        $this->list_target_debates = [];
        $this->list_target_pensums = [];
        $this->step = 1;
        $this->source_questions_count = 0;
        if ($this->clone_source_category) {
            $this->updatedCloneSourceCategory($this->clone_source_category);
        }
    }

    public function updatedCloneSourceCategory($value)
    {
        if ($value && $this->clone_source_grado) {
            $this->source_questions_count = DebateQuestion::whereHas('debate', function($q) {
                $q->where('competition_id', $this->clone_competition_id)
                  ->where('grado_id', $this->clone_source_grado);
            })
            ->where('category', $value)
            ->count();
        } else {
            $this->source_questions_count = 0;
        }
    }

    public function nextStep()
    {
        if ($this->step == 1) {
            $this->validate(['clone_source_category' => 'required']);
            if ($this->source_questions_count == 0) {
                $this->showSwal('Advertencia', 'No hay preguntas para clonar con esta categoría.', 'warning');
                return;
            }
        } elseif ($this->step == 2) {
            $this->validate([
                'clone_target_grado' => 'required',
                'clone_target_pensum_id' => 'required'
            ]);
        } elseif ($this->step == 3) {
            $this->validate([
                'clone_target_debate_id' => 'required',
                'clone_target_category' => 'required'
            ]);
        }

        if ($this->step < 4) {
            $this->step++;
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function updatedCloneTargetGrado($value)
    {
        if ($value) {
            $this->list_target_debates = Debate::where('grado_id', $value)->pluck('name', 'id')->toArray();
            
            $this->list_target_pensums = Pensum::select('pensums.*')
                ->selectRaw("CONCAT(grados.name, ' - ', asignaturas.name) as name")
                ->join('grados', 'grados.id', '=', 'pensums.grado_id')
                ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
                ->where('pensums.grado_id', $value)
                ->pluck('name', 'pensums.id')
                ->toArray();
        } else {
            $this->list_target_debates = [];
            $this->list_target_pensums = [];
        }
        $this->clone_target_debate_id = null;
        $this->clone_target_pensum_id = null;
    }

    public function cloneQuestions()
    {
        $this->validate([
            'clone_competition_id' => 'required',
            'clone_source_grado' => 'required',
            'clone_source_category' => 'required',
            'clone_target_debate_id' => 'required',
            'clone_target_pensum_id' => 'required',
            'clone_target_category' => 'required',
        ]);

        $sourceQuestions = DebateQuestion::whereHas('debate', function($q) {
                $q->where('competition_id', $this->clone_competition_id)
                  ->where('grado_id', $this->clone_source_grado);
            })
            ->where('category', $this->clone_source_category)
            ->get();

        if ($sourceQuestions->count() == 0) {
            $this->showSwal('Advertencia', 'No se encontraron preguntas en el origen seleccionado.', 'warning');
            return;
        }

        foreach ($sourceQuestions as $sq) {
            $newQ = $sq->replicate();
            $newQ->debate_id = $this->clone_target_debate_id;
            $newQ->pensum_id = $this->clone_target_pensum_id;
            $newQ->category = $this->clone_target_category;
            $newQ->save();

            foreach ($sq->options as $opt) {
                $newOpt = $opt->replicate();
                $newOpt->question_id = $newQ->id;
                $newOpt->save();
            }
        }

        $this->reset(['clone_target_grado', 'clone_target_debate_id', 'clone_target_pensum_id', 'clone_source_category', 'clone_target_category', 'step', 'source_questions_count']);
        
        $this->dispatchBrowserEvent('closeModal');
        $this->showSwal('¡Hecho!', 'Preguntas clonadas exitosamente!', 'success');
    }

    public function fixCategoryByPensum($pensum_id)
    {
        $pensum = Pensum::with('grado.pestudio')->find($pensum_id);

        if (!$pensum || !$pensum->grado || !$pensum->grado->pestudio) {
            $this->showSwal('Error', 'No se pudo determinar el Plan de Estudio del pensum.', 'error');
            return;
        }

        $correctCode = $pensum->grado->pestudio->code; // '21000' o '31059'
        $wrongCode   = ($correctCode == '21000') ? '31059' : '21000';

        $questions = DebateQuestion::where('pensum_id', $pensum_id)
            ->where('category', 'like', '[' . $wrongCode . ']%')
            ->get();

        $fixed   = 0;
        $skipped = 0;

        // Lookup inline usando constantes para evitar problemas con el magic __callStatic de Eloquent
        $mapDirect  = DebateQuestion::CATEGORY_MAP;
        $mapInverse = array_flip($mapDirect);
        $mapExtra   = defined(DebateQuestion::class . '::CATEGORY_MAP_INVERSE_EXTRA')
                        ? DebateQuestion::CATEGORY_MAP_INVERSE_EXTRA
                        : [];

        foreach ($questions as $q) {
            if ($correctCode === '31059') {
                $equivalent = $mapDirect[$q->category] ?? null;
            } else {
                $equivalent = $mapInverse[$q->category] ?? $mapExtra[$q->category] ?? null;
            }

            if ($equivalent) {
                $q->category = $equivalent;
                $q->save();
                $fixed++;
            } else {
                $skipped++;
            }
        }

        if ($fixed === 0 && $skipped === 0) {
            $this->showSwal('Sin cambios', 'No se encontraron categorías inconsistentes en este pensum.', 'info');
        } elseif ($skipped > 0) {
            $this->showSwal(
                'Corrección parcial',
                "Se corrigieron <b>{$fixed}</b> pregunta(s). <br><b>{$skipped}</b> no tienen equivalencia definida en el mapa de categorías.",
                'warning'
            );
        } else {
            $this->showSwal('¡Corregido!', "Se corrigieron <b>{$fixed}</b> pregunta(s) con categoría incorrecta.", 'success');
        }
    }

    // =========================================================
    // CRUD PREGUNTAS
    // =========================================================

    public function openQuestions($pensum_id)
    {
        $this->q_pensum_id         = $pensum_id;
        $this->q_active_question_id = null;
        $this->q_form_mode         = null;
        $this->opt_form_mode       = null;
        $this->opt_list            = [];
        $this->loadQuestions();
        $this->dispatchBrowserEvent('open-questions-modal');
    }

    private function loadQuestions()
    {
        $this->q_list = DebateQuestion::where('pensum_id', $this->q_pensum_id)
            ->with(['options'])
            ->orderBy('category')
            ->orderBy('id')
            ->get()
            ->toArray();
    }

    public function newQuestion()
    {
        $this->resetQuestionForm();
        $this->q_form_mode = 'create';
    }

    public function editQuestion($id)
    {
        $q = DebateQuestion::findOrFail($id);
        $this->q_id             = $q->id;
        $this->q_text           = $q->text;
        $this->q_category_field = $q->category;
        $this->q_time           = $q->time;
        $this->q_weighting      = $q->weighting;
        $this->q_observation    = $q->observation;
        $this->q_option_max     = $q->option_max;
        $this->q_status_active  = $q->status_active;
        $this->q_form_mode      = 'edit';
    }

    public function saveQuestion()
    {
        $this->validate([
            'q_text'           => 'required|min:10',
            'q_category_field' => 'required',
            'q_time'           => 'required|integer|min:5',
            'q_weighting'      => 'required|numeric|min:1',
            'q_option_max'     => 'required|integer|min:2|max:6',
        ]);

        $data = [
            'pensum_id'    => $this->q_pensum_id,
            'user_id'      => $this->user_id,
            'text'         => $this->q_text,
            'category'     => $this->q_category_field,
            'time'         => $this->q_time,
            'weighting'    => $this->q_weighting,
            'observation'  => $this->q_observation,
            'option_max'   => $this->q_option_max,
            'status_active'=> $this->q_status_active,
        ];

        if ($this->q_form_mode === 'edit') {
            DebateQuestion::findOrFail($this->q_id)->update($data);
            $this->showSwal('Actualizado', 'Pregunta actualizada correctamente.', 'success');
        } else {
            DebateQuestion::create($data);
            $this->showSwal('Creada', 'Pregunta creada correctamente.', 'success');
        }

        $this->resetQuestionForm();
        $this->loadQuestions();
    }

    public function deleteQuestion($id)
    {
        $q = DebateQuestion::with('options')->findOrFail($id);
        $q->options()->delete();
        $q->delete();

        if ($this->q_active_question_id == $id) {
            $this->q_active_question_id = null;
            $this->opt_list = [];
        }

        $this->loadQuestions();
        $this->showSwal('Eliminada', 'Pregunta y sus opciones eliminadas.', 'success');
    }

    private function resetQuestionForm()
    {
        $this->q_id             = null;
        $this->q_text           = null;
        $this->q_category_field = null;
        $this->q_time           = 30;
        $this->q_weighting      = 1;
        $this->q_observation    = null;
        $this->q_option_max     = 4;
        $this->q_status_active  = 1;
        $this->q_form_mode      = null;
        $this->resetErrorBag();
    }

    // =========================================================
    // CRUD OPCIONES
    // =========================================================

    public function selectQuestion($id)
    {
        $this->q_active_question_id = $id;
        $this->q_form_mode = null; // Fuerza a salir del modo edición de pregunta para ver las opciones
        $this->resetOptionForm();
        $this->loadOptions();
    }

    private function loadOptions()
    {
        $this->opt_list = DebateOption::where('question_id', $this->q_active_question_id)
            ->orderBy('id')
            ->get()
            ->toArray();
    }

    public function newOption()
    {
        $this->resetOptionForm();
        $this->opt_form_mode = 'create';
    }

    public function editOption($id)
    {
        $opt = DebateOption::findOrFail($id);
        $this->opt_id                    = $opt->id;
        $this->opt_text                  = $opt->text;
        $this->opt_observation           = $opt->observation;
        $this->opt_status_option_correct = (bool) $opt->status_option_correct;
        $this->opt_form_mode             = 'edit';
    }

    public function saveOption()
    {
        $this->validate([
            'opt_text' => 'required|min:2',
        ]);

        // Si se marca como correcta, desmarcar las demás de la misma pregunta
        if ($this->opt_status_option_correct) {
            DebateOption::where('question_id', $this->q_active_question_id)
                ->where('id', '!=', $this->opt_id ?? 0)
                ->update(['status_option_correct' => false]);
        }

        $data = [
            'question_id'           => $this->q_active_question_id,
            'user_id'               => $this->user_id,
            'text'                  => $this->opt_text,
            'observation'           => $this->opt_observation,
            'status_option_correct' => $this->opt_status_option_correct,
        ];

        if ($this->opt_form_mode === 'edit') {
            DebateOption::findOrFail($this->opt_id)->update($data);
            $this->showSwal('Actualizada', 'Opción actualizada.', 'success');
        } else {
            DebateOption::create($data);
            $this->showSwal('Creada', 'Opción creada.', 'success');
        }

        $this->resetOptionForm();
        $this->loadOptions();
        $this->loadQuestions(); // refresca conteo de opciones
    }

    public function deleteOption($id)
    {
        DebateOption::findOrFail($id)->delete();
        $this->loadOptions();
        $this->showSwal('Eliminada', 'Opción eliminada.', 'success');
    }

    private function resetOptionForm()
    {
        $this->opt_id                    = null;
        $this->opt_text                  = null;
        $this->opt_observation           = null;
        $this->opt_status_option_correct = false;
        $this->opt_form_mode             = null;
        $this->resetErrorBag();
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

    public function render()
    {
        $pensums = Pensum::select('pensums.*')
        ->selectRaw("CONCAT(grados.name, ' - ', asignaturas.name) as asignatura_name")
        ->selectRaw("
            (
                SELECT COUNT(dq.id)
                FROM debate_questions dq
                WHERE dq.pensum_id = pensums.id
                  AND (
                    (pestudios.code = '21000' AND dq.category LIKE '[31059]%')
                    OR
                    (pestudios.code = '31059' AND dq.category LIKE '[21000]%')
                  )
            ) as inconsistency_count
        ")
        ->with('questions.options')
        ->join('grados', 'grados.id', '=', 'pensums.grado_id')
        ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
        ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
        ->where('grados.id', $this->grado_id)
        ->groupby('pensums.id', 'pestudios.code')
        ->paginate($this->paginate);

        return view('livewire.planning.competition.index-component',[
            'pensums' => $pensums
        ]);
    }
}
