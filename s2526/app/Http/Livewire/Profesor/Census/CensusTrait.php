<?php

namespace App\Http\Livewire\Profesor\Census;

trait CensusTrait
{
    protected $rules = [
        // 'census.user_id'=>'required|integer',
        'census.ci_estudiant'=>'required|numeric|unique:censuses,ci_estudiant',
        'census.lastname'=>'required|string',
        'census.name'=>'required|string',
        'census.gender'=>'required|string',
        'census.date_birth'=>'required|string',
        'census.town_hall_birth'=>'nullable|string',
        'census.state_birth'=>'nullable|string',
        'census.country_birth'=>'nullable|string',
        'census.dir_address'=>'nullable:string',
        'census.grado_id'=>'nullable|integer',
        'census.institution'=>'nullable|string',
        'census.ci_representant'=>'required|string',
        'census.name_representant'=>'required|string',
        'census.relationship'=>'required|string',
        'census.phone_representant'=>'required|string',
        'census.email_representant'=>'required|email',
        'census.status_admite'=>'nullable',
    ];

    protected function validationAttributes()
    {
        return [
            // 'census.user_id' => $this->list_comment['user_id'],
            'census.ci_estudiant' => $this->list_comment['ci_estudiant'],
            'census.lastname' => $this->list_comment['lastname'],
            'census.name' => $this->list_comment['name'],
            'census.gender' => $this->list_comment['gender'],
            'census.date_birth' => $this->list_comment['date_birth'],
            'census.town_hall_birth' => $this->list_comment['town_hall_birth'],
            'census.state_birth' => $this->list_comment['state_birth'],
            'census.country_birth' => $this->list_comment['country_birth'],
            'census.dir_address' => $this->list_comment['dir_address'],
            'census.pestudio_id' => $this->list_comment['pestudio_id'],
            'census.grado_id' => $this->list_comment['grado_id'],
            'census.institution' => $this->list_comment['institution'],
            'census.ci_representant' => $this->list_comment['ci_representant'],
            'census.name_representant' => $this->list_comment['name_representant'],
            'census.relationship' => $this->list_comment['relationship'],
            'census.phone_representant' => $this->list_comment['phone_representant'],
            'census.email_representant' => $this->list_comment['email_representant'],
            'census.status_admite' => $this->list_comment['status_admite'],
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
