@include('admin.elements.forms.errors')

@include('admin.elements.messeges.oper_ok')

{{-- {{ Form::hidden('user_id', Auth::user()->id) }} --}}

<div class="form-label-group pb-1">

    {!! Form::text('view', old('view'), ['class' => 'form-control', 'placeholder' => 'View', 'id' => 'view']) !!}
    <label for="view">View</label>

</div>

<div class="form-label-group pb-1">

    {!! Form::text('table', old('table'), ['class' => 'form-control', 'placeholder' => 'Table', 'id' => 'table']) !!}
    <label for="table">Table</label>

</div>

<div class="form-label-group pb-1">

    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => 'Name', 'id' => 'name']) !!}
    <label for="name">Name</label>

</div>

<div class="form-label-group pb-1">

    {!! Form::text('value', old('value'), ['class' => 'form-control', 'placeholder' => 'Value', 'id' => 'value']) !!}
    <label for="value">Value</label>

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
