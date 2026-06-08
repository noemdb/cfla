<table border="1" width="100%" class="table-signal" cellpadding="1" cellspacing="1">
  <thead>
    <tr>
      <th style="width: 30% !important">VIII. Fecha de Remisión:
      <span class="small-90" style="padding-left:0.2cm;text-transform: capitalize;">{{
          $fecha_remision->format('d-m-Y') ?? '' }}</span></td>
      <td rowspan="6" style="width: 20% !important;  vertical-align: middle !important; text-align: center;">SELLO DE LA INSTITUCIÓN EDUCATIVA</td>
      <th style="width: 30% !important">IX. Fecha de Recepción</th>
      <td rowspan="6" style="width: 20% !important;  vertical-align: middle !important; text-align: center;">SELLO DE LA ZONA EDUCATIVA DEL ESTADO YARACUY</td>
    </tr>
    <tr>
      <td>Directora</td>
      <td>Funcionario Receptor</td>
    </tr>
    <tr>
      <td style="font-weight: bold">{{ $autoridad1->fullname ?? ''}}</td>
      <td></td>
    </tr>
    <tr>
      <td>Cédula de Identidad</td>
      <td>Cédula de Identidad</td>
    </tr>
    <tr>
      <td style="font-weight: bold">{{ $autoridad1->getCiFullF2() ?? ''}}</td>
      <td></td>
    </tr>
    <tr>
      <td style="line-height: 1.2rem !important">Firma</td>
      <td style="line-height: 1.2rem !important">Firma</td>
    </tr>
  </thead>
</table>
