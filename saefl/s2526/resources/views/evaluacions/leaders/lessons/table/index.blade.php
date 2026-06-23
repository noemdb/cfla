<table class="table table-striped table-inverse table-sm small">
    <thead class="thead-inverse">
        <thead>
            <tr>
                <th>N</th>
                <th>Plan de Evaluación</th>
                <th>Título</th>
                <th>Comentarios</th>
                <th>Finalizada</th>
                <th>Observaciones</th>
                <th>Profesor</th>
                <th>Evidencias</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lessons as $lesson)
                @php 
                    $autor = $lesson->autor; 
                    $area_conocimiento = $lesson->getAreaConocimientoLeaderId(Auth::id()); 
                    $pevaluacion = $lesson->pevaluacion; 
                    $lapso = ($pevaluacion) ? $pevaluacion->lapso : null; 
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ ($pevaluacion) ? $pevaluacion->asignatura_name: null }} 
                        <div class=" font-weight-bold text-muted">{{ ($lapso) ? $lapso->name: null}}</div>
                        {{-- <div class=" font-weight-bold text-muted">-. {{ ($area_conocimiento) ? $area_conocimiento->name: null}}</div> --}}
                    </td>
                    <td>{{ $lesson->title }}</td>
                    <td>{{ $lesson->comments }}</td>
                    <td class="text-center">{{ ($lesson->status) ? '-SI-' : '-NO-' }}</td>
                    <td>{{ $lesson->observations }}</td>
                    <td>{{ $lesson->autor->username ?? null }}</td>
                    <td>
                        @if ($lesson->evidence)
                            {{-- <i class="fa fa-file-image p-2 border rounded shadow-sm text-danger pointer" aria-hidden="true" wire:click="showImagen({{$lesson->id}})"></i> --}}

                            <button type="button" class="btn btn-light">
                                <i class="fa fa-file-image p-2 border rounded shadow-sm text-danger pointer" aria-hidden="true" wire:click="showImagen({{$lesson->id}})"></i>
                            </button>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                            <button type="button" class="btn btn-info">
                                <i class="fa fa-info" aria-hidden="true"></i>
                            </button>
                            {{-- <button type="button" class="btn btn-warning" wire:click="edit({{$lesson->id}})">
                                <i class="fas fa-pen" aria-hidden="true"></i>
                            </button> --}}
                        </div>    
                    </td>
                </tr>
            @empty

                <tr>
                    <td colspan="9">No hay datos</td>
                </tr>
            @endforelse
        </tbody>
</table>
{{-- 
status
observations
author_id --}}