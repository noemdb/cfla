{{--
name
token
description
motive
date
status_active
attachment
--}}
<div class="container-fluid border-bottom">

    <div class="row py-1">
        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'name';
                    $model = 'competition.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::textarea($name, old($name), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
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
                    $name = 'description';
                    $model = 'competition.' . $name;
                @endphp
                <label for="{{ $model }}" class="m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
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
                    $name = 'motive';
                    $model = 'competition.' . $name;
                @endphp
                <label for="{{ $model }}" class="m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                    'rows' => '4',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row py-1">
        <div class="col-6">
            <div class="form-group">
                @php
                    $name = 'date';
                    $model = 'competition.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::date($model, old($model), ['wire:model.defer' => $model, 'class' => 'form-control', 'id' => $model]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-6">
            @if ($modeCreator)
                <div class="form-group">
                    @php
                        $name = 'cant_group';
                        $model = '' . $name;
                    @endphp
                    <label for="{{ $name }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {{-- {!! Form::select($model,$list_seccion,old($model),['wire:model.defer'=>$model,'class' => 'form-control','id'=>$model,'placeholder'  => 'Selecciones']) !!} --}}
                    {!! Form::selectRange($model, 1, 9, $model, [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => 'Selecciones',
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            @endif
        </div>
    </div>

</div>
