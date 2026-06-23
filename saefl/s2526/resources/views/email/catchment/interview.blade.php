<table width="100%" cellpadding="0" cellspacing="0"
    style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <thead>
        <tr>
            <th scope="row" width="70px">
                <img width="70px" height="70px" class="card-img-top" src="{{asset('images/avatar/uecfla.jpg')}}">
            </th>
            <th align="center" style="text-align: center;">
                <div class="title"><b>{{ $institucion->name }}</b></div>
                <div class="title"><b>DIRECCIÓN ACADÉMICA</b></div>
            </th>
            <th scope="row" width="70px">
                <img width="100px" height="70px" class="card-img-top" src="{{asset('images/avatar/amigoniano.png')}}">
            </th>
        </tr>
    </thead>
</table>

<hr>
{{-- $catchment =Cacatchment_interviewtchmentInterview::where('id',$id)->first(); --}}
@php $catchment_interview = $interview; @endphp

<table cellpadding="2" cellspacing="2" style="border-collapse: collapse; width: 100%;" border="0">
    <tbody>
        <tr>
            <td style="width: 100%;">

                <p style="text-align: right;">En San Felipe - Edo. Yaracuy a {{$toDate ?? null}}</p>

                @if ($catchment_interview)

                <h1>Entrevista registrada</h1>
                <h3>Proceso de Matriculación Escolar</h3>

                <hr>

                <p style="text-align: justify;">
                    Estimado(a) Sr(a).&nbsp;<em><strong>{{$catchment_interview->full_name}}</strong>, CI;
                        {{$catchment_interview->identification_number}},</em>
                </p>

                <hr>

                <p>
                    Reiteramos nuestro agradecimiento por depositar en nuestras manos la confianza en la educación de
                    sus hijos. Para nuestro colegio, es de gran importancia la información que en el día de hoy nos han
                    suministrado tanto ustedes como sus representados, lo cual nos permitirá evaluar y decidir.
                    Una vez concluido el proceso con los grupos restantes, nos comunicaremos igualmente con ustedes por
                    esta vía.
                </p>

                <hr>

                <blockquote style="color: rgb(75, 74, 74); background-color: rgb(248, 248, 248);border-radius: 4px;padding: 3px" style="background-color:#">

                    <div style="color: rgb(75, 74, 74);font-size: 1rem;font-weight: bold;">Datos procesados:</div>

                    <table cellpadding="2" cellspacing="2" style="border-collapse: collapse; width: 100%;" border="0">
                        <tbody>
                            <tr>
                                <td colspan="1"
                                    style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">
                                    <div style="font-size: 1rem;font-weight: bold;">I.- Datos del Representante</div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['full_name']
                                        ?? null}}</span>:
                                    <span style="white-space: nowrap !important;"> {{ $catchment_interview->full_name ??
                                        null}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['identification_number']
                                        ?? null}}</span>: <span style="white-space: nowrap !important;"> {{
                                        $catchment_interview->identification_number ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['age']
                                        ?? null}}</span>: <span style="white-space: nowrap !important;"> {{
                                        $catchment_interview->age ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['relationship']
                                        ?? null}}</span>: <span style="white-space: nowrap !important;">
                                        {{ $catchment_interview->relationship ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['phone_numbers']
                                        ?? null}}</span>: <span style="white-space: nowrap !important;">
                                        {{ $catchment_interview->phone_numbers ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['email']
                                        ?? null}}</span>: <span style="white-space: nowrap !important;"> {{
                                        $catchment_interview->email ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['profession_occupation']
                                        ?? null}}</span>: <span style="white-space: nowrap !important;"> {{
                                        $catchment_interview->profession_occupation ?? null}}</span></td>
                            </tr>

                            {{-- --------------------------------------------------------------------------- --}}

                            <tr>
                                <td colspan="1"
                                    style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">
                                    <div style="font-size: 1rem;font-weight: bold;">II.- Datos del Estudiante Aspirante
                                        a Cupo</div>
                                </td>
                            </tr>

                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['student_full_name']
                                        ?? null}}</span>:
                                    <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->student_full_name ?? null}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['date_of_birth']
                                        ?? null}}</span>: <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->date_of_birth ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['student_age']
                                        ?? null}}</span>: <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->student_age ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td>
                                    @php $grado = $catchment_interview->grado; @endphp
                                    <span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['grade_year_aspiring']
                                        ?? null}}</span>: <span style="white-space: nowrap !important;">{{
                                        $grado->name ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['has_siblings']
                                        ?? null}}</span>: <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->has_siblings ? 'Sí' : 'No' ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['sibling_name']
                                        ?? null}}</span>: <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->sibling_name ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['sibling_grade_section']
                                        ?? null}}</span>: <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->sibling_grade_section ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['tutor_teacher_name']
                                        ?? null}}</span>: <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->tutor_teacher_name ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['tutor_teacher_phone']
                                        ?? null}}</span>: <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->tutor_teacher_phone ?? null}}</span></td>
                            </tr>

                            {{-- --------------------------------------------------------------------------- --}}

                            <tr>
                                <td colspan="1"
                                    style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">
                                    <div style="font-size: 1rem;font-weight: bold;">III.- Información sobre quién vive
                                        con el representado</div>
                                </td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['living_with']
                                        ?? null}}</span>: <span class="field-vaule">{{
                                        $catchment_interview->living_with ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['other_person_origin']
                                        ?? null}}</span>: <span class="field-vaule">{{
                                        $catchment_interview->other_person_origin ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['reason_for_living_with_other']
                                        ?? null}}</span>: <span class="field-vaule">{{
                                        $catchment_interview->reason_for_living_with_other ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['num_family_group_members']
                                        ?? null}}</span>: <span class="field-vaule">{{
                                        $catchment_interview->num_family_group_members ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['num_people_financially_dependent']
                                        ?? null}}</span>: <span class="field-vaule">{{
                                        $catchment_interview->num_people_financially_dependent ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['person_responsible_attending']
                                        ?? null}}</span>: <span class="field-vaule">{{
                                        $catchment_interview->person_responsible_attending ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['place_person_responsible_attending']
                                        ?? null}}</span>: <span class="field-vaule">{{
                                        $catchment_interview->place_person_responsible_attending ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['position_person_responsible_attending']
                                        ?? null}}</span>:
                                    <span class="field-vaule">{{
                                        $catchment_interview->position_person_responsible_attending ?? null}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['work_person_responsible_attending']
                                        ?? null}}</span>: <span class="field-vaule">{{
                                        $catchment_interview->work_person_responsible_attending ?? null}}</span></td>
                            </tr>

                            {{-- --------------------------------------------------------------------------- --}}

                            <tr>
                                <td colspan="1"
                                    style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">
                                    <div style="font-size: 1rem;font-weight: bold;">IV.- Aspectos Socio-Económicos</div>
                                </td>
                            </tr>

                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['monthly_income']
                                        ?? null}}:</span> <span class="field-valus">{{
                                        $catchment_interview->monthly_income ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['num_people_contributing']
                                        ?? null}}:</span> <span class="field-valus">{{
                                        $catchment_interview->num_people_contributing ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['income_source']
                                        ?? null}}:</span> <span class="field-valus">{{
                                        $catchment_interview->income_source ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['able_to_pay_dollars']
                                        ?? null}}:</span> <span class="field-valus">{{
                                        $catchment_interview->able_to_pay_dollars ? 'Sí' : 'No' ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['able_to_pay_bolivars']
                                        ?? null}}:</span> <span class="field-valus">{{
                                        $catchment_interview->able_to_pay_bolivars ? 'Sí' : 'No' ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['has_payment_responsible']
                                        ?? null}}:</span> <span class="field-valus">{{
                                        $catchment_interview->has_payment_responsible ? 'Sí' : 'No' ?? null}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['person_guarantor_name_phone']
                                        ?? null}}:</span> <span class="field-valus">{{
                                        $catchment_interview->person_guarantor_name_phone ?? null}}</span></td>
                            </tr>

                            {{-- --------------------------------------------------------------------------- --}}

                            <tr>
                                <td colspan="1"
                                    style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">
                                    <div style="font-size: 1rem;font-weight: bold;">V.- Aspectos Institucionales</div>
                                </td>
                            </tr>

                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['knowledge_of_school']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->knowledge_of_school ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['studied_at_school']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->studied_at_school ? 'Sí' : 'No' ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['year_of_graduation']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->year_of_graduation ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['academic_director']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->academic_director ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['school_director']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->school_director ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['teachers_worked_at_school']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->teachers_worked_at_school ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['reason_for_choosing_institution']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->reason_for_choosing_institution ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['recommendation_from_school']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->recommendation_from_school ? 'Sí' : 'No' ?? null}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['recommender_name']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->recommender_name ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['recommender_affinity']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->recommender_affinity ?? null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['recommender_phone']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->recommender_phone ?? null}}</span></td>
                            </tr>

                            {{-- --------------------------------------------------------------------------- --}}

                            <tr>
                                <td colspan="1"
                                    style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">
                                    <div style="font-size: 1rem;font-weight: bold;">VI.- Acuerdos</div>
                                </td>
                            </tr>

                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['agreement_to_code_of_conduct']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->agreement_to_code_of_conduct ? 'Sí' : 'No' ??
                                        null}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['respect_communication_channels']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->respect_communication_channels ? 'Sí' : 'No' ??
                                        null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['ensure_compliance_with_school_activities']
                                        ?? null}}:</span>
                                    <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->ensure_compliance_with_school_activities ? 'Sí' : 'No'
                                        ?? null}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['comply_with_school_uniform']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->comply_with_school_uniform ? 'Sí' : 'No' ?? null}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['respect_authorities_directives']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->respect_authorities_directives ? 'Sí' : 'No' ??
                                        null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['pay_installments_on_time']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->pay_installments_on_time ? 'Sí' : 'No' ?? null}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['respect_parent_assembly_agreements']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->respect_parent_assembly_agreements ? 'Sí' : 'No' ??
                                        null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['pay_overdue_installments']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->pay_overdue_installments ? 'Sí' : 'No' ?? null}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['family_member_studied_worked_at_school']
                                        ?? null}}:</span>
                                    <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->family_member_studied_worked_at_school ? 'Sí' : 'No' ??
                                        null}}</span>
                                </td>
                            </tr>

                            {{-- --------------------------------------------------------------------------- --}}

                            <tr>
                                <td colspan="1"
                                    style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">
                                    <div style="font-size: 1rem;font-weight: bold;">VII.- Aspectos Religiosos</div>
                                </td>
                            </tr>

                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['religion']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->religion ?? null}}</span></td>
                            </tr>

                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['awareness_of_catholic_school_affiliation']
                                        ?? null}}:</span>
                                    <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->awareness_of_catholic_school_affiliation ? 'Sí' : 'No'
                                        ?? null}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['agreement_to_catholic_formation']
                                        ?? null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->agreement_to_catholic_formation ? 'Sí' : 'No' ??
                                        null}}</span></td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['agreement_to_participate_in_catholic_activities']
                                        ??
                                        null}}:</span> <span style="white-space: nowrap !important;">{{
                                        $catchment_interview->agreement_to_participate_in_catholic_activities ? 'Sí' :
                                        'No' ?? null}}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><span
                                        style="font-weight: bold; white-space: wrap !important; margin-top: 1rem; padding-top: 1rem;">{{$list_comment['justification_for_not_participating_in_catholic_activities']
                                        ?? null}}:</span>
                                    <span style="white-space: nowrap !important;">
                                        {{$catchment_interview->justification_for_not_participating_in_catholic_activities
                                        ?? null}}
                                    </span>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </blockquote>

                @endif

                <hr>

                <p>
                    <div><strong>Siguientes actividades:</strong></div>

                    <ul>
                        <li>Envío de un correo electrónico a cada representante aceptado, para formalizar la inscripción de su representado en nuestra institución (Carta Digital de Aceptación)</li>
                        <li>Formalización de la inscripción de los estudiantes aceptados.</li>
                    </ul>
                    
                </p>

                <hr>

                <strong>Agradecemos su interés en nuestra institución.</strong>

                <hr>

                <p style="text-align: center;">Atte.</p>
                <div style="text-align: center;">
                    <div>{{$autoridad->profile_professional}} {{$autoridad->fullname}}</div>
                    <div>{{$autoridad->position}}</div>
                </div>
                <hr>
                <p>
                <div>{{$director->profile_professional}} {{$director->fullname}}</div>
                <div>{{$director->position}}</div>
                </p>
            </td>
        </tr>
    </tbody>
</table>

<hr>

<footer class="text-muted" style="font-size:0.8rem">
    <span>
        AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
        Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
    </span>
</footer>
