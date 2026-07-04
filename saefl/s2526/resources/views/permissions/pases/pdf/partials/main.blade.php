<hr>
<div><strong>Detalles del pase</strong></div>
<table class="table table-sm small">
    <thead  class="thead-inverse"  align="left">
        <tr >
            <th>Tipo</th>
            <th>Motivo</th>
            <th>Descripción</th>
            <th>Fecha</th>
            <th>Duración</th>
            <th>Estado</th>
            <th>Emergencia</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $pase->type ?? null}}</td>
            <td>{{ $pase->motive ?? null}}</td>
            <td>{{ $pase->description ?? null}}</td>
            <td>{{ $pase->date_time->format('Y-m-d H:i a') ?? null}}</td>
            <td>{{ $pase->duration ?? null}} Horas</td>
            <td>{{ $pase->status ?? null}}</td>
            <td>{{  ($pase->status_emergency) ? 'SI':'NO'}}</td>
        </tr>
    </tbody>
</table>
