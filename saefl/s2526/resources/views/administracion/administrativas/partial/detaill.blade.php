<table class="table table-sm table-borderless ">
    <tbody>
  {{-- <thead> --}}
    <tr>
      <th scope="col">Estudiante</th>
      <td>{{$administrativa->estudiant->fullname}}</td>
    </tr>
  {{-- </thead> --}}
    <tr>
      <th scope="col">Fecha de Inscripción</th>
      <td>{{$administrativa->created_at ?? ''}}</td>
    </tr>
    <tr>
      <th scope="col">Plan de pago asignado</th>
      <td>{{$administrativa->planpago->name}}</td>
    </tr>
    <tr>
      <th scope="col">Elaborada por:</th>
      <td>{{$administrativa->user->profile->fullname ?? ''}}</td>
    </tr>
  </tbody>
</table>