<u style="font-size:0.8rem"><strong>DATOS DE LA MADRE:</strong></u> 
<table width="100%" class="table table-striped table-hover table-sm small p-1" style="padding-bottom:0.5rem;font-size:0.7rem !important">

    <tr>
        <td colspan="3">
                <strong>NOMBRES Y APELLIDOS: {{$student_record->mother_name ?? null}} {{$student_record->mother_lastname ?? null}} </strong> 
        </td>
    </tr>
    <tr>
        <td>
                <strong>CÉDULA DE IDENTIDAD: </strong> 
        </td>
        <td>
                <strong>PROFESIÓN: </strong> 
        </td>
        <td>
                <strong>TELÉFONOS: </strong> 
        </td>
    </tr>
    <tr>
        <td>{{$student_record->mother_ci ?? null}} </td>
        <td>{{$student_record->mother_profession ?? null}} </td>
        <td>{{$student_record->mother_phones ?? null}} </td>
    </tr>
    <tr>
        <td colspan="3">
                <strong>DIRECCIÓN Y CONTACTO DE SU TRABAJO</strong> 
        </td>
    </tr>
    <tr>
        <td colspan="3"> {{$student_record->mother_address ?? null}}</td>
    </tr>

</table>

<br>

<u style="font-size:0.8rem"><strong>DATOS DEL PADRE:</strong></u> 
<table width="100%" class="table table-striped table-hover table-sm small p-1" style="padding-bottom:0.5rem;font-size:0.7rem !important">

    <tr>
        <td colspan="3">
                <strong>NOMBRES Y APELLIDOS: {{$student_record->father_name ?? null}} {{$student_record->father_lastname ?? null}} </strong> 
        </td>
    </tr>
    <tr>
        <td>
                <strong>CÉDULA DE IDENTIDAD: </strong> 
        </td>
        <td>
                <strong>PROFESIÓN: </strong> 
        </td>
        <td>
                <strong>TELÉFONOS: </strong> 
        </td>
    </tr>
    <tr>
        <td>{{$student_record->father_ci ?? null}} </td>
        <td>{{$student_record->father_profession ?? null}} </td>
        <td>{{$student_record->father_phones ?? null}} </td>
    </tr>
    <tr>
        <td colspan="3">
                <strong>DIRECCIÓN Y CONTACTO DE SU TRABAJO</strong> 
        </td>
    </tr>
    <tr>
        <td colspan="3"> {{$student_record->father_address ?? null}}</td>
    </tr>

</table>