<table cellspacing="1" cellpadding="1" class="table-sm" style="padding-top:0.6rem;font-size:0.6rem !important"
    border="1">
    <tr>
        <td colspan="16">
            <strong>¿PRESENTA ALGUNA ENFERMEDAD DE GRAVEDAD?</strong>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            CARDIOVASCULAR [{{ optional($enrollment)->status_illness_cardiovascular ? 'X' : ' ' }}]
        </td>
        <td colspan="4">
            CÁNCER [{{ optional($enrollment)->status_illness_cancer ? 'X' : ' ' }}]
        </td>
        <td colspan="5" style="white-space: nowrap !important;">
            LUPUS [{{ optional($enrollment)->status_illness_lupus ? 'X' : ' ' }}]
        </td>
        <td colspan="5">
            DIABETES [{{ optional($enrollment)->status_illness_diabetes ? 'X' : ' ' }}]
        </td>
    </tr>
    <tr>
        <td colspan="2">
            PROBLEMAS RENALES [{{ optional($enrollment)->status_illness_renal_problems ? 'X' : ' ' }}]
        </td>
        <td colspan="4">
            SOBREPESO [{{ optional($enrollment)->status_illness_overweight ? 'X' : ' ' }}]
        </td>
        <td colspan="10">
            OTRA (ESPECIFIQUE) [{{ optional($enrollment)->status_illness_other ? 'X' : ' ' }}]
            {{ optional($enrollment)->illness_other }}
        </td>
    </tr>
    <tr>
        <td colspan="16">
            <strong>¿PRESENTA ALGUNA DE ESTAS CONDICIONES?</strong>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            DISCAPACIDAD INTELECTUAL
            [{{ optional($enrollment)->status_conditions_intellectual_disability ? 'X' : ' ' }}]
        </td>
        <td colspan="6">
            DISCAPACIDAD MOTRIZ [{{ optional($enrollment)->status_conditions_motor_disability ? 'X' : ' ' }}]
        </td>
        <td colspan="7">
            DISCAPACIDAD VISUAL [{{ optional($enrollment)->status_conditions_visual_disability ? 'X' : ' ' }}]

        </td>
    </tr>
    <tr>
        <td colspan="3">
            DISCAPACIDAD AUDITIVA [{{ optional($enrollment)->status_conditions_hearing_impairment ? 'X' : ' ' }}]
        </td>
        <td colspan="6">
            ACTITUDES SOBRESALIENTES
            [{{ optional($enrollment)->status_conditions_outstanding_attitudes ? 'X' : ' ' }}]
        </td>
        <td colspan="7">
            AUTISMO [{{ optional($enrollment)->status_conditions_autism ? 'X' : ' ' }}]
        </td>
    </tr>
    <tr>
        <td colspan="16">
            OTRA (ESPECIFIQUE): [{{ optional($enrollment)->status_conditions_other ? 'X' : ' ' }}]
            <div>{{ optional($enrollment)->conditions_other ?? null }}</div>
        </td>
    </tr>
    <tr>
        <td colspan="9">
            <strong>¿ESTÁ SIENDO TRATADO POR ALGÚN ESPECIALISTA?</strong>
        </td>
        <td colspan="2">SI [{{ optional($enrollment)->status_treated_by_specialist ? 'X' : ' ' }}]</td>
        <td colspan="2"></td>
        <td colspan="2">NO [{{ is_null(optional($enrollment)->status_treated_by_specialist) ? 'X' : ' ' }}]</td>
        <td> </td>
    </tr>
    <tr>
        <td colspan="16">
            <strong>¿CUÁL ESPECIALISTA?: </strong> {{ optional($enrollment)->specialist ?? null }}
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <strong>¿TOMA ALGÚN MEDICAMENTO?
                [{{ optional($enrollment)->status_take_medication ? 'X' : ' ' }}]</strong>
        </td>
        <td colspan="12">
            ESPECIFIQUE: {{ optional($enrollment)->medication ?? null }}
        </td>
    </tr>

</table>
