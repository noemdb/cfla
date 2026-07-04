<table cellspacing="1" cellpadding="1" class="table-sm" style="padding-top:0.4rem;font-size:0.6rem !important" border="1">
    <tr>
        <td colspan="16">
            <strong>¿PRESENTA ALGUNA ENFERMEDAD DE GRAVEDAD?</strong>
        </td>
    </tr>
    <tr>
        <td colspan="2" >
            CARDIOVASCULAR [{{($student_record->status_illness_cardiovascular) ? "X" : ' '}}]
        </td>
        <td colspan="4" >
                CÁNCER [{{($student_record->status_illness_cancer) ? "X" : ' '}}]
        </td>
        <td colspan="5" style="white-space: nowrap !important;">
            LUPUS [{{($student_record->status_illness_lupus) ? "X" : ' '}}]
        </td>
        <td colspan="5" >
            DIABETES [{{($student_record->status_illness_diabetes) ? "X" : ' '}}]
        </td>
    </tr>
    <tr>
        <td colspan="2" >
            PROBLEMAS RENALES [{{($student_record->status_illness_renal_problems) ? "X" : ' '}}]
        </td>
        <td colspan="4" >
            SOBREPESO [{{($student_record->status_illness_overweight) ? "X" : ' '}}]
        </td>
        <td colspan="10" >
            OTRA (ESPECIFIQUE) [{{($student_record->status_illness_other) ? "X" : ' '}}] {{$student_record->illness_other}}
        </td>
    </tr>
    <tr>
        <td colspan="16">
            <strong>¿PRESENTA ALGUNA DE ESTAS CONDICIONES?</strong>
        </td>
    </tr>
    <tr>
        <td colspan="3" >
            DISCAPACIDAD INTELECTUAL [{{($student_record->status_conditions_intellectual_disability) ? "X" : ' '}}]
        </td>
        <td colspan="6" >
            DISCAPACIDAD MOTRIZ [{{($student_record->status_conditions_motor_disability) ? "X" : ' '}}]
        </td>
        <td colspan="7" >
            DISCAPACIDAD VISUAL [{{($student_record->status_conditions_visual_disability) ? "X" : ' '}}]

        </td>
    </tr>
    <tr>
        <td colspan="3" >
            DISCAPACIDAD AUDITIVA [{{($student_record->status_conditions_hearing_impairment) ? "X" : ' '}}]
        </td>
        <td colspan="6" >
            ACTITUDES SOBRESALIENTES [{{($student_record->status_conditions_outstanding_attitudes) ? "X" : ' '}}]
        </td>
        <td colspan="7" >
            AUTISMO [{{($student_record->status_conditions_autism) ? "X" : ' '}}]
        </td>
    </tr>
    <tr>
        <td colspan="16" >
            OTRA (ESPECIFIQUE): [{{($student_record->status_conditions_other) ? "X" : ' '}}]
            <div>{{$student_record->conditions_other ?? null}}</div>
        </td>
    </tr>
    <tr>
        <td colspan="9">
            <strong>¿ESTÁ SIENDO TRATADO POR ALGÚN ESPECIALISTA?</strong>
        </td>
        <td colspan="2" >SI </td>
        <td colspan="2" align="center"><b>{{($student_record->status_treated_by_specialist) ? "X" : ' '}}</b></td>
        <td colspan="2" >NO </td>
        <td align="center"><b>{{(is_null($student_record->status_treated_by_specialist)) ? "X" : ' '}}</b></td>
    </tr>
    <tr>
        <td colspan="16">
            <strong>¿CUÁL ESPECIALISTA?: </strong> {{$student_record->specialist ?? null}}
        </td>
    </tr>
    <tr>
        <td colspan="4" >
                <strong>¿TOMA ALGÚN MEDICAMENTO? [{{($student_record->status_take_medication) ? "X" : ' '}}]</strong>
        </td>
        <td colspan="12">
                ESPECIFIQUE: {{$student_record->medication ?? null}}
        </td>
    </tr>

</table>
