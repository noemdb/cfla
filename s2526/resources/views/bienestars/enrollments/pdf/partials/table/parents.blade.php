<u style="font-size:0.8rem"><strong>DATOS DE LA MADRE:</strong></u>
<table cellspacing="1" cellpadding="1" class="table-sm" style="padding-top:0.4rem;font-size:0.6rem !important" border="1">

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

<u style="font-size:0.8rem"><strong>DATOS DEL PADRE:</strong></u>
<table cellspacing="1" cellpadding="1" class="table-sm" style="padding-top:0.4rem;font-size:0.6rem !important" border="1">

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
