<table class="table table-sm small" style=" margin-top:0.3rem">
    <thead>
        <tr>
            <th colspan="4">DATOS DE IDENTIFICACIÓN DE{{ ($estudiant->gender == 'Femenino') ? ' LA':'L' }} ESTUDIANTE</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>APELLIDOS Y NOMBRES:</td>
            <th class="td_uline" colspan="3">{{$estudiant->fullname ?? ''}}</th>

        </tr>
        <tr>
            <td>CÉDULA ESCOLAR:</td>
            <th class="td_uline">{{ $estudiant->ci_estudiant ?? '' }}</th>
            <td style=" text-align:right">EDAD: </td>
            <th class="td_uline">{{ $estudiant->age ?? '' }} AÑOS</th>
        </tr>
        <tr>
            <td>DIRECCIÓN:</td>
            <th class="td_uline" colspan="3" style="white-space:normal !important;">{{ $estudiant->dir_address ?? '' }} </th>
        </tr>
    </tbody>

</table>
