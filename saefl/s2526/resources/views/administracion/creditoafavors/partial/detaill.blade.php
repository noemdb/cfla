<table class="table table-sm table-striped">
    <tbody>
  {{-- <thead> --}}
    <tr>
      <th scope="col">Estudiante</th>
      <td>{{$creditoafavor->estudiant->fullname ?? ''}}</td>
    </tr>
    <tr>
      <th scope="col">Concepto generador</th>
      <td>{{$creditoafavor->registro_pago->cuentaxpagar->name ?? ''}}</td>
      {{-- <td>{{$creditoafavor->pago->registro_pago->cuentaxpagar->name ?? ''}}</td> --}}
    </tr>
  {{-- </thead> --}}
    <tr>
      <th scope="col">Ingreso</th>
      <td>
        NO: {{$creditoafavor->ingreso->number_i_pay ?? ''}},
        {{$creditoafavor->ingreso->banco->name ?? ''}}
      </td>
    </tr>
    <tr>
      <th scope="col">Otroes CAF</th>
      <td>{{$creditoafavor->credito_a_favor_ids ?? ''}}</td>
    </tr>
    <tr>
      <th scope="col">Descripción</th>
      <td>{{$creditoafavor->credito_description ?? ''}}</td>
    </tr>
    <tr>
      <th scope="col">Observaciones</th>
      <td>{{$creditoafavor->credito_observations ?? ''}}</td>
    </tr>
    <tr>
      <th scope="col">Monto Bs.</th>
      <td>{{f_float($creditoafavor->credito_ammount)}}</td>
    </tr>
  </tbody>
</table>
