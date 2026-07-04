<div class="catchment-interview-section">
    <div class="table-container">
        <table class="table table-sm small">
            <tbody>
                {{-- Helper inline para formatear booleanos --}}
                @php
                    function formatBoolean($value) {
                        if ($value === null || $value === '') return '';
                        return ((int)$value === 1) ? 'Sí' : 'No';
                    }
                @endphp

                <tr>
                    <td colspan="1" class="field-label">
                        <div class="tr_strong">I.- Datos del Representante</div>
                    </td>
                </tr>

                <tr>
                    <td><span class="field-label">{{ $list_comment['full_name'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->full_name ?? null }}</span></td>
                </tr>
                <tr>
                    <td><span class="field-label">{{ $list_comment['identification_number'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->identification_number ?? null }}</span></td>
                </tr>
                <tr>
                    <td><span class="field-label">{{ $list_comment['age'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->age ?? null }}</span></td>
                </tr>
                <tr>
                    <td><span class="field-label">{{ $list_comment['relationship'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->relationship ?? null }}</span></td>
                </tr>
                <tr>
                    <td><span class="field-label">{{ $list_comment['phone_numbers'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->phone_numbers ?? null }}</span></td>
                </tr>
                <tr>
                    <td><span class="field-label">{{ $list_comment['email'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->email ?? null }}</span></td>
                </tr>
                <tr>
                    <td><span class="field-label">{{ $list_comment['profession_occupation'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->profession_occupation ?? null }}</span></td>
                </tr>

                <tr>
                    <td colspan="1" class="field-label">
                        <div class="tr_strong">II.- Datos del Estudiante Aspirante a Cupo</div>
                    </td>
                </tr>

                <tr>
                    <td><span class="field-label">{{ $list_comment['student_full_name'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->student_full_name ?? null }}</span></td>
                </tr>
                <tr>
                    <td><span class="field-label">{{ $list_comment['date_of_birth'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->date_of_birth ?? null }}</span></td>
                </tr>
                <tr>
                    <td><span class="field-label">{{ $list_comment['student_age'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->student_age ?? null }}</span></td>
                </tr>
                <tr>
                    @php $grado = $catchment_interview->grado @endphp
                    <td><span class="field-label">{{ $list_comment['grade_year_aspiring'] ?? null }}</span>: <span class="field-value">{{ $grado->name ?? null }}</span></td>
                </tr>
                <tr>
                    <td><span class="field-label">{{ $list_comment['has_siblings'] ?? null }}</span>: <span class="field-value">{{ formatBoolean($catchment_interview->has_siblings) }}</span></td>
                </tr>
                <tr>
                    <td><span class="field-label">{{ $list_comment['sibling_name'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->sibling_name ?? null }}</span></td>
                </tr>
                <tr>
                    @php $grado_sibling = $catchment_interview->grado_sibling @endphp
                    <td><span class="field-label">Grado/Año del hermano/a:</span>: <span class="field-value">{{ $grado_sibling->name ?? null }}</span></td>
                </tr>
                <tr>
                    <td><span class="field-label">{{ $list_comment['sibling_name_2'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->sibling_name_2 ?? null }}</span></td>
                </tr>
                <tr>
                    <td><span class="field-label">{{ $list_comment['sibling_name_3'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->sibling_name_3 ?? null }}</span></td>
                </tr>
                <tr>
                    <td><span class="field-label">{{ $list_comment['tutor_teacher_name'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->tutor_teacher_name ?? null }}</span></td>
                </tr>
                <tr>
                    <td><span class="field-label">{{ $list_comment['tutor_teacher_phone'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->tutor_teacher_phone ?? null }}</span></td>
                </tr>

                <tr>
                    <td colspan="1" class="field-label">
                        <div class="tr_strong">III.- Información sobre quién vive con el representado</div>
                    </td>
                </tr>

                <tr><td><span class="field-label">{{ $list_comment['living_with'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->living_with ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['other_person_origin'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->other_person_origin ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['reason_for_living_with_other'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->reason_for_living_with_other ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['num_family_group_members'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->num_family_group_members ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['num_people_financially_dependent'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->num_people_financially_dependent ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['person_responsible_attending'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->person_responsible_attending ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['place_person_responsible_attending'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->place_person_responsible_attending ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['position_person_responsible_attending'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->position_person_responsible_attending ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['work_person_responsible_attending'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->work_person_responsible_attending ?? null }}</span></td></tr>

                <tr>
                    <td colspan="1" class="field-label">
                        <div class="tr_strong">IV.- Aspectos Socioeconómicos</div>
                    </td>
                </tr>

                <tr><td><span class="field-label">{{ $list_comment['monthly_income'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->monthly_income ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['num_people_contributing'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->num_people_contributing ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['income_source'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->income_source ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['able_to_pay_dollars'] ?? null }}</span>: <span class="field-value">{{ formatBoolean($catchment_interview->able_to_pay_dollars) }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['able_to_pay_bolivars'] ?? null }}</span>: <span class="field-value">{{ formatBoolean($catchment_interview->able_to_pay_bolivars) }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['has_payment_responsible'] ?? null }}</span>: <span class="field-value">{{ formatBoolean($catchment_interview->has_payment_responsible) }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['person_guarantor_name_phone'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->person_guarantor_name_phone ?? null }}</span></td></tr>

                <tr>
                    <td colspan="1" class="field-label">
                        <div class="tr_strong">V.- Aspectos Institucionales</div>
                    </td>
                </tr>

                <tr><td><span class="field-label">{{ $list_comment['knowledge_of_school'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->knowledge_of_school ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['studied_at_school'] ?? null }}</span>: <span class="field-value">{{ formatBoolean($catchment_interview->studied_at_school) }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['year_of_graduation'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->year_of_graduation ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['academic_director'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->academic_director ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['school_director'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->school_director ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['teachers_worked_at_school'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->teachers_worked_at_school ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['reason_for_choosing_institution'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->reason_for_choosing_institution ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['recommendation_from_school'] ?? null }}</span>: <span class="field-value">{{ formatBoolean($catchment_interview->recommendation_from_school) }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['recommender_name'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->recommender_name ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['recommender_affinity'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->recommender_affinity ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['recommender_phone'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->recommender_phone ?? null }}</span></td></tr>
                <tr><td><span class="field-label">{{ $list_comment['family_member_studied_worked_at_school'] ?? null }}</span>: <span class="field-value">{{ formatBoolean($catchment_interview->family_member_studied_worked_at_school) }}</span></td></tr>

                <tr>
                    <td colspan="1" class="field-label">
                        <div class="tr_strong">VI.- Acuerdos</div>
                    </td>
                </tr>

                @foreach([
                    'agreement_to_code_of_conduct',
                    'respect_communication_channels',
                    'ensure_compliance_with_school_activities',
                    'comply_with_school_uniform',
                    'respect_authorities_directives',
                    'pay_installments_on_time',
                    'respect_parent_assembly_agreements',
                    'pay_overdue_installments',
                    // 'family_member_studied_worked_at_school'
                ] as $field)
                    <tr>
                        <td>
                            <span class="field-label">{{ $list_comment[$field] ?? null }}</span>:
                            <span class="field-value">
                                {{ formatBoolean($catchment_interview->$field) }}
                            </span>
                        </td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="1" class="field-label">
                        <div class="tr_strong">VII.- Aspectos Religiosos</div>
                    </td>
                </tr>

                <tr><td><span class="field-label">{{ $list_comment['religion'] ?? null }}</span>: <span class="field-value">{{ $catchment_interview->religion ?? null }}</span></td></tr>

                @foreach([
                    'awareness_of_catholic_school_affiliation',
                    'agreement_to_catholic_formation',
                    'agreement_to_participate_in_catholic_activities'
                ] as $field)
                    <tr>
                        <td>
                            <span class="field-label">{{ $list_comment[$field] ?? null }}</span>:
                            <span class="field-value">
                                {{ formatBoolean($catchment_interview->$field) }}
                            </span>
                        </td>
                    </tr>
                @endforeach

                <tr>
                    <td>
                        <span class="field-label">{{ $list_comment['justification_for_not_participating_in_catholic_activities'] ?? null }}</span>:
                        <span class="field-value">{{ $catchment_interview->justification_for_not_participating_in_catholic_activities ?? null }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>