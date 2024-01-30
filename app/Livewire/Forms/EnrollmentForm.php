<?php

namespace App\Livewire\Forms;

use App\Models\app\Academy\Enrollment;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EnrollmentForm extends Form
{
    public $list_comment = '';

    #[Validate]
    public $user_id,$ci_estudiant,$lastname,$name,$photo,$gender,$date_birth,$town_hall_birth,$state_birth,$country_birth,$dir_address,$pestudio_id,$grado_id,$institution,$pending_matter,$literal,$grupo_estable_id,$age,$blood_type,$weight,$height,$laterality,$order_born,$group_family,$status_brother,$ci_representant,$name_representant,$relationship,$phone_representant,$cellphone_representant,$profession_representant,$email_representant,$recommended_by,$coexistence,$status_transport_private_vehicle,$status_transport_public_vehicle,$status_transport_walking,$status_transport_other,$transport_other,$status_vaccination_schedule,$status_sports_potential,$sports_potential,$place_where_he_practices,$status_illness_cardiovascular,$status_illness_cancer,$status_illness_lupus,$status_illness_diabetes,$status_illness_renal_problems,$status_illness_overweight,$status_illness_other,$illness_other,$status_conditions_intellectual_disability,$status_conditions_motor_disability,$status_conditions_visual_disability,$status_conditions_hearing_impairment,$status_conditions_outstanding_attitudes,$status_conditions_autism,$status_conditions_other,$conditions_other,$status_treated_by_specialist,$specialist,$status_take_medication,$medication,$mother_name,$mother_lastname,$mother_ci,$mother_profession,$mother_phones,$mother_address,$father_name,$father_lastname,$father_ci,$father_profession,$father_phones,$father_address;

    protected $rules = [
        // 'user_id'=>'required|integer',
        'ci_estudiant'=>'required|unique:enrollments,ci_estudiant|digits_between:6,12',
        'lastname'=>'required',
        'name'=>'required',
        'photo'=>'nullable',
        'gender'=>'required',
        'date_birth'=>'required',
        'town_hall_birth'=>'nullable',
        'state_birth'=>'nullable',
        'country_birth'=>'nullable',
        'dir_address'=>'nullable:string',
        'grado_id'=>'required|integer',
        'pestudio_id'=>'required|integer',
        'institution'=>'required',
        'pending_matter'=>'nullable',
        'literal'=>'nullable',
        'grupo_estable_id'=>'nullable',
        'age'=>'required|integer',
        'blood_type'=>'required',
        'weight'=>'required|integer',
        'height'=>'required|integer',
        'laterality'=>'required',
        'order_born'=>'required|integer',
        'group_family'=>'required|integer',
        'status_brother'=>'required',
        'ci_representant'=>'required',
        'name_representant'=>'required',
        'relationship'=>'required',
        'phone_representant'=>'required',
        'cellphone_representant'=>'nullable',
        'profession_representant'=>'required',
        'email_representant'=>'required|email:rfc',
        'recommended_by' => 'required',
        'coexistence' => 'required',
        'status_transport_private_vehicle' => 'boolean|nullable',
        'status_transport_public_vehicle' => 'boolean|nullable',
        'status_transport_walking' => 'boolean|nullable',
        'status_transport_other' => 'boolean|nullable',
        'transport_other' => 'nullable|required_if:status_transport_other,true',
        'status_vaccination_schedule' => 'boolean|nullable',
        'status_sports_potential' => 'boolean|nullable',
        'sports_potential' => 'nullable|required_if:status_sports_potential,true',
        'place_where_he_practices' => 'nullable|required_if:status_sports_potential,true',
        'status_illness_cardiovascular' => 'boolean|nullable',
        'status_illness_cancer' => 'boolean|nullable',
        'status_illness_lupus' => 'boolean|nullable',
        'status_illness_diabetes' => 'boolean|nullable',
        'status_illness_renal_problems' => 'boolean|nullable',
        'status_illness_overweight' => 'boolean|nullable',
        'status_illness_other' => 'boolean|nullable',
        'illness_other' => 'nullable|required_if:status_illness_other,true',
        'status_conditions_intellectual_disability' => 'nullable|boolean',
        'status_conditions_motor_disability' => 'nullable|boolean',
        'status_conditions_visual_disability' => 'nullable|boolean',
        'status_conditions_hearing_impairment' => 'nullable|boolean',
        'status_conditions_outstanding_attitudes' => 'nullable|boolean',
        'status_conditions_autism' => 'nullable|boolean',
        'status_conditions_other' => 'nullable|boolean',
        'conditions_other' => 'nullable|required_if:status_conditions_other,true',
        'status_treated_by_specialist' => 'nullable|boolean',
        'specialist' => 'nullable|required_if:status_treated_by_specialist,true',
        'status_take_medication' => 'nullable|boolean',
        'medication' => 'nullable|required_if:status_take_medication,true',
        'mother_name' => 'required',
        'mother_lastname' => 'required',
        'mother_ci' => 'required|digits_between:6,12',
        'mother_profession' => 'required',
        'mother_phones' => 'required|digits_between:6,12',
        'mother_address' => 'nullable',
        'father_name' => 'required',
        'father_lastname' => 'required',
        'father_ci' => 'required|digits_between:6,12',
        'father_profession' => 'required',
        'father_phones' => 'required|digits_between:6,12',
        'father_address' => 'nullable',
    ];

    protected function validationAttributes()
    {   
        $this->list_comment = Enrollment::COLUMN_COMMENTS;
        return [
            'ci_estudiant' => $this->list_comment['ci_estudiant'],
            'lastname' => $this->list_comment['lastname'],
            'name' => $this->list_comment['name'],
            'photo' => $this->list_comment['photo'],
            'gender' => $this->list_comment['gender'],
            'date_birth' => $this->list_comment['date_birth'],
            'town_hall_birth' => $this->list_comment['town_hall_birth'],
            'state_birth' => $this->list_comment['state_birth'],
            'country_birth' => $this->list_comment['country_birth'],
            'dir_address' => $this->list_comment['dir_address'],
            'pestudio_id' => $this->list_comment['pestudio_id'],
            'grado_id' => $this->list_comment['grado_id'],
            'institution' => $this->list_comment['institution'],
            'pending_matter' => $this->list_comment['pending_matter'],
            'literal' => $this->list_comment['literal'],
            'grupo_estable_id' => $this->list_comment['grupo_estable_id'],
            'age' => $this->list_comment['age'],
            'blood_type' => $this->list_comment['blood_type'],
            'weight' => $this->list_comment['weight'],
            'height' => $this->list_comment['height'],
            'laterality' => $this->list_comment['laterality'],
            'order_born' => $this->list_comment['order_born'],
            'group_family' => $this->list_comment['group_family'],
            'status_brother' => $this->list_comment['status_brother'],
            'ci_representant' => $this->list_comment['ci_representant'],
            'name_representant' => $this->list_comment['name_representant'],
            'relationship' => $this->list_comment['relationship'],
            'profession_representant' => $this->list_comment['profession_representant'],
            'phone_representant' => $this->list_comment['phone_representant'],
            'cellphone_representant' => $this->list_comment['cellphone_representant'],
            'email_representant' => $this->list_comment['email_representant'],
            'recommended_by' => $this->list_comment['recommended_by'],
            'coexistence' => $this->list_comment['coexistence'],
            'status_transport_private_vehicle' => $this->list_comment['status_transport_private_vehicle'],
            'status_transport_public_vehicle' => $this->list_comment['status_transport_public_vehicle'],
            'status_transport_walking' => $this->list_comment['status_transport_walking'],
            'status_transport_other' => $this->list_comment['status_transport_other'],
            'transport_other' => $this->list_comment['transport_other'],
            'status_vaccination_schedule' => $this->list_comment['status_vaccination_schedule'],
            'status_sports_potential' => $this->list_comment['status_sports_potential'],
            'sports_potential' => $this->list_comment['sports_potential'],
            'place_where_he_practices' => $this->list_comment['place_where_he_practices'],
            'status_illness_cardiovascular' => $this->list_comment['status_illness_cardiovascular'],
            'status_illness_cancer' => $this->list_comment['status_illness_cancer'],
            'status_illness_lupus' => $this->list_comment['status_illness_lupus'],
            'status_illness_diabetes' => $this->list_comment['status_illness_diabetes'],
            'status_illness_renal_problems' => $this->list_comment['status_illness_renal_problems'],
            'status_illness_overweight' => $this->list_comment['status_illness_overweight'],
            'status_illness_other' => $this->list_comment['status_illness_other'],
            'illness_other' => $this->list_comment['illness_other'],
            'status_conditions_intellectual_disability' => $this->list_comment['status_conditions_intellectual_disability'],
            'status_conditions_motor_disability' => $this->list_comment['status_conditions_motor_disability'],
            'status_conditions_visual_disability' => $this->list_comment['status_conditions_visual_disability'],
            'status_conditions_hearing_impairment' => $this->list_comment['status_conditions_hearing_impairment'],
            'status_conditions_outstanding_attitudes' => $this->list_comment['status_conditions_outstanding_attitudes'],
            'status_conditions_autism' => $this->list_comment['status_conditions_autism'],
            'status_conditions_other' => $this->list_comment['status_conditions_other'],
            'conditions_other' => $this->list_comment['conditions_other'],
            'status_treated_by_specialist' => $this->list_comment['status_treated_by_specialist'],
            'specialist' => $this->list_comment['specialist'],
            'status_take_medication' => $this->list_comment['status_take_medication'],
            'medication' => $this->list_comment['medication'],
            'mother_name' => $this->list_comment['mother_name'],
            'mother_lastname' => $this->list_comment['mother_lastname'],
            'mother_ci' => $this->list_comment['mother_ci'],
            'mother_profession' => $this->list_comment['mother_profession'],
            'mother_phones' => $this->list_comment['mother_phones'],
            'mother_address' => $this->list_comment['mother_address'],
            'father_name' => $this->list_comment['father_name'],
            'father_lastname' => $this->list_comment['father_lastname'],
            'father_ci' => $this->list_comment['father_ci'],
            'father_profession' => $this->list_comment['father_profession'],
            'father_phones' => $this->list_comment['father_phones'],
            'father_address' => $this->list_comment['father_address'],
        ];
    }
}


/*
user_id
--------------------------------------
ci_estudiant
lastname
name
gender
date_birth
town_hall_birth
state_birth
country_birth
dir_address
grado_id
pestudio_id
institution
pending_matter
literal
grupo_estable_id
--------------------------------------
age
blood_type
weight
height
laterality
order_born
group_family
status_brother
--------------------------------------
ci_representant
name_representant
relationship
profession_representant
phone_representant
email_representant
recommended_by
--------------------------------------
coexistence
status_transport_private_vehicle
status_transport_public_vehicle
status_transport_walking
status_transport_other
transport_other
--------------------------------------
status_vaccination_schedule
status_sports_potential
sports_potential
place_where_he_practices
--------------------------------------
status_illness_cardiovascular
status_illness_cancer
status_illness_lupus
status_illness_diabetes
status_illness_renal_problems
status_illness_overweight
status_illness_other
illness_other
--------------------------------------
status_conditions_intellectual_disability
status_conditions_motor_disability
status_conditions_visual_disability
status_conditions_hearing_impairment
status_conditions_outstanding_attitudes
status_conditions_autism
status_conditions_other
conditions_other
--------------------------------------
status_treated_by_specialist
specialist
status_take_medication
medication
--------------------------------------
mother_name
mother_lastname
mother_ci
mother_profession
mother_phones
mother_address
father_name
father_lastname
father_ci
father_profession
father_phones
father_address
--------------------------------------
cellphone
cellphone_representant
twitter
instagram
*/