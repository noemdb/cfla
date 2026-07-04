<table width="100%" class="table table-striped table-hover table-sm small p-1" style="padding-bottom:0.5rem;font-size:0.7rem !important">
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
            @php
                $grado = $estudiant->grado;
                $seccion = $estudiant->seccion;
                $pestudio = $estudiant->pestudio;
            @endphp
            <td>{{ $grado->name ?? ''}} {{ $seccion->name ?? ''}}</td>
            <td>{{ $pestudio->name ?? ''}} </td>
        </tr>
    </tbody>
</table>