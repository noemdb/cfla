<table class="table table-sm small">
    <thead  class="thead-inverse">
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
    <thead  class="thead-inverse">
        <tr>
            <th>Grado y Sección</th>
            <th>Etapa</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $grado->name ?? ''}} {{ $seccion->name ?? ''}}</td>
            <td>{{ $pestudio->name ?? ''}} </td>
        </tr>
    </tbody>
</table>
