<div>

    <div class="text-start">

        <div class="d-flex justify-content-between">
            <div class="px-1">
                @php
                    $name = 'lapso_id';
                    $model = $name;
                    $comment = $list_comment[$name];
                @endphp
                <label for="{{ $model }}" class="fw-bold m-0 small text-muted">{{ $comment ?? '' }}</label>
                {!! Form::select($model, $list_lapsos, old($model), [
                    'wire:model' => $model,
                    'class' => 'form-select',
                    'id' => $model,
                    'placeholder' => 'Selecciones',
                ]) !!}
            </div>
            <div class="px-1">
                @php
                    $name = 'profesor_id';
                    $model = $name;
                    $comment = $list_comment[$name];
                @endphp
                <label for="{{ $model }}" class="fw-bold m-0 small text-muted">{{ $comment ?? '' }}</label>
                {!! Form::select($model, $list_profesor, old($model), [
                    'wire:model' => $model,
                    'class' => 'form-select',
                    'id' => $model,
                    'placeholder' => 'Selecciones',
                ]) !!}
            </div>
        </div>

    </div>

    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>Profesor</th>
                <th>Á.Formación</th>
                <th>Acción</th>
                <!-- Agrega las demás columnas según tus necesidades -->
            </tr>
        </thead>
        <tbody>
            @forelse ($pevaluacions as $item)
                <tr>
                    <td>
                        <div class="text-muted border-bottom">{{ $item ? $item->asignatura_name_full : null }}</div>
                    </td>
                    <td title="{{ $item->description }}">
                        {{ $item->asignatura_code_name }}
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                            <button type="button" class="btn btn-warning" wire:click="edit({{ $item->id }})">
                                {{-- <i class="fas fa-pen" aria-hidden="true"></i> --}}
                                <i class="fa fa-plus-square" aria-hidden="true"></i>
                            </button>
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

    {{ $pevaluacions->links() }}
</div>
