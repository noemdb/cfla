<u style="font-size:0.8rem"><strong>DATOS DE LA MADRE:</strong></u>
<table cellspacing="1" cellpadding="1" class="table-sm" style="padding-top:0.6rem;font-size:0.6rem !important"
    border="1">

    <tr>
        <td colspan="3">
            <strong>NOMBRES Y APELLIDOS: {{ optional($enrollment)->mother_name ?? null }}
                {{ optional($enrollment)->mother_lastname ?? null }} </strong>
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
        <td>{{ optional($enrollment)->mother_ci ?? null }} </td>
        <td>{{ optional($enrollment)->mother_profession ?? null }} </td>
        <td>{{ optional($enrollment)->mother_phones ?? null }} </td>
    </tr>
    <tr>
        <td colspan="3">
            <strong>DIRECCIÓN Y CONTACTO DE SU TRABAJO</strong>
        </td>
    </tr>
    <tr>
        <td colspan="3"> {{ optional($enrollment)->mother_address ?? null }}</td>
    </tr>

</table>

<u style="font-size:0.8rem"><strong>DATOS DEL PADRE:</strong></u>
<table cellspacing="1" cellpadding="1" class="table-sm" style="padding-top:0.4rem;font-size:0.6rem !important"
    border="1">

    <tr>
        <td colspan="3">
            <strong>NOMBRES Y APELLIDOS: {{ optional($enrollment)->father_name ?? null }}
                {{ optional($enrollment)->father_lastname ?? null }} </strong>
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
        <td>{{ optional($enrollment)->father_ci ?? null }} </td>
        <td>{{ optional($enrollment)->father_profession ?? null }} </td>
        <td>{{ optional($enrollment)->father_phones ?? null }} </td>
    </tr>
    <tr>
        <td colspan="3">
            <strong>DIRECCIÓN Y CONTACTO DE SU TRABAJO</strong>
        </td>
    </tr>
    <tr>
        <td colspan="3"> {{ optional($enrollment)->father_address ?? null }}</td>
    </tr>

</table>
