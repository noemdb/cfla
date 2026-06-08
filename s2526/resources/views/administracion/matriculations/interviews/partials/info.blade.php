<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="h4">I.- Datos del Representante</div>
        </div>
    </div>

    <div class="row border-bottom mb-2 pb-1">
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['full_name'] ?? null}}</span>:
            <span class="font-weight-lighter"> {{ $catchment_interview->full_name ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['identification_number'] ?? null}}</span>:
            <span class="font-weight-lighter"> {{ $catchment_interview->identification_number ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['age'] ?? null}}</span>:
            <span class="font-weight-lighter"> {{ $catchment_interview->age ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['relationship'] ?? null}}</span>:
            <span class="font-weight-lighter"> {{ $catchment_interview->relationship ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['phone_numbers'] ?? null}}</span>:
            <span class="font-weight-lighter"> {{ $catchment_interview->phone_numbers ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['email'] ?? null}}</span>:
            <span class="font-weight-lighter"> {{ $catchment_interview->email ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['profession_occupation'] ?? null}}</span>:
            <span class="font-weight-lighter"> {{ $catchment_interview->profession_occupation ?? null}}</span>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="h4">II.- Datos del Estudiante Aspirante a Cupo</div>
        </div>
    </div>

    <div class="row border-bottom mb-2 pb-1">
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['student_full_name'] ?? null}}</span>:
            <span class="font-weight-lighter">{{ $catchment_interview->student_full_name ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['date_of_birth'] ?? null}}</span>:
            <span class="font-weight-lighter">{{ $catchment_interview->date_of_birth ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['student_age'] ?? null}}</span>:
            <span class="font-weight-lighter">{{ $catchment_interview->student_age ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            @php $grado = $catchment_interview->grado; @endphp
            <span class="font-weight-bold">{{$list_comment['grade_year_aspiring'] ?? null}}</span>:
            <span class="font-weight-lighter">{{ $grado->name ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['has_siblings'] ?? null}}</span>:
            <span class="font-weight-lighter">{{ $catchment_interview->has_siblings ? 'Sí' : 'No' ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['sibling_name'] ?? null}}</span>:
            <span class="font-weight-lighter">{{ $catchment_interview->sibling_name ?? null}}</span>
        </div>
        <div class="col-12">
            <span class="font-weight-bold">Grado/Año del hermano/a:</span>
            <span class="font-weight-lighter">{{ $catchment_interview->sibling_grade_section ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['tutor_teacher_name'] ?? null}}</span>:
            <span class="font-weight-lighter">{{ $catchment_interview->tutor_teacher_name ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['tutor_teacher_phone'] ?? null}}</span>:
            <span class="font-weight-lighter">{{ $catchment_interview->tutor_teacher_phone ?? null}}</span>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="h4">III.- Información sobre quién vive con el representado</div>
        </div>
    </div>

    <div class="row border-bottom mb-2 pb-1">
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['living_with'] ?? null}}</span>:
            <span class="field-vaule">{{ $catchment_interview->living_with ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['other_person_origin'] ?? null}}</span>:
            <span class="field-vaule">{{ $catchment_interview->other_person_origin ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['reason_for_living_with_other'] ?? null}}</span>:
            <span class="field-vaule">{{ $catchment_interview->reason_for_living_with_other ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['num_family_group_members'] ?? null}}</span>:
            <span class="field-vaule">{{ $catchment_interview->num_family_group_members ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['num_people_financially_dependent'] ?? null}}</span>:
            <span class="field-vaule">{{ $catchment_interview->num_people_financially_dependent ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['person_responsible_attending'] ?? null}}</span>:
            <span class="field-vaule">{{ $catchment_interview->person_responsible_attending ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['place_person_responsible_attending'] ?? null}}</span>:
            <span class="field-vaule">{{ $catchment_interview->place_person_responsible_attending ?? null}}</span>
        </div>
        <div class="col-12 col-md-6">
            <span class="font-weight-bold">{{$list_comment['position_person_responsible_attending'] ?? null}}</span>:

            <span class="field-vaule">{{ $catchment_interview->position_person_responsible_attending ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['work_person_responsible_attending'] ?? null}}</span>:
        <span class="field-vaule">{{ $catchment_interview->work_person_responsible_attending ?? null}}</span>
    </div>
    </div>

    <div class="row">
    <div class="col-12">
        <div class="h4">IV.- Aspectos Socio-Económicos</div>
    </div>
    </div>

    <div class="row border-bottom mb-2 pb-1">
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['monthly_income'] ?? null}}:</span>
        <span class="field-valus">{{ $catchment_interview->monthly_income ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['num_people_contributing'] ?? null}}:</span>
        <span class="field-valus">{{ $catchment_interview->num_people_contributing ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['income_source'] ?? null}}:</span>
        <span class="field-valus">{{ $catchment_interview->income_source ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['able_to_pay_dollars'] ?? null}}:</span>
        <span class="field-valus">{{ $catchment_interview->able_to_pay_dollars ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['able_to_pay_bolivars'] ?? null}}:</span>
        <span class="field-valus">{{ $catchment_interview->able_to_pay_bolivars ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['has_payment_responsible'] ?? null}}:</span>
        <span class="field-valus">{{ $catchment_interview->has_payment_responsible ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['person_guarantor_name_phone'] ?? null}}:</span>
        <span class="field-valus">{{ $catchment_interview->person_guarantor_name_phone ?? null}}</span>
    </div>
    </div>

    <div class="row">
    <div class="col-12">
        <div class="h4">V.- Aspectos Institucionales</div>
    </div>
    </div>

    <div class="row border-bottom mb-2 pb-1">
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['knowledge_of_school'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->knowledge_of_school ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['studied_at_school'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->studied_at_school ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['year_of_graduation'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->year_of_graduation ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['academic_director'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->academic_director ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['school_director'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->school_director ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['teachers_worked_at_school'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->teachers_worked_at_school ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['reason_for_choosing_institution'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->reason_for_choosing_institution ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['recommendation_from_school'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->recommendation_from_school ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['recommender_name'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->recommender_name ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['recommender_affinity'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->recommender_affinity ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['recommender_phone'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->recommender_phone ?? null}}</span>
    </div>
    </div>

    <div class="row">
    <div class="col-12">
        <div class="h4">VI.- Acuerdos</div>
    </div>
    </div>

    <div class="row border-bottom mb-2 pb-1">
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['agreement_to_code_of_conduct'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->agreement_to_code_of_conduct ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['respect_communication_channels'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->respect_communication_channels ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['ensure_compliance_with_school_activities'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->ensure_compliance_with_school_activities ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12col-md-6">
        <span class="font-weight-bold">{{$list_comment['comply_with_school_uniform'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->comply_with_school_uniform ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['respect_authorities_directives'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->respect_authorities_directives ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['pay_installments_on_time'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->pay_installments_on_time ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['respect_parent_assembly_agreements'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->respect_parent_assembly_agreements ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['pay_overdue_installments'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->pay_overdue_installments ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['family_member_studied_worked_at_school'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->family_member_studied_worked_at_school ? 'Sí' : 'No' ?? null}}</span>
    </div>
    </div>

    <div class="row">
    <div class="col-12">
        <div class="h4">VII.- Aspectos Religiosos</div>
    </div>
    </div>

    <div class="row border-bottom mb-2 pb-1">
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['religion'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->religion ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['awareness_of_catholic_school_affiliation'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->awareness_of_catholic_school_affiliation ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['agreement_to_catholic_formation'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->agreement_to_catholic_formation ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12 col-md-6">
        <span class="font-weight-bold">{{$list_comment['agreement_to_participate_in_catholic_activities'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->agreement_to_participate_in_catholic_activities ? 'Sí' : 'No' ?? null}}</span>
    </div>
    <div class="col-12">
        <span class="font-weight-bold">{{$list_comment['justification_for_not_participating_in_catholic_activities'] ?? null}}:</span>
        <span class="font-weight-lighter">{{ $catchment_interview->justification_for_not_participating_in_catholic_activities ?? null}}</span>
    </div>
    </div>

</div>