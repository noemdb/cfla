<div class="overlay">

    <div class="alert alert-warning" role="alert">
        <div class="d-flex justify-content-between">
            <div class="h5">
                <strong>Editar evaluación</strong>
            </div>
            <div>
                <span class="h4 text-muted font-weight-bold" wire:click="close()" style="cursor: pointer">&times;</span>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="text-start  p-2 m-2">

        <div class="form-group">
            @php
                $name = 'description';
                $model = '' . $name;
            @endphp
            {!! Form::label('description', 'Descripción / Estrategia / Contenidos', [
                'class' => 'm-0 font-weight-bold text-muted',
            ]) !!}
            {!! Form::textarea('description', old('description'), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => 'Descripción / Estrategía / Contenidos',
                'id' => 'description',
                'rows' => '4',
                'required',
            ]) !!}
        </div>

        <div class="form-group pb-1">
            @php
                $name = 'fecha';
                $model = '' . $name;
            @endphp
            {!! Form::label('fecha', 'Fecha', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            {!! Form::date($model, old($model), ['wire:model.defer' => $model, 'class' => 'form-control', 'id' => $model]) !!}
            {{-- {!! Form::text('fecha', old('fecha'), ['wire:model.defer'=>$model,'class'=>'form-control datepicker','placeholder'=>'Fecha','id'=>'fecha','required'=>'required','maxlength'=>"10"]); !!} --}}
        </div>

        <button type="submit" class="btn btn-primary btn-block w-100">Guardar</button>

    </form>

</div>
