<table width="100%" class="table table-striped table-hover table-sm small p-1" style="padding-bottom:0.5rem;font-size:0.7rem !important;">

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
        <td colspan="2" >
                SI: [{{($student_record->status_vaccination_schedule) ? "X" : ' '}}] 
        </td>
        <td colspan="5" > </td>
        <td colspan="2" >
                NO: [{{($student_record->vaccination_schedule===false) ? "X" : ' '}}] 
        </td>
        <td colspan="2" > </td>
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

