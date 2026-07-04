@include('admin.elements.forms.errors')

@include('admin.elements.messeges.oper_ok')

{{-- {{ Form::hidden('user_id', Auth::user()->id) }} --}}

<div class="form-label-group pb-1">

    @if (empty($setting->id))
        {!! Form::select('user_id', $user_list, old('user_id'), ['class' => 'form-control', 'placeholder' => 'Usuario']) !!}
    @else
        {!! Form::text('username', $user_list[$setting->user_id], [
            'class' => 'form-control',
            'placeholder' => 'Nombre',
            'id' => 'user_id',
            'readonly' => 'readonly',
        ]) !!}
        {!! Form::hidden('user_id', old('user_id')) !!}
        <label for="user_id">Usuario</label>
    @endif
    {{-- $user_list[$setting->user_id] --}}

    {{-- {!! Form::select('user_id',$user_list,old('user_id'),['class' => 'form-control','placeholder' => 'Usuario']) !!} --}}

</div>

<div class="form-label-group pb-1">

    @if (empty($setting->id))
        {!! Form::select('name', $name_list, old('name'), ['class' => 'form-control', 'placeholder' => 'Nombre']) !!}
    @else
        {!! Form::text('name', old('name'), [
            'class' => 'form-control',
            'placeholder' => 'Nombre',
            'id' => 'name',
            'readonly' => 'readonly',
        ]) !!}
        <label for="name">Nombre</label>
    @endif

    {{-- {!! Form::select('name',$name_list,old('name'),['class' => 'form-control','placeholder' => 'Nombre']); !!} --}}

</div>

<div class="form-label-group pb-1">

    {!! Form::select('value', $value_list, old('value'), ['class' => 'form-control', 'placeholder' => 'Valor']) !!}

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
