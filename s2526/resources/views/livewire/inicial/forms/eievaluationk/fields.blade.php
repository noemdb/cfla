<div class="container border-bottom">

    <div class="row py-1">
        <div class="col">
            <div class="d-flex">
                <div class="form-group pr-2 w-50">
                    @php
                        $name = 'grado_id';
                        $model = 'eievaluationk.' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::select($model, $list_grado, old($model), [
                        'wire:model' => $model,
                        'class' => 'form-control',
                        'id' => $model,
                        'placeholder' => 'Selecciones',
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group w-50">
                    @php
                        $name = 'seccion_id';
                        $model = 'eievaluationk.' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::select($model, $list_seccion, old($model), [
                        'wire:model.defer' => $model,
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
                    $name = 'lapso_id';
                    $model = 'eievaluationk.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_lapso, old($model), [
                    'wire:model.defer' => $model,
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

    {{-- 
    <div class="row py-1"> 
        <div class="col">
            <div class="form-group">
                @php $name = 'lapso_id' ; $model = 'eievaluationk.'.$name @endphp
                <label for="{{$model}}" class=" font-weight-bold m-0 small">{{$list_comment[$name] ?? ''}}</label>
                {!! Form::select($model,$list_lapso,old($model),['wire:model.defer'=>$model,'class' => 'form-control','id'=>$model,'placeholder'  => 'Selecciones']) !!}
                @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                @php $name = 'asistencia' ; $model = 'eievaluationk.'.$name @endphp
                <label for="{{$model}}" class=" font-weight-bold m-0 small">{{$list_comment[$name] ?? ''}}</label>
                {!! Form::number($name, old($name), ['wire:model.defer'=>$model,'class' => 'form-control','placeholder'=>$list_comment[$name]]) !!}
                @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
            </div>
        </div> 
    </div>
    --}}

    <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'observaciones';
                    $model = 'eievaluationk.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
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
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'recomendacion';
                    $model = 'eievaluationk.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
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
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'finicial';
                    $model = 'eievaluationk.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::date($model, old($model), ['wire:model.defer' => $model, 'class' => 'form-control', 'id' => $model]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'ffinal';
                    $model = 'eievaluationk.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::date($model, old($model), ['wire:model.defer' => $model, 'class' => 'form-control', 'id' => $model]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

</div>
