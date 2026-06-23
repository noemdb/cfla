<table width="100%" cellpadding="0" cellspacing="0" style="font-size:0.8rem; margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <thead>
        <tr>
            <th style="width:25%">Identificador</th>
            <th style="width:75%">Apellidos y Nombres</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $estudiant->type_ci->code ?? ''}}: {{ $estudiant->ci_estudiant ?? ''}}</td>
            <td>{{ $estudiant->fullname ?? ''}} </td>
        </tr>
        <tr>
            <th>Grado y Sección</th>
            <th>Etapa</th>
        </tr>
        <tr>
            <td>{{ strtoupper($grado->name) }} SECCIÓN {{ $seccion->name ?? ''}}</td>
            <td>{{ $pestudio->name ?? ''}} [{{$pestudio->code_oficial ?? ''}}]</td>
        </tr>
    </tbody>
</table>
