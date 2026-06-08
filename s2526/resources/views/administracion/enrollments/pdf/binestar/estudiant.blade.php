<table cellspacing="1" cellpadding="1" class="table-sm" style="padding-top:0.4rem;font-size:0.6rem !important"
    border="1">

    <tr>
        <td colspan="16">
            <strong>&iquest;CON QUIÈNES VIVE ACTUALMENTE EL ALUMNO?</strong>
        </td>
    </tr>
    <tr>
        <td colspan="16">{{ optional($enrollment)->coexistence }}</td>
    </tr>
    <tr>
        <td colspan="16">
            <strong>&iquest;CÓMO SE TRANSPORTA EL ESCOLAR PARA LLEGAR A LA ESCUELA? (MARQUE CON UNA X LA CASILLA
                CORRESPONDIENTE)</strong>
        </td>
    </tr>
    <tr style="vertical-align: top !important">
        <td>
            VEH&Iacute;CULO PARTICULAR: [{{ optional($enrollment)->status_transport_private_vehicle ? 'X' : ' ' }}]
        </td>
        <td colspan="4" style="white-space:nowrap !important">
            TRANSPORTE PÚBLICO: [{{ optional($enrollment)->status_transport_public_vehicle ? 'X' : ' ' }}]
        </td>
        <td colspan="3">
            CAMINANDO: [{{ optional($enrollment)->status_transport_walking ? 'X' : ' ' }}]
        </td>
        <td colspan="8">
            OTRO: {{ optional($enrollment)->status_transport_other ? 'X' : null }}
            (ESPECIFIQUE):<br />{{ optional($enrollment)->transport_other ?? null }}
        </td>
    </tr>
    <tr>
        <td colspan="5">
            <strong>ESQUEMA DE VACUNACIÓN:</strong>
        </td>
        @php $status = (optional($enrollment)->status_vaccination_schedule) ? true : false; @endphp
        <td colspan="2">
            SI: [{{ $status ? 'X' : ' ' }}]
        </td>
        <td colspan="5"> </td>
        <td colspan="2">
            NO: [{{ !$status ? 'X' : ' ' }}]
        </td>
        <td colspan="2"> </td>
    </tr>
    <tr>
        <td colspan="16"> </td>
    </tr>
    <tr>
        <td colspan="16">
            <strong>&iquest;POSEE POTENCIAL DEPORTIVO/CULTURAL U OTRO? : </strong>
            [{{ optional($enrollment)->status_sports_potential ? 'X' : ' ' }}]
            {{ optional($enrollment)->sports_potential ?? null }}
        </td>
    </tr>
    <tr>
        <td colspan="16">
            <strong>LUGAR DÓNDE PRACTICA: </strong> {{ optional($enrollment)->place_where_he_practices ?? null }}
        </td>
    </tr>

</table>
