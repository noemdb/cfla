<?php

namespace App\Http\Livewire\Movile\Profesor\Activity;

trait ActivityValidateTrait
{    
    protected $rules = [
        'activity.pevaluacion_id'=>'required|integer',
        'activity.finicial'=>'required|date',
        'activity.ffinal'=>'required|date',
        'activity.topic'=>'required|string',
        'activity.thematic'=>'required|string',
        'activity.references'=>'required|string',
        'activity.teaching'=>'nullable|string',
        'activity.learning'=>'nullable|string',
        'activity.observations'=>'required|string',
        'activity.description'=>'nullable|string',
        'achievement.name'=>'null|string',
        'achievement.weighting' => 'nullable|numeric|min:0|max:100',
        'achievement.status_quantitative_weighting' => 'nullable|boolean',
    ];

    protected function validationAttributes()
    {
        return [
            'activity.pevaluacion_id' => $this->list_comment['pevaluacion_id'],
            'activity.finicial' => $this->list_comment['finicial'],
            'activity.ffinal' => $this->list_comment['ffinal'],
            'activity.topic' => $this->list_comment['topic'],
            'activity.thematic' => $this->list_comment['thematic'],
            'activity.references' => $this->list_comment['references'],
            'activity.teaching' => $this->list_comment['teaching'],
            'activity.learning' => $this->list_comment['learning'],
            'activity.description' => $this->list_comment['description'],
            'activity.observations' => $this->list_comment['observations'],
            'achievement.name' => $this->list_comment['name'],
            'achievement.weighting' => $this->list_comment['weighting'],
            'achievement.status_quantitative_weighting' => $this->list_comment['status_quantitative_weighting'],
        ];
    }
}