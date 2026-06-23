<?php

namespace App\Http\Livewire\Profesor\Debate;

use App\User;
use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateGroup;
use App\Models\app\Educational\DebateOption;
use App\Models\app\Educational\DebateQuestion;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class IndexComponent extends Component
{
    use ValidateTrait;
    use ResetTrait;
    use CreateTrait;
    use SaveTrait;
    use EditTrait;
    use DeepSeekTrait, QwenTrait;

    protected $aiProvider = 'deepseek'; // Options: 'deepseek', 'qwen'

    public DebateCompetition $competition;
    public Debate $debate;
    public DebateQuestion $question;
    public DebateOption $option;
    public DebateGroup $group;
    // public Gemini $client;

    public $competitions, $debates, $result, $cant_group;
    public $user_id, $competition_id, $debate_id, $group_id, $question_id, $option_id;
    public $modeIndex, $modeCreator, $modeEdit, $modeCreatorDebate, $modeCreatorGroup, $modeCreatorQuestion, $modeCreatorOption, $modeCreatorGeminiDebate;
    public $modeCreatorGeminiCompetition;
    public $list_comment, $list_comment_debate, $list_comment_group, $list_comment_question, $list_comment_option;
    public $list_competition, $list_grado, $list_seccion, $list_category, $list_weighting, $list_timing;
    public $activities, $checkboxes;
    public $attachment, $referents;
    public bool $statusEmpiricalEvidence = false;
    public $profesor_id, $grado_id, $seccion_id;
    public $showDiv;

    public bool $statusApproachConstructivist = false;
    public bool $statusApproachSociocultural = false;
    public bool $statusApproachHumanist = false;
    public bool $statusApproachCritical = false;
    public bool $statusApproachCulturalHistorical = false;
    public bool $statusApproachEcological = false;

    public bool $statusCognitiveInductive = false;
    public bool $statusCognitiveSynthetic = false;
    public bool $statusCognitiveAnalytical = false;
    public bool $statusCognitiveCreativo = false;
    public bool $statusCognitiveCritical = false;

    public $crossCutting;

    public function updatedGradoId($value)
    {
        $this->list_seccion = Seccion::list_seccion_grado($value);
        $profesor = Profesor::find($this->profesor_id);
        $this->activities = $profesor->getActivities(null, null, $value);
        $this->resetErrorBag();
    }

    public function updatedSeccionId($value)
    {
        $this->list_seccion = Seccion::list_seccion_grado($value);
        $profesor = Profesor::find($this->profesor_id);
        $this->activities = $profesor->getActivities(null, $value);
        $this->resetErrorBag();
    }

    public function mount()
    {
        $user = User::findOrFail(Auth::id());
        $this->user_id = $user->id;
        $profesor = $user->profesor;
        $this->profesor_id = $profesor->id;
        $this->list_comment = DebateCompetition::COLUMN_COMMENTS;
        $this->list_grado = Profesor::list_grado($profesor->id);
        $this->list_seccion = collect();
        $this->list_comment_debate = Debate::COLUMN_COMMENTS;
        $this->list_comment_question = DebateQuestion::COLUMN_COMMENTS;
        $this->list_comment_option = DebateOption::COLUMN_COMMENTS;
        $this->list_comment_group = DebateGroup::COLUMN_COMMENTS;
        $this->close();
        // $this->list_category = DebateQuestion::CATEGORY;
        $this->list_category = DebateQuestion::getListCategory();
        $this->list_weighting = ['30' => '30', '50' => '50', '100' => '100'];
        $this->list_timing = ['30' => '30', '45' => '45', '60' => '60', '100' => '100'];
        // $this->referents = 'Sentimos Y Reaccionamos Antes Los Cambios Ambientales, tipos De Receptores. Mecanismos De Los Receptores. Reacción De Los Receptores Ante Los Cambios Ambientales.';

    }

    public function render()
    {
        $this->competitions = DebateCompetition::query();
        $this->competitions = $this->competitions->where('user_id', $this->user_id);
        $this->competitions = $this->competitions->where('id', $this->competition_id);
        $this->competitions = $this->competitions->get();
        $this->debates = Debate::where('competition_id', $this->competition_id)->get();
        $this->list_competition = DebateCompetition::where('user_id', $this->user_id)->pluck('name', 'id');
        return view('livewire.profesor.debate.index-component');
    }

    public function close()
    {
        $this->modeIndex = true;
        $this->modeCreator = false;
        $this->modeEdit = false;
        $this->modeCreatorDebate = false;
        $this->modeCreatorQuestion = false;
        $this->modeCreatorOption = false;
        $this->modeCreatorGroup = false;
        $this->modeCreatorGeminiCompetition = false;
        $this->modeCreatorGeminiDebate = false;
        $this->grado_id = null;
        $this->list_seccion = collect();
        $this->resetStatus();
        $this->resetErrorBag();
    }

    public function resetStatus()
    {
        $this->statusApproachConstructivist = false;
        $this->statusApproachSociocultural = false;
        $this->statusApproachHumanist = false;
        $this->statusApproachCritical = false;
        $this->statusApproachCulturalHistorical = false;
        $this->statusApproachEcological = false;
        $this->statusCognitiveInductive = false;
        $this->statusCognitiveSynthetic = false;
        $this->statusCognitiveAnalytical = false;
        $this->statusCognitiveCreativo = false;
        $this->statusCognitiveCritical = false;
        $this->statusEmpiricalEvidence = false;
        $this->crossCutting = null;
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

    public function updatedDebateGradoId($value)
    {
        $this->list_seccion = Seccion::list_seccion_grado($value);
    }

    /**
     * AI Bridge Methods
     */

    public function aiCreateCompetition()
    {
        if ($this->aiProvider === 'qwen') {
            $this->qwCreateCompetition();
        } else {
            $this->dsCreateCompetition();
        }
    }

    public function aiGenerateCompetition()
    {
        if ($this->aiProvider === 'qwen') {
            $this->qwGenerateCompetition();
        } else {
            $this->dsGenerateCompetition();
        }
    }

    public function aiCreateDebate($id)
    {
        if ($this->aiProvider === 'qwen') {
            $this->qwCreateDebate($id);
        } else {
            $this->dsCreateDebate($id);
        }
    }

    public function generateAiDebate($id)
    {
        if ($this->aiProvider === 'qwen') {
            $this->qwGenerateAiDebate($id);
        } else {
            $this->dsGenerateAiDebate($id);
        }
    }
}
