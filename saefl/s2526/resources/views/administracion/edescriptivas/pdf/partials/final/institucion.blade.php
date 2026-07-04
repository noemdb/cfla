<table class="table table-sm small" cellpadding="1" cellspacing="1" style="font-size:0.7rem !important;">
    <thead>
        <tr>
            <th colspan="2">1.- DATOS DE IDENTIFICACIÓN DEL PLANTEL</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>NOMBRE DEL PLANTEL</td>
            <th class="td_uline">{{$institucion->name ?? ''}}</th>
        </tr>
        <tr>
            <td>DIRECCIÓN</td>
            <th class="td_uline" style="white-space:normal !important;">{{ $institucion->address ?? '' }}</th>
        </tr>
        <tr>
            <td>MUNICIPIO</td>
            <th class="td_uline">{{ $institucion->town_hall ?? '' }} </th>
        </tr>
    </tbody>

</table>
