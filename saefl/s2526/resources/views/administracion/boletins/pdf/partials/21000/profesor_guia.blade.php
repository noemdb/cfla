@php
    $grado=$estudiant->grado;
    $seccion=$estudiant->seccion;
    $profesor_guia=$grado->profesor_guias->where('seccion_id',$seccion->id)->first();
@endphp

<table class="table-sm" style=" font-size:0.7rem">
    <tr>
        <td width="33%">
            @if ($autoridad1)
                {{ $autoridad1->name.' '.$autoridad1->lastname }}<br>
                <span class="text-muted">{{$autoridad1->position ?? ''}}</span>
            @endif
        </td>
        <td width="33%">
            @if ($profesor_guia)
                {{ $profesor_guia->profesor->fullname ?? '' }}<br>
                <span class="text-muted">Profesor Guía</span>
            @else
                <span class="text-muted">Sin profesor guía asignado</span>
            @endif
        </td>
        <td width="33%">
            {{ ($estudiant->representant) ? $estudiant->representant->name : null}}<br>
            <span class="text-muted">Representante</span>
        </td>
    </tr>
</table>
