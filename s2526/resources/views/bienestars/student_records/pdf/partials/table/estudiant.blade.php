<table cellspacing="1" cellpadding="1" class="table-sm" style="padding-top:0.4rem;font-size:0.6rem !important" border="1">

    <tr>
        <td colspan="16">
                <strong>&iquest;CON QUIÈNES VIVE ACTUALMENTE EL ALUMNO?</strong>
        </td>
    </tr>
    <tr>
        <td colspan="16">{{$student_record->coexistence}}</td>
    </tr>
    <tr>
        <td colspan="16">
                <strong>&iquest;CÓMO SE TRANSPORTA EL ESCOLAR PARA LLEGAR A LA ESCUELA? (MARQUE CON UNA X LA CASILLA CORRESPONDIENTE)</strong>
        </td>
    </tr>
    <tr style="vertical-align: top !important">
        <td >
                VEH&Iacute;CULO PARTICULAR: [{{($student_record->status_transport_private_vehicle) ? "X" : ' '}}]
        </td>
        <td colspan="4" style="white-space:nowrap !important">
                TRANSPORTE PÚBLICO: [{{($student_record->status_transport_public_vehicle) ? "X" : ' '}}]
        </td>
        <td colspan="3" >
                CAMINANDO: [{{($student_record->status_transport_walking) ? "X" : ' '}}]
        </td>
        <td colspan="8" >
                OTRO: {{($student_record->status_transport_other) ? "X" : null}} (ESPECIFIQUE):<br />{{$student_record->transport_other ?? null}}
        </td>
    </tr>
    <tr>
        <td colspan="5" >
                <strong>ESQUEMA DE VACUNACIÓN:</strong>
        </td>
        @php $status = ($student_record->status_vaccination_schedule) ? true : false; @endphp
        <td colspan="2" >
                SI:
        </td>
        <td colspan="5" align="center" ><b>{{ ( $status) ? "X" : ' '}}</b></td>
        <td colspan="2" >
                NO:
        </td>
        <td colspan="2" align="center" ><b>{{ ( ! $status) ? "X" : ' '}}</b></td>
    </tr>
    <tr>
        <td colspan="16"> </td>
    </tr>
    <tr>
        <td colspan="16">
            <strong>&iquest;POSEE POTENCIAL DEPORTIVO/CULTURAL U OTRO? : </strong> [{{($student_record->status_sports_potential) ? "X" : ' '}}] {{$student_record->sports_potential ?? null}}
        </td>
    </tr>
    <tr>
        <td colspan="16">
            <strong>LUGAR DÓNDE PRACTICA: </strong> {{$student_record->place_where_he_practices ?? null}}
        </td>
    </tr>

</table>

