@include('admin.elements.forms.errors')

@include('admin.elements.messeges.oper_ok')

{{ Form::hidden('user_id', Auth::user()->id) }}
{{-- {{ Form::hidden('invisible', 'secret') }} --}}

<div class="form-label-group pb-1">

    {!! Form::select('destino_user_id', $user_list, old('destino_user_id'), [
        'class' => 'form-control',
        'placeholder' => 'Destino',
    ]) !!}

</div>

<div class="form-label-group pb-1">

    {!! Form::select('estado', $estado_list, old('estado'), ['class' => 'form-control', 'placeholder' => 'Estado']) !!}

</div>

<div class="form-label-group pb-1">

    {!! Form::select('tipo', $tipo_list, old('tipo'), ['class' => 'form-control', 'placeholder' => 'Tipo']) !!}

</div>

<div class="form-label-group pb-1">
    {!! Form::text('mensaje', old('mensaje'), [
        'class' => 'form-control',
        'placeholder' => 'Mensaje',
        'id' => 'mensaje',
        'required',
    ]) !!}
    <label for="mensaje">Mensaje</label>
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
