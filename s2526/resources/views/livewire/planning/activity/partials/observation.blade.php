<div class="container-fluid border rounded p-1 m-1 shadow-lg">

    <div class="alert alert-primary mb-0" role="alert">
        <strong>Ingresar Observación</strong>
    </div>


    <div class="row py-1">

        <div class="col">
            <div class="p-2">
                <div class="text-muted">
                    @php
                        $profesor = $pevaluacion->profesor;
                        $pensum = $pevaluacion->pensum;
                        $grado = $pevaluacion->pensum->grado;
                        $asignatura = $pensum->asignatura;
                    @endphp
                    <div class=""><strong>Profesor:</strong> <small> {{ $profesor->fullname }}</small></div>
                    <div class=""><strong>A.Formación:</strong> <small> {{ $asignatura->name }}</small></div>
                </div>

                <div class="border rounded p-2 m-2">

                    <div class="form-group">
                        @php
                            $name = 'observations';
                            $model = '' . $name;
                        @endphp
                        <label for="{{ $model }}" class="m-0 font-weight-bold">Observaciones</label>
                        {!! Form::textarea($model, old($model), [
                            'wire:model.defer' => $model,
                            'class' => 'form-control',
                            'placeholder' => 'Comentario',
                            'rows' => '4',
                        ]) !!}
                        @error($model)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
                        {!! Form::button('Guardar', [
                            'class' => 'form-control btn pt-1 mt-1 btn-info w-75 btn-sm',
                            'wire:click.prevent' => 'saveObservation()',
                        ]) !!}
                        {!! Form::button('Cerrar', [
                            'class' => 'form-control btn pt-1 mt-1 btn-secondary w-25 btn-sm',
                            'wire:click.prevent' => 'close()',
                        ]) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
