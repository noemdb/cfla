<table class="table table-sm table-borderless ">
    <tbody>
  {{-- <thead> --}}
    <tr>
      <th scope="col">Estudiante</th>
      <td>{{$ingreso->estudiant->fullname ?? ''}}</td>
    </tr>
    <tr>
      <th scope="col">Concepto generador</th>
      <td>{{$ingreso->registro_pago->cuentaxpagar->name ?? ''}}</td>
      {{-- <td>{{$ingreso->pago->registro_pago->cuentaxpagar->name ?? ''}}</td> --}}
    </tr>
  {{-- </thead> --}}
    <tr>
      <th scope="col">Ingreso</th>
      <td>
        NO: {{$ingreso->ingreso->number_i_pay ?? ''}},
        {{$ingreso->ingreso->banco->name ?? ''}}
      </td>
    </tr>
    <tr>
      <th scope="col">Otroes CAF</th>
      <td>{{$ingreso->credito_a_favor_ids ?? ''}}</td>
    </tr>
    <tr>
      <th scope="col">Descripción</th>
      <td>{{$ingreso->credito_description ?? ''}}</td>
    </tr>
    <tr>
      <th scope="col">Observaciones</th>
      <td>{{$ingreso->credito_observations ?? ''}}</td>
    </tr>
    <tr>
      <th scope="col">Monto Bs.</th>
      <td>{{f_float($ingreso->credito_ammount)}}</td>
    </tr>
  </tbody>
</table>