<div class="container border-bottom">

    {{-- eievaluationk_id
    pevaluacion_id
    fecha
    nombre_ninos
    aprendizaje_alcanzado
    componente
    indicadores
    instrumento
    observacion --}}

    <div class="row py-1">
        <div class="col">
            <div class="d-flex">
                <div class="form-group w-25 pr-2">
                    @php
                        $name = 'lapso_id';
                        $model = '' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment_position[$name] ?? '' }}</label>
                    {!! Form::select($model, $list_lapso, old($model), [
                        'wire:model' => $model,
                        'class' => 'form-control',
                        'id' => $model,
                        'placeholder' => 'Selecciones',
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group w-25 pr-2">
                    @php
                        $name = 'order';
                        $model = 'eievaluationp.' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class="font-weight-bold m-0 small">{{ $list_comment_position[$name] ?? '' }}</label>
                    {!! Form::selectRange($model, 1, 20, old($name), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => 'Seleccione',
                        'required',
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group w-50">
                    @php
                        $name = 'pevaluacion_id';
                        $model = 'eievaluationp.' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment_position[$name] ?? '' }}</label>
                    {!! Form::select($model, $list_pevaluacion, old($model), [
                        'wire:model' => $model,
                        'class' => 'form-control',
                        'id' => $model,
                        'placeholder' => 'Selecciones',
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                @php
                    $name = 'fecha';
                    $model = 'eievaluationp.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_position[$name] ?? '' }}</label>
                {!! Form::date($model, old($model), ['wire:model.defer' => $model, 'class' => 'form-control', 'id' => $model]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'nombre_ninos';
                    $model = 'eievaluationp.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_position[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_position[$name],
                    'rows' => '4',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'aprendizaje_alcanzado';
                    $model = 'eievaluationp.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_position[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_position[$name],
                    'rows' => '4',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="row py-1">
    <div class="col">
        <div class="form-group">
            @php
                $name = 'componente';
                $model = 'eievaluationp.' . $name;
            @endphp
            <label for="{{ $model }}"
                class="font-weight-bold m-0 small">{{ $list_comment_position[$name] ?? '' }}</label>
            {!! Form::textarea($model, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment_position[$name],
                'rows' => '4',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            @php
                $name = 'indicadores';
                $model = 'eievaluationp.' . $name;
            @endphp
            <label for="{{ $model }}"
                class="font-weight-bold m-0 small">{{ $list_comment_position[$name] ?? '' }}</label>
            {!! Form::textarea($model, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment_position[$name],
                'rows' => '4',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="row py-1">
    <div class="col">
        <div class="form-group">
            @php
                $name = 'instrumento';
                $model = 'eievaluationp.' . $name;
            @endphp
            <label for="{{ $model }}"
                class="font-weight-bold m-0 small">{{ $list_comment_position[$name] ?? '' }}</label>
            {!! Form::textarea($model, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment_position[$name],
                'rows' => '4',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            @php
                $name = 'observacion';
                $model = 'eievaluationp.' . $name;
            @endphp
            <label for="{{ $model }}"
                class="font-weight-bold m-0 small">{{ $list_comment_position[$name] ?? '' }}</label>
            {!! Form::textarea($model, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment_position[$name],
                'rows' => '4',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

</div>
