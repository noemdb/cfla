<?php

namespace App\Livewire\App\General\Educational\Competition\Moderator;
//livewire.app.general.educational.competition.moderator.question-component

use App\Events\Competition\DebateActivated;
use App\Events\Competition\QuestionActivated;
use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateQuestion;
use Livewire\Component;
use Livewire\Attributes\On;

class QuestionComponent extends Component
{
    public $debate,$debate_id,$questions,$question,$active_id,$category,$list_category,$list_weighting,$weighting;
    public array $selectedWeightings = [];
    public bool $filterAnswered = false;
    public bool $filterUnanswered = true;

    #[On('detabe-active')] 
    public function updateQuestionsList($id)
    {
        $this->debate = Debate::find($id) ?? new Debate();
        $this->updatedCategory($this->category);
        $this->loadActive();       
        $this->updatedListCategory($id);
        $this->dispatch('question-active',id: $this->active_id);
    }

    #[On('update-score')] 
    public function updateScore()
    {
        $this->updatedCategory($this->category);
    }

    #[On('question-online')] 
    public function updateQuestionOnline($id)
    {
        // $this->question = DebateQuestion::findOrFail($id);
        $this->question = DebateQuestion::find($id) ?? new DebateQuestion();
        $this->category = ($this->question) ? $this->question->category : null; 
    }

    public function mount($debate_id)
    {
        $this->updateQuestionsList($debate_id); 
        $this->loadActive();    
        $this->list_weighting = DebateQuestion::list_weighting();   //dd($this->list_weighting);  
    }

    public function loadActive()
    {
        $question =  DebateQuestion::where('status_active',true)->first();
        $this->question = ($question) ? $question : null;
        $this->active_id = ($question) ? $question->id : null;
        $this->category = ($question) ? $question->category : null;        
    }
    
    public function render()
    {
        $active = collect($this->questions)->filter(fn($q) => $q->status_active);
        $inactive = collect($this->questions)->reject(fn($q) => $q->status_active);
        
        $this->questions = $active->merge($inactive)->values();
        
        return view('livewire.app.general.educational.competition.moderator.question-component');
    }

    public function active($id)
    {
        $question = DebateQuestion::findOrFail($id);
        $this->debate = $question->debate;
        $this->question = $question;
        $this->active_id = $question->id ;
        $this->dispatch('question-active',id: $id);
        $this->updatedListCategory($this->debate->id);
        // $this->updatedWeighting();
    }

    public function updatedCategory($category)
    {
        $query = DebateQuestion::where('debate_id', $this->debate->id)
            ->where('category', 'like', '%'.$category.'%')
            ->whereNotNull('pensum_id');

        // Combinar select rápido + checkboxes en un único filtro whereIn (semántica OR)
        $weightingFilter = array_unique(array_filter(
            array_merge($this->selectedWeightings, $this->weighting ? [$this->weighting] : [])
        ));
        if (!empty($weightingFilter)) {
            $query->whereIn('weighting', $weightingFilter);
        }

        if ($this->filterAnswered) {
            $query->where('status_answer', true);
        }
        if ($this->filterUnanswered) {
            $query->where('status_answer', false);
        }

        $this->questions = $query->inRandomOrder()->get();
    }

    public function updatedListCategory($debate_id)
    {
        $this->list_category =  DebateQuestion::CATEGORY;
        $pestudio = $this->debate->pestudio; //dd($pestudio);
        $filteredArray = [];
        foreach ($this->list_category as $key => $value) {
            if (strpos($key, '['.$pestudio->code_oficial.']') !== false) {
                $filteredArray[$key] = $value;
            }
        }
        $this->list_category = $filteredArray;
    }

    public function updatedSelectedWeightings()
    {
        $this->updatedCategory($this->category);
    }

    public function updatedWeighting()
    {
        $this->updatedCategory($this->category);
    }

    public function updatedFilterAnswered()
    {
        $this->updatedCategory($this->category);
    }

    public function updatedFilterUnanswered()
    {
        $this->updatedCategory($this->category);
    }

    public function setOnline($id)
    {
        $this->debate = Debate::setActive($id);
        $this->question = DebateQuestion::find($this->active_id);
        $this->updatedCategory($this->category);
        $this->dispatch('debate-online', id: $id);
        $qId = ($this->question) ? $this->question->id : null;
        $this->dispatch('question-active', id: $qId);

        // Broadcast vía WebSocket
        event(new DebateActivated($this->debate->competition_id, $id));
        event(new QuestionActivated(
            $this->debate->competition_id,
            $qId,
            $this->question?->timeRemaining,
        ));
    }

    public function setOffline($id)
    {
        $this->debate = Debate::setDesActive($id);
        $this->question = DebateQuestion::find($this->active_id);
        $this->updatedCategory($this->category);
        $this->dispatch('debate-online', id: $id);
        $qId = ($this->question) ? $this->question->id : null;
        $this->dispatch('question-active', id: $qId);

        // Broadcast vía WebSocket
        event(new DebateActivated($this->debate->competition_id, null));
    }

    public function setOnlineQuestion($id)
    {
        $this->question = DebateQuestion::setActive($id);
        $this->active_id = $id;

        // Broadcast vía WebSocket
        event(new QuestionActivated(
            $this->question->debate->competition_id,
            $id,
            $this->question->timeRemaining,
        ));
    }

    public function setOfflineQuestion($id)
    {
        $this->question = DebateQuestion::setDesActive($id);
        $this->active_id = null;

        // Broadcast: pregunta desactivada
        if ($this->debate) {
            event(new QuestionActivated($this->debate->competition_id, null, null));
        }
    }

    public function activeOnline($id)
    {
        // $this->question = DebateQuestion::find($id);
        $this->question = DebateQuestion::setActive($id);
        $this->questions =  DebateQuestion::where('debate_id',$this->question->debate_id)->whereNotNull('pensum_id')->get();
    }

    public function reshuffle()
    {
        $grouped = collect($this->questions)->shuffle()->groupBy('category');
        
        $interleaved = collect();
        
        while ($grouped->isNotEmpty()) {
            $keys = $grouped->keys()->shuffle();
            
            foreach ($keys as $key) {
                if ($grouped->has($key)) {
                    $items = $grouped->get($key);
                    $interleaved->push($items->shift());
                    
                    if ($items->isEmpty()) {
                        $grouped->forget($key);
                    }
                }
            }
        }
        
        $this->questions = $interleaved;
    }

}