<div class="alert alert-light" role="alert">

    <div class="font-weight-bold border-bottom mb-2"> Descripción de la falta: {{ $incident->fault ?? null }}</div>

    <div class="">
        <div class="d-flex">
            <div class="form-group flex-grow-1">
                @php
                    $name = 'corrective_id';
                    $model = 'incident_action.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_correctives, old($model), [
                    'wire:model' => $model,
                    'class' => 'form-control text-wrap',
                    'id' => $model,
                    'placeholder' => 'Selecciones',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="button" class="small mb-0 font-weight-bold">Acción</label>
                {!! Form::button('Guardar', ['class' => 'btn btn-primary mt-0', 'wire:click' => "saveAction($incident->id)"]) !!}
            </div>
            <div wire:loading wire:target="{{ $model }}">
                <div class=" d-flex align-items-center justify-content-evenly">
                    <div class="px-4">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="sr-only">Procesando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
