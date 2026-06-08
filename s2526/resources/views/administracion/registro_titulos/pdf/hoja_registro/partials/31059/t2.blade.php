<table cellpadding="1" cellspacing="1" class="table-seccion">
    <tbody>
      <tr>
        <th colspan="23" align="left">II. Datos del Plantel</th>
      </tr>
      <tr>
        <td width="40">C&oacute;digo:</td>
        <td colspan="5" class="td_uline">{{ $institucion->code_oficial ?? '' }}</td>
        <td align="right" >Nombre:</td>
        <td colspan="17" class="td_uline">{{ $institucion->name ?? '' }}</td>
      </tr>
      <tr>
        <td>Direcci&oacute;n:  </td>
        <td colspan="15" class="td_uline">{{ $institucion->address ?? '' }}</td>
        <td align="right">Tel&eacute;fono:</td>
        <td colspan="7" class="td_uline">{{ $institucion->phone ?? '' }} / {{ $institucion->phone2 ?? '' }}</td>
      </tr>
      <tr>
        <td>Municipio:</td>
        <td colspan="6" class="td_uline">{{ $institucion->town_hall ?? '' }}</td>
        <td align="right" class="td-nowrap">Entidad Federal: </td>
        <td colspan="8" class="td_uline">{{ $institucion->state ?? '' }}</td>
        <td colspan="2" align="right" class="td-nowrap">Zona Educativa: </td>
        <td colspan="6" class="td_uline">YARACUY</td>
      </tr>

    </tbody>
  </table>
