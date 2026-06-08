@php $representant = $estudiant->representant; @endphp
@php $profesor_guia = ($estudiant->profesor_guia) ? $estudiant->profesor_guia:null; @endphp

<table class="table-sm" style=" font-size:0.7rem">
    <tr>
        <td align="center" width="33%">
            @if ($autoridad1)
                {{ $autoridad1->name.' '.$autoridad1->lastname }}<br>
                <span class="text-muted">{{$autoridad1->position ?? ''}}</span>
            @else
                <span class="text-muted">Sin autoridad asignada</span>
            @endif
        </td>
        <td align="center" width="33%">
            @if ($profesor_guia)
                {{ $profesor_guia->fullname ?? '' }}<br>
                <span class="text-muted">Profesor Guía</span>
            @else
                <span class="text-muted">Sin profesor guía asignado</span>
            @endif
        </td>
        <td align="center" width="33%">
            {{ $representant->name ?? ''}}<br>
            <span class="text-muted">Representante</span>
        </td>
    </tr>
</table>
