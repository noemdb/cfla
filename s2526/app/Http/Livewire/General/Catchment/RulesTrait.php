<?php

namespace App\Http\Livewire\General\Catchment;

trait RulesTrait
{
    protected $rules = [
        'catchment.group_id'=>'required|integer',
        'catchment.token'=>'required|string',
        'catchment.firstname'=>'required',
        'catchment.lastname'=>'required',
        'catchment.grade'=>'required',
        'catchment.date_birth'=>'required',
        'catchment.status_foreign'=>'required',
        'catchment.country_foreign'=>'required_if:catchment.status_foreign,true',
        'catchment.status_siblings_college'=>'required',
        'catchment.brothers'=>'nullable',
        'catchment.gender'=>'required',
        'catchment.origin'=>'nullable',
        'catchment.representant_name'=>'required',
        'catchment.representant_lastname'=>'required',
        'catchment.representant_ci'=>'required:string',
        'catchment.email'=>'required|email',
        'catchment.relationship'=>'required',
        'catchment.occupation'=>'nullable',
        'catchment.educational_level'=>'nullable',
        'catchment.representant_phone'=>'nullable',
        'catchment.direction'=>'nullable',
        'catchment.reason_catholic' => 'nullable',
        'catchment.reason_interest' => 'required',
        'catchment.aspects_valued' => 'nullable',
        'catchment.expectations' => 'nullable',
        'catchment.importance_education' => 'nullable',
        'catchment.expectations_education' => 'nullable',
        'catchment.participation_activities' => 'nullable',
        'catchment.skills_talents' => 'nullable',
        'catchment.interests' => 'nullable',
        'catchment.challenges' => 'nullable',
        'catchment.status_active' => 'required|boolean',
        'catchment.status_accept_terms' => 'required|boolean|accepted',
    ];

    protected $messages = [
        'catchment.group_id.required' => 'No hay cupo para el nivel/grado/año seleccionado.',
    ];

    protected function validationAttributes()
    {
        return [
            'catchment.group_id' => $this->list_comment['group_id'],
            'catchment.token' => $this->list_comment['token'],
            'catchment.firstname' => $this->list_comment['firstname'],
            'catchment.lastname' => $this->list_comment['lastname'],
            'catchment.grade' => $this->list_comment['grade'],
            'catchment.date_birth' => $this->list_comment['date_birth'],
            'catchment.status_foreign' => $this->list_comment['status_foreign'],
            'catchment.country_foreign' => $this->list_comment['country_foreign'],
            'catchment.status_siblings_college' => $this->list_comment['status_siblings_college'],
            'catchment.brothers' => $this->list_comment['brothers'],
            'catchment.gender' => $this->list_comment['gender'],
            'catchment.origin' => $this->list_comment['origin'],
            'catchment.representant_name' => $this->list_comment['representant_name'],
            'catchment.representant_lastname' => $this->list_comment['representant_lastname'],
            'catchment.representant_ci' => $this->list_comment['representant_ci'],
            'catchment.email' => $this->list_comment['email'],
            'catchment.relationship' => $this->list_comment['relationship'],
            'catchment.occupation' => $this->list_comment['occupation'],
            'catchment.educational_level' => $this->list_comment['educational_level'],
            'catchment.representant_phone' => $this->list_comment['representant_phone'],
            'catchment.direction' => $this->list_comment['direction'],
            'catchment.reason_catholic' => $this->list_comment['reason_catholic'],
            'catchment.reason_interest' => $this->list_comment['reason_interest'],
            'catchment.aspects_valued' => $this->list_comment['aspects_valued'],
            'catchment.expectations' => $this->list_comment['expectations'],
            'catchment.importance_education' => $this->list_comment['importance_education'],
            'catchment.expectations_education' => $this->list_comment['expectations_education'],
            'catchment.participation_activities' => $this->list_comment['participation_activities'],
            'catchment.skills_talents' => $this->list_comment['skills_talents'],
            'catchment.interests' => $this->list_comment['interests'],
            'catchment.challenges' => $this->list_comment['challenges'],
            'catchment.status_active' => $this->list_comment['status_active'],
            'catchment.status_accept_terms' => $this->list_comment['status_accept_terms'],
        ];
    }
}

/*

ci_estudiant
lastname
name
cellphone
gender
date_birth
town_hall_birth
state_birth
country_birth
dir_address
pestudio_id
grado_id
ci_representant
name_representant
lastname_representant
relationship
phone_representant
email_representant

*/

?>
