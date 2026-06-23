<table cellspacing="1" cellpadding="1" class="table-sm" style="padding-top:0.4rem;font-size:0.6rem !important" border="1">
    <thead>
        <tr>
            <th colspan="6" align="left" style="font-weight: bold !important;font-size: 0.8rem !important">DATOS DEL ALUMNO(A):</th>
            <th colspan="5" style="font-weight: bold !important;font-size: 0.8rem !important">TELÉFONOS</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="6">APELLIDOS: {{ $enrollment->lastname ?? '' }}</td>
            {{-- <td colspan="2" rowspan="2" align="center">TELÉFONOS</td> --}}
            <td colspan="2">TEL.</td>
            <td colspan="3">{{ $enrollment->phone_representant ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="6">NOMBRES: {{ $enrollment->name ?? '' }}</td>
            <td colspan="2">CORREO</td>
            <td colspan="3">{{ $enrollment->email_representant ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="11">GRADO/AÑO SECCIÓN: {{ $estudiant->grado->name ?? '' }} {{ $estudiant->seccion->name ?? '' }}</td>
        </tr>
        {{-- <tr> --}}
            {{-- <td colspan="6">APELLIDOS: {{ $enrollment->lastname ?? '' }}</td> --}}
            {{-- <td colspan="5">NOMBRES: {{ $enrollment->name ?? '' }}</td> --}}
            {{-- <td colspan="3"></td> --}}
        {{-- </tr> --}}
        <tr>
            <td>SEXO</td>
            <td colspan="3">CEDULA DE IDENTIDAD</td>
            <td>EDAD</td>
            <td colspan="3" align="center">FECHA DE NACIMIENTO</td>
            <td style="width: 3.5rem">CIU. DE N.</td>
            <td colspan="2">{{ $enrollment->town_hall_birth ?? '' }}</td>
        </tr>
        <tr>
            <td>{{ $enrollment->gender ?? '' }}</td>
            <td colspan="3">{{ $enrollment->nacionalidad ?? '' }} {{ $enrollment->ci_estudiant ?? '' }}</td>
            <td>{{ $enrollment->age ?? '' }}</td>
            <td>DÍA</td>
            <td>MES</td>
            <td>AÑO</td>
            <td style="width: 3.5rem">EDO. DE N.</td>
            <td colspan="2">{{ $enrollment->state_birth ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td>{{ $enrollment->day ?? '' }}</td>
            <td>{{ $enrollment->month ?? '' }}</td>
            <td>{{ $enrollment->year ?? '' }}</td>
            <td style="width: 3.5rem">PAÍS. DE N.</td>
            <td colspan="2">{{ $enrollment->country_birth ?? '' }}</td>
        </tr>
        <tr>
            <td colspan="11">PARTICIPACIÓN EN GRUPOS: </td>
        </tr>
        <tr>
            <td colspan="11">MATERIA PENDIENTE: {{ $estudiant->pending_matter ?? '' }}</td>
        </tr>
    </tbody>
</table>
