<?php

namespace App\Livewire\App\Enrollment;

trait RulesTrait
{
    protected $rules = [
        'enrollment.ci_estudiant'=>'required|unique:enrollments,ci_estudiant|digits_between:6,12',
        'enrollment.lastname'=>'required',
        'enrollment.name'=>'required',
        'enrollment.gender'=>'required',
        'enrollment.date_birth'=>'required',
        'enrollment.town_hall_birth'=>'nullable',
        'enrollment.state_birth'=>'nullable',
        'enrollment.country_birth'=>'nullable',
        'enrollment.dir_address'=>'nullable:string',
        'enrollment.grado_id'=>'required|integer',
        'enrollment.institution'=>'nullable',
        'enrollment.ci_representant'=>'required',
        'enrollment.name_representant'=>'required',
        'enrollment.relationship'=>'required',
        'enrollment.phone_representant'=>'required',
        'enrollment.email_representant'=>'required|email:rfc',
        'enrollment.recommended_by' => 'required',
        'enrollment.coexistence' => 'required',
        'enrollment.status_transport_private_vehicle' => 'boolean|nullable',
        'enrollment.status_transport_public_vehicle' => 'boolean|nullable',
        'enrollment.status_transport_walking' => 'boolean|nullable',
        'enrollment.status_transport_other' => 'boolean|nullable',
        'enrollment.transport_other' => 'nullable|required_if:enrollment.status_transport_other,true',
        'enrollment.status_vaccination_schedule' => 'boolean|nullable',
        'enrollment.status_sports_potential' => 'boolean|nullable',
        'enrollment.sports_potential' => 'nullable|required_if:enrollment.status_sports_potential,true',
        'enrollment.place_where_he_practices' => 'nullable|required_if:enrollment.status_sports_potential,true',
        'enrollment.status_illness_cardiovascular' => 'boolean|nullable',
        'enrollment.status_illness_cancer' => 'boolean|nullable',
        'enrollment.status_illness_lupus' => 'boolean|nullable',
        'enrollment.status_illness_diabetes' => 'boolean|nullable',
        'enrollment.status_illness_renal_problems' => 'boolean|nullable',
        'enrollment.status_illness_overweight' => 'boolean|nullable',
        'enrollment.status_illness_other' => 'boolean|nullable',
        'enrollment.illness_other' => 'nullable|required_if:enrollment.status_illness_other,true',
        'enrollment.status_conditions_intellectual_disability' => 'nullable|boolean',
        'enrollment.status_conditions_motor_disability' => 'nullable|boolean',
        'enrollment.status_conditions_visual_disability' => 'nullable|boolean',
        'enrollment.status_conditions_hearing_impairment' => 'nullable|boolean',
        'enrollment.status_conditions_outstanding_attitudes' => 'nullable|boolean',
        'enrollment.status_conditions_autism' => 'nullable|boolean',
        'enrollment.status_conditions_other' => 'nullable|boolean',
        'enrollment.conditions_other' => 'nullable|required_if:enrollment.status_conditions_other,true',
        'enrollment.status_treated_by_specialist' => 'nullable|boolean',
        'enrollment.specialist' => 'nullable|required_if:enrollment.status_treated_by_specialist,true',
        'enrollment.status_take_medication' => 'nullable|boolean',
        'enrollment.medication' => 'nullable|required_if:enrollment.status_take_medication,true',
        'enrollment.mother_name' => 'required',
        'enrollment.mother_lastname' => 'required',
        'enrollment.mother_ci' => 'required|digits_between:6,12',
        'enrollment.mother_profession' => 'nullable',
        'enrollment.mother_phones' => 'required|digits_between:6,12',
        'enrollment.mother_address' => 'nullable',
        'enrollment.father_name' => 'required',
        'enrollment.father_lastname' => 'required',
        'enrollment.father_ci' => 'required|digits_between:6,12',
        'enrollment.father_profession' => 'nullable',
        'enrollment.father_phones' => 'required|digits_between:6,12',
        'enrollment.father_address' => 'nullable',
    ];

    protected function validationAttributes()
    {
        return [
            'enrollment.ci_estudiant' => $this->list_comment['ci_estudiant'],
            'enrollment.lastname' => $this->list_comment['lastname'],
            'enrollment.name' => $this->list_comment['name'],
            'enrollment.gender' => $this->list_comment['gender'],
            'enrollment.date_birth' => $this->list_comment['date_birth'],
            'enrollment.town_hall_birth' => $this->list_comment['town_hall_birth'],
            'enrollment.state_birth' => $this->list_comment['state_birth'],
            'enrollment.country_birth' => $this->list_comment['country_birth'],
            'enrollment.dir_address' => $this->list_comment['dir_address'],
            'enrollment.pestudio_id' => $this->list_comment['pestudio_id'],
            'enrollment.grado_id' => $this->list_comment['grado_id'],
            'enrollment.institution' => $this->list_comment['institution'],
            'enrollment.ci_representant' => $this->list_comment['ci_representant'],
            'enrollment.name_representant' => $this->list_comment['name_representant'],
            'enrollment.relationship' => $this->list_comment['relationship'],
            'enrollment.phone_representant' => $this->list_comment['phone_representant'],
            'enrollment.email_representant' => $this->list_comment['email_representant'],
            'enrollment.recommended_by' => $this->list_comment['recommended_by'],
            'enrollment.coexistence' => $this->list_comment['coexistence'],
            'enrollment.status_transport_private_vehicle' => $this->list_comment['status_transport_private_vehicle'],
            'enrollment.status_transport_public_vehicle' => $this->list_comment['status_transport_public_vehicle'],
            'enrollment.status_transport_walking' => $this->list_comment['status_transport_walking'],
            'enrollment.status_transport_other' => $this->list_comment['status_transport_other'],
            'enrollment.transport_other' => $this->list_comment['transport_other'],
            'enrollment.status_vaccination_schedule' => $this->list_comment['status_vaccination_schedule'],
            'enrollment.status_sports_potential' => $this->list_comment['status_sports_potential'],
            'enrollment.sports_potential' => $this->list_comment['sports_potential'],
            'enrollment.place_where_he_practices' => $this->list_comment['place_where_he_practices'],
            'enrollment.status_illness_cardiovascular' => $this->list_comment['status_illness_cardiovascular'],
            'enrollment.status_illness_cancer' => $this->list_comment['status_illness_cancer'],
            'enrollment.status_illness_lupus' => $this->list_comment['status_illness_lupus'],
            'enrollment.status_illness_diabetes' => $this->list_comment['status_illness_diabetes'],
            'enrollment.status_illness_renal_problems' => $this->list_comment['status_illness_renal_problems'],
            'enrollment.status_illness_overweight' => $this->list_comment['status_illness_overweight'],
            'enrollment.status_illness_other' => $this->list_comment['status_illness_other'],
            'enrollment.illness_other' => $this->list_comment['illness_other'],
            'enrollment.status_conditions_intellectual_disability' => $this->list_comment['status_conditions_intellectual_disability'],
            'enrollment.status_conditions_motor_disability' => $this->list_comment['status_conditions_motor_disability'],
            'enrollment.status_conditions_visual_disability' => $this->list_comment['status_conditions_visual_disability'],
            'enrollment.status_conditions_hearing_impairment' => $this->list_comment['status_conditions_hearing_impairment'],
            'enrollment.status_conditions_outstanding_attitudes' => $this->list_comment['status_conditions_outstanding_attitudes'],
            'enrollment.status_conditions_autism' => $this->list_comment['status_conditions_autism'],
            'enrollment.status_conditions_other' => $this->list_comment['status_conditions_other'],
            'enrollment.conditions_other' => $this->list_comment['conditions_other'],
            'enrollment.status_treated_by_specialist' => $this->list_comment['status_treated_by_specialist'],
            'enrollment.specialist' => $this->list_comment['specialist'],
            'enrollment.status_take_medication' => $this->list_comment['status_take_medication'],
            'enrollment.medication' => $this->list_comment['medication'],
            'enrollment.mother_name' => $this->list_comment['mother_name'],
            'enrollment.mother_lastname' => $this->list_comment['mother_lastname'],
            'enrollment.mother_ci' => $this->list_comment['mother_ci'],
            'enrollment.mother_profession' => $this->list_comment['mother_profession'],
            'enrollment.mother_phones' => $this->list_comment['mother_phones'],
            'enrollment.mother_address' => $this->list_comment['mother_address'],
            'enrollment.father_name' => $this->list_comment['father_name'],
            'enrollment.father_lastname' => $this->list_comment['father_lastname'],
            'enrollment.father_ci' => $this->list_comment['father_ci'],
            'enrollment.father_profession' => $this->list_comment['father_profession'],
            'enrollment.father_phones' => $this->list_comment['father_phones'],
            'enrollment.father_address' => $this->list_comment['father_address'],
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
