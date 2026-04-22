<?php

namespace App\Livewire\App\General\Educational\Competition;

use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Educational\DebateAnswer;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;
use App\Models\app\Academy\Grado;

use App\Events\Competition\ScoreboardUpdated;

class AnswersComponent extends Component
{
    use WireUiActions, WithPagination;

    public $competition;
    public $search = '';
    public $debate_id = '';
    public $grado_id = '';
    public $status = '';
    public $category = '';
    public $weighting = '';
    public bool $filterAnswered = false;
    public bool $filterUnanswered = false;
    public $list_debates;
    public $list_grados;
    public $list_weightings;
    public $list_categories;

    public function mount(DebateCompetition $competition)
    {
        $this->competition = $competition;
        $this->list_debates = $this->competition->debates;
        $this->list_categories = DebateQuestion::getListCategories();
        
        $gradoIds = $this->competition->debates()->pluck('grado_id')->unique();
        $this->list_grados = Grado::whereIn('id', $gradoIds)->get();
        $this->list_weightings = DebateQuestion::list_weighting();
    }

    public function updatedSearch() { $this->resetPage(); }
    public function updatedDebateId() { $this->resetPage(); }
    public function updatedGradoId() { $this->resetPage(); }
    public function updatedWeighting() { $this->resetPage(); }
    public function updatedStatus() { $this->resetPage(); }
    public function updatedCategory() { $this->resetPage(); }
    public function updatedFilterAnswered() { $this->resetPage(); }
    public function updatedFilterUnanswered() { $this->resetPage(); }

    public function resetFilters()
    {
        $this->reset(['search', 'debate_id', 'grado_id', 'weighting', 'status', 'category', 'filterAnswered', 'filterUnanswered']);
        $this->resetPage();
    }

    public function toggleNullifyStatus($questionId)
    {
        $question = DebateQuestion::find($questionId);
        if (!$question) return;
        
        if ($question->status_under_review) {
            // Desanular -> Devolver puntuación original y quitar estado de revisión
            $question->answers()->update(['score' => $question->weighting]);
            $question->update(['status_under_review' => false]);
            
            $this->notification()->success(
                $title = 'Respuesta Restaurada',
                $description = 'Se ha restaurado el puntaje de esta respuesta a su valor original de '.$question->weighting.' puntos.'
            );
        } else {
            // Anular -> Puntaje 0 y pasar a revisión
            $question->answers()->update(['score' => 0]);
            $question->update(['status_under_review' => true]);
            
            $this->notification()->warning(
                $title = 'Respuesta Anulada',
                $description = 'Los puntos han sido anulados (score = 0) y colocados bajo revisión.'
            );
        }

        // Broadcast al scoreboard para actualización en tiempo real
        broadcast(new ScoreboardUpdated($this->competition->id))->toOthers();
    }

    public function paginationView()
    {
        return 'livewire.app.general.educational.competition.partials.pagination';
    }

    public function render()
    {
        $debateIds = $this->competition->debates()->pluck('id');
        
        $questions = DebateQuestion::with(['debate', 'answers.grado', 'answers.seccion', 'answers.option'])
            ->whereIn('debate_id', $debateIds)
            ->when($this->search, function($query) {
                $query->where('text', 'like', '%' . $this->search . '%');
            })
            ->when($this->debate_id, function($query) {
                $query->where('debate_id', $this->debate_id);
            })
            ->when($this->grado_id, function($query) {
                $query->whereHas('debate', function($q) {
                    $q->where('grado_id', $this->grado_id);
                });
            })
            ->when($this->category, function($query) {
                $query->where('category', $this->category);
            })
            ->when($this->weighting, function($query) {
                $query->where('weighting', $this->weighting);
            })
            ->when($this->filterAnswered, function($query) {
                $query->has('answers');
            })
            ->when($this->filterUnanswered, function($query) {
                $query->doesntHave('answers');
            })
            ->when($this->status, function($query) {
                switch ($this->status) {
                    case 'answered':
                        $query->where('status_answer', true);
                        break;
                    case 'unanswered':
                        $query->where('status_answer', false);
                        break;
                    case 'under_review':
                        $query->where('status_under_review', true);
                        break;
                }
            })
            ->paginate(12);

        return view('livewire.app.general.educational.competition.answers-component', compact('questions'));
    }
}
