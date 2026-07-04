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

    <div class="pt-1 px-1">
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
    </div>

</div>

@if ($lessons->isNotEmpty())
    <div>Listado</div>
    @include('livewire.movile.evaluacion.table.lessons')
@else
    <div class="alert alert-warning p-2 m-2" role="alert">
        <div class="text-center fw-bold">Prueba piloto</div>
        <div class="fw-light">Control y seguimiento de las lecciones de los docentes</div>
    </div>
@endif
