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
    'placeholder' => 'Selecciones',
]) !!}

<table class="table table-sm small table-striped">
    <thead>
        <tr>
            <th>Contenido</th>
            <th>Comentario/Observación</th>
            <th>Acción</th>
            <!-- Agrega las demás columnas según tus necesidades -->
        </tr>
    </thead>
    <tbody>
        @forelse ($lessons as $lesson)
            <tr>
                <td title="{{ $lesson->content }}">
                    <div class="text-muted border-bottom">{{ $lesson->planned ? f_date($lesson->planned) : null }}
                    </div>
                    <div class="text-truncate text-wrap">{{ $lesson->title }}</div>
                    <div class="text-truncate text-wrap">{{ $lesson->content ?? null }}</div>
                </td>
                <td title="{{ $lesson->comments }}">
                    <div class="text-truncate text-wrap">
                        <span class=" fw-bold">Comentario:</span> {{ $lesson->comments }}
                    </div>
                    <hr class="m-1 p-1">
                    <div class="text-truncate text-wrap"><span class=" fw-bold">Observación:</span>
                        {{ $lesson->observations }}</div>
                </td>
                <td>
                    <fieldset {{ $lesson->observations ? 'disabled' : null }}>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                            <button type="button" class="btn btn-warning" wire:click="edit({{ $lesson->id }})">
                                <i class="fas fa-pen" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="btn btn-danger"
                                wire:click="alertQuestion({{ $lesson->id }},'remove')"
                                wire:key="btn-lesson-delete-{{ $lesson->id }}">
                                <i class="fa fa-window-close" aria-hidden="true"></i>
                            </button>
                        </div>
                    </fieldset>
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

{{ $lessons->links() }}
