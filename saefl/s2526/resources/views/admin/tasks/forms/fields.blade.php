@include('admin.elements.forms.errors')

@include('admin.elements.messeges.oper_ok')

<div class="form-label-group pb-1">

    {!! Form::select('user_id', $user_list, old('user_id'), ['class' => 'form-control', 'placeholder' => 'Usuario']) !!}

</div>

<div class="form-label-group pb-1">

    {!! Form::select('estado', $estado_list, old('estado'), ['class' => 'form-control', 'placeholder' => 'Estado']) !!}

</div>

<div class="form-label-group pb-1">

    {!! Form::select('tipo', $tipo_list, old('tipo'), ['class' => 'form-control', 'placeholder' => 'Tipo']) !!}

</div>

<div class="form-label-group pb-1">
    {!! Form::text('descripcion', old('descripcion'), [
        'class' => 'form-control',
        'placeholder' => 'descripcion',
        'id' => 'descripcion',
        'required',
    ]) !!}
    <label for="descripcion">Descripción</label>
</div>

<div class="form-label-group pb-1">
    {!! Form::text('evento', old('evento'), [
        'class' => 'form-control',
        'placeholder' => 'evento',
        'id' => 'evento',
    ]) !!}
    <label for="evento">Evento</label>
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
