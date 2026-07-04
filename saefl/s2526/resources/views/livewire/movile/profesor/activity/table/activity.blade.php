@php
    $name = 'pevaluacion_id';
    $model = $name;
    $comment = $list_comment[$name];
@endphp
<label for="{{ $model }}" class="fw-bold m-0 small text-muted">{{ $comment ?? '' }}</label>
{!! Form::select($model, $list_pevaluacions, old($model), [
    'wire:model' => $model,
    'class' => 'form-select',
    'id' => $model,
    'placeholder' => 'Seleccione',
]) !!}

<table class="table table-sm small table-striped">
    <thead>
        <tr>
            <th>N°</th>
            <th>Actividad</th>
            <th>Acción</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($activities as $activity)
            @php $pevaluacion = $activity->pevaluacion; @endphp
            <tr class="small table-{{ $activity->status ? 'success' : 'warning' }}">
                <td>
                    {{ $loop->iteration }}
                    @if ($activity->status)
                        <i class="fa fa-check text-success fw-bold" aria-hidden="true"></i>
                    @endif
                </td>

                <td title="{{ $activity->comments }}">
                    <div class="border rounded mb-2 border-{{ $activity->status ? 'success' : 'warning' }}">
                        <div class="text-muted fw-bold">Contenido:</div>
                        <div class="text-muted">
                            <div class="text-left border-bottom border-light">{{ $activity->references ?? null }}</div>
                            <div class="text-left border-bottom border-light">{{ $activity->thematic }}</div>
                            {{-- <div class="text-left text-muted border-bottom">{{ $activity->topic }}</div> --}}
                            <div class="text-left text-muted">{{ $activity->observations }}</div>
                        </div>
                    </div>
                    <div class="border rounded mb-2 border-{{ $activity->status ? 'success' : 'warning' }}">
                        <div class="text-wrap border border-light">
                            <span class="text-muted fw-bold">Comentario:</span> {{ $activity->comments }}
                        </div>
                        {{-- <hr class="m-1 p-1"> --}}
                        <div class="text-wrap"><span class="text-muted fw-bold">Observación:</span>
                            {{ $pevaluacion->observations }}</div>
                    </div>

                </td>

                <td>
                    @php $disabled = ($activity->status) ? true : false; @endphp
                    <div class="btn-group-vertical btn-group-sm" role="group" aria-label="Small button group">
                        @php $disabled = ($activity->status) ? true : false; @endphp
                        <button type="button" class="btn btn-warning" {{ $disabled ? 'disabled' : null }}
                            wire:click="setEditActivity({{ $activity->id }})">
                            <i class="fas fa-pen" aria-hidden="true"></i>
                        </button>
                        {{-- <button type="button" class="btn btn-danger" {{ ($disabled) ? 'disabled' : null}} wire:click="alertQuestionDefault({{$activity->id}},'deleteActivity')"> --}}
                        <button type="button" class="btn btn-danger" {{ $disabled ? 'disabled' : null }}
                            wire:click="deleteActivity({{ $activity->id }},'deleteActivity')">
                            <i class="fa fa-window-close" aria-hidden="true"></i>
                        </button>

                        <a title="Plan de Actividades" class="btn btn-success"
                            href="{{ route('profesors.activities.format', $pevaluacion->id) }}" role="button"
                            target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>

                        <a title="Resumen del Plan de Actividades" class="btn btn-secondary"
                            href="{{ route('profesors.activities.resume', $pevaluacion->id) }}" role="button"
                            target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>
                    </div>

                </td>
                <!-- Muestra las demás propiedades de la lección en las columnas correspondientes -->
            </tr>
        @empty

            <tr>
                <td colspan="3">No hay datos</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $activities->links() }}
