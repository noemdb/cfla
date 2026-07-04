<table cellpadding="1" cellspacing="1" width="100%" class="table-seccion">
    <thead>
       <tr>
          <th colspan="8" align="left">
            III. Identificación de la Evaluación:
          </th>
       </tr>
    </thead>
    <tbody>
       <tr>
          <td class="nowrap_td" style="width: 1rem">Final:</td>
          <td class="td_uline" align="center"> {{ ($registro_titulo->tevaluacion_id == '1') ? 'X' : null}} </td>
          <td class="nowrap_td" style="width: 1rem">Revisión:</td>
          <td class="td_uline" align="center"> {{ ($registro_titulo->tevaluacion_id == '3') ? 'X' : null}} </td>
          <td class="nowrap_td" style="width: 1rem">Materia Pendiente:</td>
          <td class="td_uline" align="center"> {{ ($registro_titulo->tevaluacion_id == '13') ? 'X' : null}} </td>
          <td class="nowrap_td" style="width: 1rem">Otra: </td>
          <td class="td_uline" align="center"> {{ ( !in_array($registro_titulo->tevaluacion_id, ['1','3','13']) ) ? 'X' : null}} </td>
       </tr>
    </tbody>
 </table>
