<div class="container-fluid border rounded p-2 mb-2">
    <div class="row">
        <div class="col"> <strong>Información Académica.</strong> </div>
    </div>
    <div class="row py-2">
        <div class="col-sm-12">
            <div class="form-group pb-2">
                @php
                    $name = 'ci_estudiant';
                    $model = '' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::number('ci_estudiant', $enrollment->ci_estudiant, [
                    'class' => 'form-control alert alert-secondary p-2 fw-bold',
                    'readonly',
                    'placeholder' => $list_comment[$name],
                    'required',
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
                    $model = '' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::text($name, $enrollment->name, [
                    'class' => 'form-control alert alert-secondary p-2 fw-bold',
                    'readonly',
                    'placeholder' => $list_comment[$name],
                    'required',
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
                    $model = '' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::text($name, $enrollment->lastname, [
                    'class' => 'form-control alert alert-secondary p-2',
                    'readonly',
                    'placeholder' => $list_comment[$name],
                    'required',
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
                    $model = '' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, ['Masculino' => 'Masculino', 'Femenino' => 'Femenino'], $enrollment->gender, [
                    'class' => 'form-control',
                    'id' => $model,
                    'placeholder' => 'Selecciones',
                    'required',
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
                    $model = '' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::date($model, $enrollment->date_birth, ['class' => 'form-control', 'id' => $model, 'required']) !!}
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
                    $model = '' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::text($name, $enrollment->town_hall_birth, [
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                    'required',
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
                    $model = '' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::text($name, $enrollment->state_birth, [
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                    'required',
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
                    $model = '' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_country_birth, $enrollment->country_birth, [
                    'class' => 'form-select',
                    'id' => $model,
                    'placeholder' => 'Selecciones',
                    'required',
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
                    $model = '' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::textarea($model, $enrollment->dir_address, [
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                    'rows' => '4',
                    'required',
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
                    $model = '' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_grado, $enrollment->grado_id, [
                    'class' => 'form-select',
                    'id' => $model,
                    'placeholder' => 'Selecciones',
                    'required',
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
                    $model = '' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_oinstitucions, $enrollment->institution, [
                    'class' => 'form-select',
                    'id' => $model,
                    'required',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>



</div>
