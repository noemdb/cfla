<table class="table table-striped table-sm table-hover">
    <thead class="thead-light">
        <tr>
            <th>Código</th>
            <th>Abreviación</th>
            <th>Nombre</th>
            <th>H.Teóricas</th>
            <th>H.Prácicas</th>
            <th>Prelaciones</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($pensums as $pensum)
            @php $asignatura = $pensum->asignatura; @endphp
                <tr data-id="{{$pensum->id}}">
                    <td>{{$asignatura->code ?? ''}}</td>
                    <td>{{$asignatura->code_sm ?? ''}}</td>
                    <td>{{$asignatura->name ?? ''}}</td>
                    <td>{{$asignatura->hour_t_week ?? ''}}</td>
                    <td>{{$asignatura->hour_p_week ?? ''}}</td>
                    <td>{{$asignatura->prelacions ?? ''}}</td>
                </tr>
            @endforeach
        </tbody>
</table>
