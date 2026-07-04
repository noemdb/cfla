<?php

namespace App\Http\Livewire\Movile\Profesor\Learning;

trait LessonTrait
{
    public $list_comment;

    // evaluacion_id
    // order
    // title
    // status
    // comments
    // evidence
    // requireds
    
    protected $rules = [
        'pevaluacion_id'=>'required|integer',
        'lesson.evaluacion_id'=>'required|integer',
        'lesson.order'=>'required|integer',
        'lesson.content'=>'nullable|string',
        'lesson.planned'=>'required|date',
        'lesson.teaching'=>'nullable|string',
        'lesson.learning'=>'nullable|string',
        'lesson.status'=>'required|boolean',
        'lesson.comments'=>'required|string',
        'lesson.evidence'=>'nullable|string',
        'lesson.requireds'=>'nullable|string',
        
        // 'lesson.title'=>'required|string',
        // 'lesson.description'=>'required|string',
        // 'lesson.objectives'=>'required|string',
        // 'lesson.activity_type'=>'required|string',
        // 'lesson.duration'=>'nullable|integer',
        // 'lesson.level'=>'required|integer',
        // 'lesson.planned'=>'required|date',
        // 'lesson.reprogrammed'=>'nullable|integer',
        // 'lesson.active'=>'required|boolean',
        // 'lesson.observations'=>'nullable|string',
        // 'lesson.pedagogical_id'=>'nullable|integer',
        // 'lesson.reprogrammed_by'=>'nullable|integer',
        // 'lesson.author_id'=>'required|integer',
        // 'lesson.modified_by'=>'nullable|integer',
    ];

    protected function validationAttributes()
    {
        return [
            'pevaluacion_id' => $this->list_comment['pevaluacion_id'],
            'lesson.evaluacion_id' => $this->list_comment['evaluacion_id'],
            'lesson.order' => $this->list_comment['order'],
            'lesson.content' => $this->list_comment['title'],
            'lesson.planned' => $this->list_comment['planned'],
            'lesson.teaching' => $this->list_comment['teaching'],
            'lesson.learning' => $this->list_comment['learning'],
            'lesson.status' => $this->list_comment['status'],
            'lesson.comments' => $this->list_comment['comments'],
            'lesson.evidence' => $this->list_comment['evidence'],
            'lesson.requireds' => $this->list_comment['requireds'],
            
            // 'lesson.title' => $this->list_comment['title'],
            // 'lesson.description' => $this->list_comment['description'],
            // 'lesson.objectives' => $this->list_comment['objectives'],
            // 'lesson.activity_type' => $this->list_comment['activity_type'],
            // 'lesson.duration' => $this->list_comment['duration'],
            // 'lesson.level' => $this->list_comment['level'],
            // 'lesson.planned' => $this->list_comment['planned'],
            // 'lesson.reprogrammed' => $this->list_comment['reprogrammed'],
            // 'lesson.active' => $this->list_comment['active'],
            // 'lesson.observations' => $this->list_comment['observations'],
            // 'lesson.pedagogical_id' => $this->list_comment['pedagogical_id'],
            // 'lesson.reprogrammed_by' => $this->list_comment['reprogrammed_by'],
            // 'lesson.author_id' => $this->list_comment['author_id'],
            // 'lesson.modified_by' => $this->list_comment['modified_by'],
        ];
    }
}

?>
