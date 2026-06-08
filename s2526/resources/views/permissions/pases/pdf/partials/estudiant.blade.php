@php $pestudio = $estudiant->pestudio; $full_inscripcion = $estudiant->full_inscripcion; @endphp
<table class="table table-sm small">
    <thead  class="thead-inverse"  align="left">
        <tr >
            <th>Identificador</th>
            <th>Apellidos y Nombres</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $estudiant->type_ci->code ?? ''}}: {{ $estudiant->ci_estudiant ?? ''}}</td>
            <td>{{$estudiant->fullname ?? ''}} </td>
        </tr>
    </tbody>
    <thead  class="thead-inverse"  align="left">
        <tr>
            <th>Grado y Sección</th>
            <th>Etapa</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $full_inscripcion ?? null }} </td>
            <td>{{ $pestudio->name ?? ''}} </td>
        </tr>
    </tbody>
</table>
