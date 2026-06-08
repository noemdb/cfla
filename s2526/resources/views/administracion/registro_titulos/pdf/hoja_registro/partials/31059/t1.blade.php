<table cellpadding="1" cellspacing="1" class="head" width="100%">
    <thead>
       <tr>
          <th colspan="2" rowspan="3" class="head" style="width: 40%">
            <img alt="{{$inscripcion->logo ?? ''}}" width="220" height="25px" src="{{ asset('images/brand/gob/logo.png') }}">
          </th>
          <th colspan="2" align="center">HOJA DE REGISTRO TÍTULO</th>
       </tr>
       <tr>
          <td style=" padding-left:1rem">Código del Formato:</td>
          <td class="td_uline">EMG</td>
       </tr>
       <tr>
          <td style=" padding-left:1rem">I. Tipo de Registro:</td>
          <td class="td_uline">TÍTULO</td>
       </tr>
    </thead>
    <tbody>
       <tr>
          <td class="text-right">Mes y Año de Egreso:</td>
          <td class="td_uline text-uppercase">{{ $fecha_egreso ?? ''}}</td>
          <td style=" padding-left:1rem">Lugar y Fecha de Expedición:</td>
          {{-- <td class="td_uline">YARACUY 31 DE JULIO DE 2019</td> --}}
          <td class="td_uline text-uppercase">YARACUY {{ $fecha_remision->format('d F Y') ?? ''}}</td>
       </tr>
    </tbody>
 </table>
