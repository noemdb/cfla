<table class="table table-striped table-inverse table-sm small">
    <thead class="thead-inverse">
        <thead>
            <tr>
                <th>N</th>
                <th>Título</th>
                <th>Comentarios</th>
                <th>Finalizada</th>
                <th>Observación</th>
                <th>Profesor</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lessons as $lesson)
                @php $autor = $lesson->autor; @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $lesson->title }}</td>
                    <td>{{ $lesson->comments }}</td>
                    <td>{{ ($lesson->status) ? '-SI-' : '-NO-' }}</td>
                    <td>{{ $lesson->observations }}</td>
                    <td>{{ $lesson->autor->username ?? null }}</td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                            <button type="button" class="btn btn-info">
                                <i class="fa fa-info" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="btn btn-warning" wire:click="edit({{$lesson->id}})">
                                <i class="fas fa-pen" aria-hidden="true"></i>
                            </button>
                        </div>    
                    </td>
                </tr>
            @empty

                <tr>
                    <td colspan="3">No hay datos</td>
                </tr>
            @endforelse
        </tbody>
</table>
{{-- 
status
observations
author_id --}}