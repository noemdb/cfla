{{-- area
rol
cargo_id
group
assit_schedule_id
descripcion
finicial
ffinal
status_schedule
 --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<fieldset @admon disabled @endadmon>

    <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'area';
                    $model = 'rol.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment_rol[$name] ?? '' }}</label>
                {!! Form::select('area', $list_area, old('area'), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_rol[$name],
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'rol';
                    $model = 'rol.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment_rol[$name] ?? '' }}</label>
                {!! Form::select('rol', $list_rol, old('rol'), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_rol[$name],
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
                    $name = 'cargo_id';
                    $model = 'rol.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment_rol[$name] ?? '' }}</label>
                {!! Form::select('cargo_id', $list_cargos, old('cargo_id'), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_rol[$name],
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'rol';
                    $model = 'rol.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment_rol[$name] ?? '' }}</label>
                {!! Form::select('rol', $list_rol, old('rol'), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment_rol[$name],
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    @admon
    @endadmon

</fieldset>

<div class="row">

    <!-- Fecha Inicial (CORRECTO) -->
    <div class="col">
        <div class="form-label-group pb-1">
            @php
                $name = 'finicial';
                $model = 'rol.' . $name;
            @endphp
            <label for="{{ $model }}" class="font-weight-bold m-0 small">
                {{ $list_comment_rol[$name] ?? '' }}
                <span>[{{ isset($rol->finicial) ? Carbon\Carbon::parse($rol->finicial)->format('d-m-Y') : '' }}]</span>
            </label>
            {!! Form::date('finicial', $rol->finicial ?? null, [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'id' => 'finicial',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- Fecha Final (CORREGIDO) -->
    <div class="col">
        <div class="form-label-group pb-1">
            @php
                $name = 'ffinal';
                $model = 'rol.' . $name;
            @endphp {{-- ¡CORREGIDO AQUI! --}}
            <label for="{{ $model }}" class="font-weight-bold m-0 small">
                {{ $list_comment_rol[$name] ?? '' }}
                <span>[{{ isset($rol->ffinal) ? Carbon\Carbon::parse($rol->ffinal)->format('d-m-Y') : '' }}]</span>
            </label>
            {!! Form::date('ffinal', $rol->ffinal ?? null, [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'id' => 'ffinal',
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
                $name = 'group';
                $model = 'rol.' . $name;
            @endphp
            <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment_rol[$name] ?? '' }}
                <span>{{ $rol->group }}</span></label>
            {!! Form::select('group', $list_rols_group, old('group'), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment_rol[$name],
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            @php
                $name = 'status_schedule';
                $model = 'rol.' . $name;
            @endphp
            <label for="{{ $model }}"
                class=" font-weight-bold m-0 small">{{ $list_comment_rol[$name] ?? '' }}</label>
            {!! Form::select('rol', [true => 'SI', false => 'NO'], old('rol'), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment_rol[$name],
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col">
        <div class="form-group">
            @php
                $name = 'assit_schedule_id';
                $model = 'rol.' . $name;
            @endphp
            <label for="{{ $model }}"
                class=" font-weight-bold m-0 small">{{ $list_comment_rol[$name] ?? '' }}</label>
            {!! Form::select('rol', $list_assit_schedule, old('rol'), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment_rol[$name],
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>

</div>




@section('stylesheet')
    @parent

    <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.css') }}" rel="stylesheet">
@endsection
