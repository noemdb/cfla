<div class="container border-bottom">

    {{-- 
eiprojectk_id
posibles_temas_interes
eleccion_tema_nombre
que_sabe
que_desean_aprender
que_necesitamos
quienes_nos_pueden_apoyar 
--}}

    <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'eleccion_tema_nombre';
                    $model = 'eiprojectreview.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_review[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_review[$name],
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
                    $name = 'posibles_temas_interes';
                    $model = 'eiprojectreview.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_review[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_review[$name],
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
                    $name = 'que_sabe';
                    $model = 'eiprojectreview.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_review[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_review[$name],
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
                    $name = 'que_desean_aprender';
                    $model = 'eiprojectreview.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_review[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_review[$name],
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
                    $name = 'que_necesitamos';
                    $model = 'eiprojectreview.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_review[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_review[$name],
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
                    $name = 'quienes_nos_pueden_apoyar';
                    $model = 'eiprojectreview.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="font-weight-bold m-0 small">{{ $list_comment_review[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_review[$name],
                    'rows' => '4',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

</div>
