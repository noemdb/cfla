<div class="container-fluid border rounded p-1 m-1">

    <div class="alert alert-info" role="alert">
        <strong>Ingresar Comentario</strong>
    </div>

    <div class="row py-1">

        <div class="col">

            <div class="d-flex">
                <div class="px-2">
                    @php
                        $pevaluacion = $activity->pevaluacion;
                        $profesor = $pevaluacion->profesor;
                        $pensum = $pevaluacion->pensum;
                        $grado = $pevaluacion->pensum->grado;
                        $asignatura = $pensum->asignatura;
                    @endphp
                    <div class=""><strong>T.Generador/Énfasis:</strong> <small> {{ $activity->topic }}</small>
                    </div>
                    <div class=""><strong>T.Temático/T.Indispensable:</strong> <small>
                            {{ $activity->thematic }}</small></div>
                    <div class=""><strong>Referentes/Ético</strong> <small> {{ $activity->references }}</small>
                    </div>
                    <div class=""><strong>Profesor:</strong> <small> {{ $profesor->fullname }}</small></div>
                    <div class=""><strong>A.Formación:</strong> <small> {{ $asignatura->name }}</small></div>
                    <hr class="p-0 m-0">
                    <div class=""><strong>Comentario:</strong> {{ $activity->comments }}</div>
                </div>
                <div class="flex-grow-1">
                    <div class="form-group">
                        @php
                            $name = 'comments';
                            $model = '' . $name;
                        @endphp
                        <label for="{{ $model }}" class="m-0 small">Comentario</label>
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
                    {{-- <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm" wire:click="saveComent()">Guardar</button>                    
                    </div> --}}

                    <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
                        {!! Form::button('Guardar', [
                            'class' => 'form-control btn pt-1 mt-1 btn-info w-75 btn-sm',
                            'wire:click' => 'saveComent()',
                        ]) !!}
                        {!! Form::button('Cerrar', [
                            'class' => 'form-control btn pt-1 mt-1 btn-secondary w-25 btn-sm',
                            'wire:click' => 'close()',
                        ]) !!}
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
