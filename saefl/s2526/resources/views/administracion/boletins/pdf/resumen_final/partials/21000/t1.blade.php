 <table cellpadding="1" cellspacing="1" class="head"  width="100%">
    <tr>
        <th colspan="2" rowspan="4"class="head" style="width: 40%">
            <img alt="{{$inscripcion->logo ?? ''}}" width="220" height="25px" src="{{ asset('images/brand/gob/logo.png') }}">
        </th>
        <th colspan="4" align="center">RESUMEN FINAL DEL RENDIMIENTO ESTUDIANTIL</th>
    </tr>
    <tr>
        <th colspan="4" align="center">(Educación Primaria: 1° a 6° GRADO)</th>
    </tr>
    <tr>
        <th colspan="4" align="center">Código del Formato: DEA-06-04</th>
    </tr>
    <tr>
        <th style="text-align: center">I. Plan de Estudio</th>
        <td class="td_uline td-nowrap">{{ $pestudio->name ?? '' }}</td>
        <th align="center">Código: </th>
        <td class="td_uline">{{ $pestudio->code_oficial ?? '' }}</td>
    </tr>
    <tr>
        <th style="text-align: center">Año Escolar:</th>
        <td class="td_uline"> {{ Session::get('pescolar_name') }}</td>
        <th class="td-nowrap">Mes y Año de la Evaluación:</th>
        <td class="td_uline"> JULIO-{{ $ano ?? '' }}</td>
    </tr>
</table>
