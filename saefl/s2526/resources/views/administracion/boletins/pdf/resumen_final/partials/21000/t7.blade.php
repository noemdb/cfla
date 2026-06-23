<table border="1" width="100%" class="table-signal" cellpadding="1" cellspacing="1" style="width: 100% !important">
    <thead>
      <tr>
        <th colspan="2" align="left" style="width: 30% !important">
          VI. Fecha de Remisión: <span class="small-90" style="padding-left:0.5cm;text-transform: capitalize;">{{ $fecha_remision->format('d-m-Y') ?? '' }}</span>
        </th>
        <th colspan="2" style="width: 30% !important">VII. Fecha de Recepción</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Directora</td>
        <td rowspan="4" align="center" style="width: 20% !important;  vertical-align: middle !important; text-align: center;">SELLO  DE LA INSTITUCION EDUCATIVA</td>
        <td>Funcionario Receptor</td>
        <td rowspan="4" align="center" style="width: 20% !important;  vertical-align: middle !important; text-align: center;">SELLO DE LA ZONA EDUCATIVA DEL ESTADO YARACUY</td>
      </tr>
      <tr>
        <td>{{ $autoridad1->fullname ?? ''}}</td>
        <td></td>
      </tr>
      <tr>
        <td>Número de C.I: V-{{ $autoridad1->ci ?? ''}}</td>
        <td>Número de C.I: </td>
      </tr>
      {{-- <tr>
        <td  style="font-weight: bold">V-{{ $autoridad1->ci ?? ''}}</td>
        <td></td>
      </tr> --}}
      <tr>
        <td> <p>Firma</p> </td>
        <td> <p>Firma</p> </td>
      </tr>
    </tbody>
</table>
