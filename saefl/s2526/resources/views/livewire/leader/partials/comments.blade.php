<div class="overlay">
    <div class="container-fluid border rounded p-1 m-1">

        <div class="alert alert-info" role="alert">
            <strong>Ingresar Comentario</strong>
        </div>


        <div class="row py-1">

            <div class="col">

                <div class="px-2">

                    <div class="form-group">
                        @php
                            $name = 'comments';
                            $model = '' . $name;
                        @endphp
                        <label for="{{ $model }}" class=" font-weight-bold m-0 small">Comentario</label>
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

                    <div class="form-group">
                        <label class="font-weight-bold m-0 small d-block">Estado de aprobación</label>
                        
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" 
                                   name="status" 
                                   id="status_1" 
                                   value="1"
                                   wire:model.defer="status">
                            <label class="form-check-label" for="status_1">Aprobado</label>
                        </div>
                        
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" 
                                   name="status" 
                                   id="status_0" 
                                   value="0"
                                   wire:model.defer="status">
                            <label class="form-check-label" for="status_0">En revisión</label>
                        </div>
                        
                        @error('status')
                            <span class="text-danger small d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
                        {!! Form::button('Guardar', [
                            'class' => 'form-control btn pt-1 mt-1 btn-info w-75 btn-sm',
                            'wire:click' => 'saveComent()',
                        ]) !!}
                        {!! Form::button('Cerrar', [
                            'class' => 'form-control btn pt-1 mt-1 btn-secondary w-25 btn-sm font-weight-bold',
                            'wire:click' => 'close()',
                        ]) !!}
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>
