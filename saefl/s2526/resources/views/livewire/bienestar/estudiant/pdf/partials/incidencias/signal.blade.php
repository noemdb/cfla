@php
    $grado = $estudiant->grado;
    $seccion = $estudiant->seccion;
    $representant = $estudiant->representant;
@endphp

<table class="table-sm" style=" font-size:0.7rem;text-align: center">
    <tr>
        <td width="33%">
            {{ $profesor->fullname ?? '' }} - CI: {{ $profesor->ci_profesor ?? '' }}<br>
            <span class="text-muted">Profesor</span>
        </td>
        <td width="33%">
            {{ $representant->name ?? ''}} - CI: {{ $representant->ci_representant ?? ''}}<br>
            <span class="text-muted">Representante</span>
        </td>
    </tr>
    <tr>
        <td colspan="2">

        </td>
    </tr>
</table>
<div style="margin-top: 1rem;font-size:0.7rem;text-align: center">
    <span>Fecha: {{ $toDate }} </span>
</div>
