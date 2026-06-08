@include('admin.elements.forms.errors')

@include('admin.elements.messeges.oper_ok')

{{-- 'user_id','area','rol','cargo_id','group','assit_schedule_id','descripcion','finicial','ffinal' --}}

<div class="form-label-group pb-1">

    {!! Form::select('area', $area_list, old('area'), ['class' => 'form-control', 'placeholder' => 'Área']) !!}
    {{-- <label for="ranareago">{{ trans('validation.attributes.area') }}</label> --}}

</div>

<div class="form-label-group pb-1">

    {!! Form::select('rol', $rol_list, old('rol'), ['class' => 'form-control', 'placeholder' => 'Rol']) !!}
    {{-- <label for="rol">{{ trans('validation.attributes.rol') }}</label> --}}

</div>
<div class="form-label-group pb-1">

    {!! Form::select('group', $list_rols_group, old('rol'), [
        'class' => 'form-control',
        'placeholder' => 'Agrupación',
    ]) !!}
    {{-- <label for="rol">{{ trans('validation.attributes.rol') }}</label> --}}

</div>

<div class="form-label-group pb-1">
    {!! Form::select('cargo_id', $list_cargos, old('cargo_id'), [
        'class' => 'form-control',
        'placeholder' => 'Cargo que ejerce',
    ]) !!}
    {{-- <label for="rol">{{ trans('validation.attributes.rol') }}</label> --}}
</div>

<div class="form-label-group pb-1">
    {!! Form::select('assit_schedule_id', $list_assit_schedule, old('cargo_id'), [
        'class' => 'form-control',
        'placeholder' => 'Horario Asignado',
    ]) !!}
    {{-- <label for="rol">{{ trans('validation.attributes.rol') }}</label> --}}
</div>



<div class="form-label-group pb-1">
    {!! Form::text('descripcion', old('descripcion'), [
        'class' => 'form-control',
        'placeholder' => 'descripcion',
        'id' => 'descripcion',
        'required',
    ]) !!}
    {{-- <input type="text" id="username" name="username" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" placeholder="Nombre de Usuario" value="{{ old('username') }}"> --}}
    <label for="descripcion">Descripción</label>

</div>

<label class="mb-0 pb-0 mt-1 pt-1 font-weight-bold text-muted" for="worker_order">Considerado para la Asistencia</label>
<div class="form-label-group pb-1">
    {!! Form::select('status_schedule', [true => 'SI', false => 'NO'], old('status_schedule'), [
        'class' => 'form-control',
        'placeholder' => 'Considerado para la Asistencia',
    ]) !!}
</div>

<div class="row">

    <div class="col">
        <div class="form-label-group pb-1">
            {!! Form::text('finicial', old('finicial'), [
                'class' => 'form-control datepicker',
                'placeholder' => 'Fecha Inicial',
                'id' => 'finicial',
                'required',
                'readonly',
                'maxlength' => '10',
            ]) !!}
            <label for="finicial">Fecha Inicial</label>
        </div>
    </div>

    <div class="col">
        <div class="form-label-group pb-1">
            {!! Form::text('ffinal', old('ffinal'), [
                'class' => 'form-control datepicker',
                'placeholder' => 'Fecha Final',
                'id' => 'ffinal',
                'required',
                'readonly',
                'maxlength' => '10',
            ]) !!}
            <label for="ffinal">Fecha Final</label>
        </div>
    </div>

</div>

@section('stylesheet')
    @parent

    <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet">

    <link href="{{ asset('vendor/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    @parent

    <script src="{{ asset('vendor/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.es.min.js') }}"></script>

    <script type="text/javascript">
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            language: "es",
            autoclose: true,
            startView: 2
        });
    </script>
@endsection
