<table class="table-sm" style=" font-size:0.7rem">
    <tr>
        <td width="33%">
            {{ $autoridad1->name.' '.$autoridad1->lastname }}<br>
            <span class="text-muted">{{$autoridad1->position ?? ''}}</span>
        </td>
        <td width="33%">
            {{ $profesor_guia->profesor->fullname ?? '' }}<br>
            <span class="text-muted">Profesor Guía</span>
        </td>
        <td width="33%">
            {{ $estudiant->representant->name ?? ''}}<br>
            <span class="text-muted">Representante</span>
        </td>
    </tr>
</table>
