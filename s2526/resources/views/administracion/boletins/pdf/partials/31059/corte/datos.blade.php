<table width="100%" cellpadding="0" cellspacing="0" style="font-size:0.7rem; margin-bottom:0.2rem; padding-bottom:0.2rem;margin-left:0.6rem;">
    <thead>
        <tr>
            <th style="width:25%;text-align: left;">Identificador</th>
            <th style="width:75%;text-align: left;">Apellidos y Nombres</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: left;">{{ $estudiant->type_ci->code ?? ''}}: {{ $estudiant->ci_estudiant ?? ''}}</td>
            <td style="">{{ $estudiant->fullname ?? ''}} </td>
        </tr>
        <tr>
            <th style="text-align: left;">Grado y Sección</th>
            <th style="text-align: left;">Etapa</th>
        </tr>
        <tr>
            <td style="text-align: left;">{{ strtoupper($grado->name) }} SECCIÓN {{ $seccion->name ?? ''}}</td>
            <td style="text-align: left;">{{ $pestudio->name ?? ''}} [{{$pestudio->code_oficial ?? ''}}]</td>
        </tr>
    </tbody>
</table>
