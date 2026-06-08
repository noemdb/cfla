<table cellpadding="2" cellspacing="2"  border="1" width="99%" class="table-seccion grid">
    <thead>
        <tr>
        <th colspan="5" align="left">
            VI. Autoridades Educativas:
        </th>
        </tr>
    </thead>
    <tbody>
       <tr>
          <td colspan="5">DIRECTOR(A) DEL PLANTEL:</td>
       </tr>
       <tr>
          <td class="td-nowrap">Apellidos y Nombres:</td>
            <td class="td-nowrap">{{ $autoridad1->fullname ?? ''}}</td>
          <td>CI:</td>
          <td>{{ $autoridad1->ci ?? ''}}</td>
          <td style="width: 12rem">FIRMA:</td>
       </tr>
       <tr>
          <td colspan="5">COORDINADOR(A) DE CONTROL DE ESTUDIOS:</td>
       </tr>
       <tr>
          <td class="td-nowrap">Apellidos y Nombres:</td>
          <td>{{ $autoridad2->fullname ?? ''}}</td>
          <td>CI:</td>
          <td>{{ $autoridad2->ci ?? ''}}</td>
          <td style="width: 12rem">FIRMA:</td>
       </tr>
       <tr>
          <td colspan="5">FUNCIONARIO DESIGNADO POR EL MINISTERIO DEL PODER POPULAR PARA LA EDUCACION:</td>
       </tr>
       <tr>
          <td class="td-nowrap">Apellidos y Nombres:</td>
          <td>{{ $registro_titulo->funcionario_name ?? ''}}</td>
          <td>CI:</td>
          <td>{{ $registro_titulo->funcionario_ci ?? ''}}</td>
          <td style="width: 12rem">FIRMA:</td>
       </tr>
    </tbody>
 </table>
