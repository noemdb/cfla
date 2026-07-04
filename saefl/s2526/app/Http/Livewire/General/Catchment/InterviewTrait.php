<?php

namespace App\Http\Livewire\General\Catchment;

trait InterviewTrait
{
    protected $rules = [
        'catchment.representant_ci'=>'required:string',
        
        //////////////////////////////////////////
        'catchment_interview.catchment_id'=>'nullable|integer',
        'catchment_interview.full_name' => 'required|string',
        'catchment_interview.identification_number' => 'required|string',
        'catchment_interview.age' => 'required|integer',
        'catchment_interview.relationship' => 'required|string',
        'catchment_interview.phone_numbers' => 'required|string',
        'catchment_interview.email' => 'required|email',
        'catchment_interview.email_alternate' => 'required|email',
        'catchment_interview.profession_occupation' => 'required|string',
        'catchment_interview.student_full_name' => 'required|string',
        'catchment_interview.date_of_birth' => 'required|date',
        'catchment_interview.student_age' => 'required|integer',
        'catchment_interview.grade_year_aspiring' => 'required|integer',
        'catchment_interview.has_siblings' => 'nullable|boolean',
        'catchment_interview.sibling_name' => 'nullable|string',
        'catchment_interview.sibling_name_2' => 'nullable|string',
        'catchment_interview.sibling_name_3' => 'nullable|string',
        'catchment_interview.sibling_grade_section' => 'nullable|integer',
        'catchment_interview.tutor_teacher_name' => 'nullable|string',
        'catchment_interview.tutor_teacher_phone' => 'nullable|string',
        'catchment_interview.living_with' => 'required|string',
        'catchment_interview.other_person_origin' => 'nullable|string',
        'catchment_interview.reason_for_living_with_other' => 'nullable|string',
        'catchment_interview.num_family_group_members' => 'required|integer',
        'catchment_interview.num_people_financially_dependent' => 'required|integer',
        'catchment_interview.person_responsible_attending' => 'required|string',
        'catchment_interview.place_person_responsible_attending' => 'required|string',
        'catchment_interview.position_person_responsible_attending' => 'nullable|string',
        'catchment_interview.work_person_responsible_attending' => 'nullable|string',
        'catchment_interview.monthly_income' => 'required|string',
        'catchment_interview.num_people_contributing' => 'required|integer',
        'catchment_interview.income_source' => 'required|string',
        'catchment_interview.able_to_pay_dollars' => 'nullable|boolean',
        'catchment_interview.able_to_pay_bolivars' => 'nullable|boolean',
        'catchment_interview.has_payment_responsible' => 'nullable|boolean',
        'catchment_interview.person_guarantor_name_phone' => 'nullable|string',
        'catchment_interview.knowledge_of_school' => 'nullable|string',
        'catchment_interview.studied_at_school' => 'nullable|boolean',
        'catchment_interview.year_of_graduation' => 'nullable|integer',
        'catchment_interview.academic_director' => 'nullable|string',
        'catchment_interview.school_director' => 'nullable|string',
        'catchment_interview.teachers_worked_at_school' => 'nullable|string',
        'catchment_interview.reason_for_choosing_institution' => 'required|string',
        'catchment_interview.recommendation_from_school' => 'nullable|boolean',
        'catchment_interview.recommender_name' => 'nullable|string',
        'catchment_interview.recommender_affinity' => 'nullable|string',
        'catchment_interview.recommender_phone' => 'nullable|string',
        'catchment_interview.agreement_to_code_of_conduct' => 'nullable|boolean',
        'catchment_interview.respect_communication_channels' => 'nullable|boolean',
        'catchment_interview.ensure_compliance_with_school_activities' => 'nullable|boolean',
        'catchment_interview.comply_with_school_uniform' => 'nullable|boolean',
        'catchment_interview.respect_authorities_directives' => 'nullable|boolean',
        'catchment_interview.pay_installments_on_time' => 'nullable|boolean',
        'catchment_interview.respect_parent_assembly_agreements' => 'nullable|boolean',
        'catchment_interview.pay_overdue_installments' => 'nullable|boolean',
        'catchment_interview.family_member_studied_worked_at_school' => 'nullable|boolean',
        'catchment_interview.religion' => 'nullable|string',
        'catchment_interview.awareness_of_catholic_school_affiliation' => 'nullable|boolean',
        'catchment_interview.agreement_to_participate_in_catholic_activities' => 'nullable|boolean',
        'catchment_interview.justification_for_not_participating_in_catholic_activities' => 'nullable|string',
        'catchment_interview.agreement_to_catholic_formation' => 'nullable|boolean',
    ];

// has_payment_responsible
// person_guarantor_name_phone (person_responsible_debt_payment)

// religion
// awareness_of_catholic_school_affiliation
// agreement_to_participate_in_catholic_activities
// justification_for_not_participating_in_catholic_activities

    // protected $messages = [
    //     'catchment.group_id.required' => 'No hay cupo para el nivel/grado/año seleccionado.',
    // ];

    protected function validationAttributes()
    {
        return [
            'catchment.representant_ci' => $this->list_comment_catchment['representant_ci'],

            ////////////////////////////////////////////////////
            'catchment_interview.catchment_id' => $this->list_comment['catchment_id'],
            'catchment_interview.full_name' => $this->list_comment['full_name'],
            'catchment_interview.identification_number' => $this->list_comment['identification_number'],
            'catchment_interview.age' => $this->list_comment['age'],
            'catchment_interview.relationship' => $this->list_comment['relationship'],
            'catchment_interview.phone_numbers' => $this->list_comment['phone_numbers'],
            'catchment_interview.email' => $this->list_comment['email'],
            'catchment_interview.profession_occupation' => $this->list_comment['profession_occupation'],
            'catchment_interview.student_full_name' => $this->list_comment['student_full_name'],
            'catchment_interview.date_of_birth' => $this->list_comment['date_of_birth'],
            'catchment_interview.student_age' => $this->list_comment['student_age'],
            'catchment_interview.grade_year_aspiring' => $this->list_comment['grade_year_aspiring'],
            'catchment_interview.has_siblings' => $this->list_comment['has_siblings'],
            'catchment_interview.sibling_name' => $this->list_comment['sibling_name'],
            'catchment_interview.sibling_name_2' => $this->list_comment['sibling_name_2'],
            'catchment_interview.sibling_name_3' => $this->list_comment['sibling_name_3'],
            'catchment_interview.sibling_grade_section' => $this->list_comment['sibling_grade_section'],
            'catchment_interview.tutor_teacher_name' => $this->list_comment['tutor_teacher_name'],
            'catchment_interview.tutor_teacher_phone' => $this->list_comment['tutor_teacher_phone'],
            'catchment_interview.living_with' => $this->list_comment['living_with'],
            'catchment_interview.other_person_origin' => $this->list_comment['other_person_origin'],
            'catchment_interview.reason_for_living_with_other' => $this->list_comment['reason_for_living_with_other'],
            'catchment_interview.num_family_group_members' => $this->list_comment['num_family_group_members'],
            'catchment_interview.num_people_financially_dependent' => $this->list_comment['num_people_financially_dependent'],
            'catchment_interview.person_responsible_attending' => $this->list_comment['person_responsible_attending'],
            'catchment_interview.place_person_responsible_attending' => $this->list_comment['place_person_responsible_attending'],
            'catchment_interview.position_person_responsible_attending' => $this->list_comment['position_person_responsible_attending'],
            'catchment_interview.work_person_responsible_attending' => $this->list_comment['work_person_responsible_attending'],
            'catchment_interview.monthly_income' => $this->list_comment['monthly_income'],
            'catchment_interview.num_people_contributing' => $this->list_comment['num_people_contributing'],
            'catchment_interview.income_source' => $this->list_comment['income_source'],
            'catchment_interview.able_to_pay_dollars' => $this->list_comment['able_to_pay_dollars'],
            'catchment_interview.able_to_pay_bolivars' => $this->list_comment['able_to_pay_bolivars'],
            'catchment_interview.has_payment_responsible' => $this->list_comment['has_payment_responsible'],
            'catchment_interview.person_guarantor_name_phone' => $this->list_comment['person_guarantor_name_phone'],
            'catchment_interview.knowledge_of_school' => $this->list_comment['knowledge_of_school'],
            'catchment_interview.studied_at_school' => $this->list_comment['studied_at_school'],
            'catchment_interview.year_of_graduation' => $this->list_comment['year_of_graduation'],
            'catchment_interview.academic_director' => $this->list_comment['academic_director'],
            'catchment_interview.school_director' => $this->list_comment['school_director'],
            'catchment_interview.teachers_worked_at_school' => $this->list_comment['teachers_worked_at_school'],
            'catchment_interview.reason_for_choosing_institution' => $this->list_comment['reason_for_choosing_institution'],
            'catchment_interview.recommendation_from_school' => $this->list_comment['recommendation_from_school'],
            'catchment_interview.recommender_name' => $this->list_comment['recommender_name'],
            'catchment_interview.recommender_affinity' => $this->list_comment['recommender_affinity'],
            'catchment_interview.recommender_phone' => $this->list_comment['recommender_phone'],
            'catchment_interview.agreement_to_code_of_conduct' => $this->list_comment['agreement_to_code_of_conduct'],
            'catchment_interview.respect_communication_channels' => $this->list_comment['respect_communication_channels'],
            'catchment_interview.ensure_compliance_with_school_activities' => $this->list_comment['ensure_compliance_with_school_activities'],
            'catchment_interview.comply_with_school_uniform' => $this->list_comment['comply_with_school_uniform'],
            'catchment_interview.respect_authorities_directives' => $this->list_comment['respect_authorities_directives'],
            'catchment_interview.pay_installments_on_time' => $this->list_comment['pay_installments_on_time'],
            'catchment_interview.respect_parent_assembly_agreements' => $this->list_comment['respect_parent_assembly_agreements'],
            'catchment_interview.pay_overdue_installments' => $this->list_comment['pay_overdue_installments'],
            'catchment_interview.family_member_studied_worked_at_school' => $this->list_comment['family_member_studied_worked_at_school'],
            'catchment_interview.religion' => $this->list_comment['religion'],
            'catchment_interview.awareness_of_catholic_school_affiliation' => $this->list_comment['awareness_of_catholic_school_affiliation'],
            'catchment_interview.agreement_to_participate_in_catholic_activities' => $this->list_comment['agreement_to_participate_in_catholic_activities'],
            'catchment_interview.justification_for_not_participating_in_catholic_activities' => $this->list_comment['justification_for_not_participating_in_catholic_activities'],
            'catchment_interview.agreement_to_catholic_formation' => $this->list_comment['agreement_to_catholic_formation'],
            
            
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
