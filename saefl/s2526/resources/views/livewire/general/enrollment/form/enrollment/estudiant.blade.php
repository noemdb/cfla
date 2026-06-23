<div class="container-fluid">
    <div class="row">
        <div class="col"> <strong>Información Académica.</strong> </div>
    </div>
    <div class="row py-2">
        <div class="col-sm-12">
            <div class="form-group pb-2">
                @php
                    $name = 'ci_estudiant';
                    $model = 'enrollment.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::number($name, old($name), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control alert alert-secondary p-2 fw-bold',
                    'readonly',
                    'placeholder' => $list_comment[$name],
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group pb-2">
                @php
                    $name = 'name';
                    $model = 'enrollment.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::text($name, old($name), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control alert alert-secondary p-2 fw-bold',
                    'readonly',
                    'placeholder' => $list_comment[$name],
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group pb-2">
                @php
                    $name = 'lastname';
                    $model = 'enrollment.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::text($name, old($name), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control alert alert-secondary p-2',
                    'readonly',
                    'placeholder' => $list_comment[$name],
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row py-2">
        <div class="col-sm-6">
            <div class="form-group pb-2">
                @php
                    $name = 'gender';
                    $model = 'enrollment.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, ['Masculino' => 'Masculino', 'Femenino' => 'Femenino'], old($model), [
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
        <div class="col-sm-6">
            <div class="form-group pb-2">
                @php
                    $name = 'date_birth';
                    $model = 'enrollment.' . $name;
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

    <div class="row py-2">
        <div class="col-sm-4">
            <div class="form-group pb-2">
                @php
                    $name = 'town_hall_birth';
                    $model = 'enrollment.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::text($name, old($name), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group pb-2">
                @php
                    $name = 'state_birth';
                    $model = 'enrollment.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::text($name, old($name), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group pb-2">
                @php
                    $name = 'country_birth';
                    $model = 'enrollment.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_country_birth, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-select',
                    'id' => $model,
                    'placeholder' => 'Selecciones',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row py-2">

        <div class="col">
            <div class="form-group pb-2">
                @php
                    $name = 'dir_address';
                    $model = 'enrollment.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
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

    <div class="row py-2">
        <div class="col-sm-6">
            <div class="form-group pb-2">
                @php
                    $name = 'grado_id';
                    $model = 'enrollment.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_grado, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-select',
                    'id' => $model,
                    'placeholder' => 'Selecciones',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group pb-2">
                @php
                    $name = 'institution';
                    $model = 'enrollment.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_oinstitucions, old($model), [
                    'wire:model.defer' => $model,
                    'class' => 'form-select',
                    'id' => $model,
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>
