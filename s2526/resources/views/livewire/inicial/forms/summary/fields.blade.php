{{--
eiplanningwsummary_id
pevaluacion_id
componente
objetivo
aprendizaje_esperado
indicadores
linea_investigacion
enfasis_curriculares
--}}

<div class="container border-bottom">

    <div class="row py-1">

        {{-- <div class="col-2">
            <div class="form-group">
                @php $name = 'lapso_id' ; $model = ''.$name @endphp
                <label for="{{$model}}" class=" font-weight-bold m-0 small">{{$list_comment_summary[$name] ?? ''}}</label>
                {!! Form::select($model,$list_lapso,old($model),['wire:model'=>$model,'class' => 'form-control','id'=>$model,'placeholder'  => 'Selecciones']) !!}
                @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
            </div>
        </div> --}}

        <div class="col-2">
            <div class="form-group">
                @php
                    $name = 'order';
                    $model = 'eiplanningwsummary.' . $name;
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
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'pevaluacion_id';
                    $model = 'eiplanningwsummary.' . $name;
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
                    $model = 'eiplanningwsummary.' . $name;
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
                    $name = 'objetivo';
                    $model = 'eiplanningwsummary.' . $name;
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
                    $name = 'aprendizaje_esperado';
                    $model = 'eiplanningwsummary.' . $name;
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
                    $model = 'eiplanningwsummary.' . $name;
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
                    $model = 'eiplanningwsummary.' . $name;
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
                    $model = 'eiplanningwsummary.' . $name;
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

    {{-- <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php $name = 'finicial' ; $model= 'eiplanningwsummary.'.$name @endphp
                <label for="{{$model}}" class="font-weight-bold m-0 small">{{$list_comment_summary[$name] ?? ''}}</label>
                {!! Form::date($model, old($model), ['wire:model.defer'=>$model,'class' => 'form-control','id'=>$model]); !!}
                @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                @php $name = 'ffinal' ; $model= 'eiplanningwsummary.'.$name @endphp
                <label for="{{$model}}" class=" font-weight-bold m-0 small">{{$list_comment_summary[$name] ?? ''}}</label>
                {!! Form::date($model, old($model), ['wire:model.defer'=>$model,'class' => 'form-control','id'=>$model]); !!}
                @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
            </div>
        </div>        
    </div> --}}

</div>
