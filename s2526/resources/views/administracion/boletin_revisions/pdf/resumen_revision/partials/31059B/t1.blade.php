 <table cellpadding="1" cellspacing="1" class="head"  width="100%">
    <tr>
        <th colspan="2" rowspan="4"class="head" style="width: 40%">
            <img alt="{{$inscripcion->logo ?? ''}}" width="220" height="25px" src="{{ asset('images/brand/gob/logo.png') }}">
        </th>
        <th colspan="4" align="center">RESUMEN FINAL DEL RENDIMIENTO ESTUDIANTIL</th>
    </tr>
    <tr>
        <th colspan="4" align="center">Código del Formato: EMGCT</th>
    </tr>
    <tr>
        <th>I. Año Escolar:</th>
        <td colspan="3" class="td_uline"> {{ Session::get('pescolar_name') }}</td>
    </tr>
    <tr>
        <td style="width: 3cm">Tipo de Evaluación:</td>
        <td class="td_uline">REVISIÓN</td>
        <td style="width: 2cm" align="right">Mes y Año:</td>
        <td class="td_uline">JULIO-{{ $ano ?? '' }}</td>
    </tr>
</table>
