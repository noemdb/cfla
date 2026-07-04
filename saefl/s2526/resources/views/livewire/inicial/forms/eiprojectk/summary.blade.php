<div class="container border-bottom">

    <div class="row py-1">
        <div class="col-2">
            <div class="form-group">
                @php
                    $name = 'lapso_id';
                    $model = '' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment_summary[$name] ?? '' }}</label>
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
        </div>
        <div class="col-2">
            <div class="form-group">
                @php
                    $name = 'order';
                    $model = 'eiprojectsummary.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_summary[$name] ?? '' }}</label>
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
        </div>
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'pevaluacion_id';
                    $model = 'eiprojectsummary.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment_summary[$name] ?? '' }}</label>
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

    <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'componente';
                    $model = 'eiprojectsummary.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_summary[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_summary[$name],
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
                    $name = 'aprendizaje_esperado';
                    $model = 'eiprojectsummary.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_summary[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_summary[$name],
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
                    $name = 'objetivo';
                    $model = 'eiprojectsummary.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_summary[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_summary[$name],
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
                    $model = 'eiprojectsummary.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_summary[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_summary[$name],
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
                    $name = 'linea_investigacion';
                    $model = 'eiprojectsummary.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_summary[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_summary[$name],
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
                    $name = 'enfasis_curriculares';
                    $model = 'eiprojectsummary.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_summary[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_summary[$name],
                    'rows' => '4',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

</div>
