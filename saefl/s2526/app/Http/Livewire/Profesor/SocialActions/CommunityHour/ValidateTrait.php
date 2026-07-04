<?php

namespace App\Http\Livewire\Profesor\SocialActions\CommunityHour;

trait ValidateTrait
{    
    protected $rules = [
        'community_action.title'=>'required|string',
        'community_action.description'=>'required|string',
        'community_action.observations'=>'nullable|string',
        'community_action.date'=>'required|date',
        'community_action.duration'=>'required|integer',
        'community_action.status'=>'required|boolean',
        'community_action.type'=>'required|string',
        'community_action.entity_benefic'=>'nullable|string',
        'community_action.location'=>'nullable|string',
        'community_action.required'=>'nullable|string',
    ];

    protected function validationAttributes()
    {
        return [
            'community_action.title' => $this->list_comment['title'],
            'community_action.description' => $this->list_comment['description'],
            'community_action.observations' => $this->list_comment['observations'],
            'community_action.date' => $this->list_comment['date'],
            'community_action.duration' => $this->list_comment['duration'],
            'community_action.status' => $this->list_comment['status'],
            'community_action.type' => $this->list_comment['type'],
            'community_action.entity_benefic' => $this->list_comment['entity_benefic'],
            'community_action.location' => $this->list_comment['location'],
            'community_action.required' => $this->list_comment['required'],
        ];
    }
}