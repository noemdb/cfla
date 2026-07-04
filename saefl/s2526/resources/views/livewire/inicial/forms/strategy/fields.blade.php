{{--
eiplanningwk_id
momento_rutina_diaria
lunes
martes
miercoles
jueves
viernes
--}}

<div class="container border-bottom">

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                @php
                    $name = 'order';
                    $model = 'eiplanningwstrategy.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_strategy[$name] ?? '' }}</label>
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
        <div class="col-6">
            <div class="form-group">
                @php
                    $name = 'momento_rutina_diaria';
                    $model = 'eiplanningwstrategy.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment_strategy[$name] ?? '' }}</label>
                {!! Form::select($model, $list_moment, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'id' => $model,
                    'placeholder' => 'Seleccione',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row py-1">
        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'lunes';
                    $model = 'eiplanningwstrategy.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_strategy[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_strategy[$name],
                    'rows' => '4',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row py-1">
        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'martes';
                    $model = 'eiplanningwstrategy.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_strategy[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_strategy[$name],
                    'rows' => '4',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'miercoles';
                    $model = 'eiplanningwstrategy.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_strategy[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_strategy[$name],
                    'rows' => '4',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row py-1">
        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'jueves';
                    $model = 'eiplanningwstrategy.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_strategy[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_strategy[$name],
                    'rows' => '4',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'viernes';
                    $model = 'eiplanningwstrategy.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_strategy[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_strategy[$name],
                    'rows' => '4',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

</div>
