    <div class="form-group pt-2">
        <label for="planpago_id" class="m-0">Plan de Pago</label>
        {!! Form::select('planpago_id', $list_planpago, old('planpago_id'), [
            'class' => 'form-control',
            'id' => 'planpago_id',
            'placeholder' => 'Seleccione',
            'required' => 'required',
        ]) !!}
    </div>


    {{-- <div class="form-group pb-1">
        <label for="date_transaction" class="m-0">Fecha de la transacción</label>
        {!! Form::text('date_transaction', old('date_transaction'), ['class'=>'form-control
        datepicker','placeholder'=>'Fecha de la transacción','id'=>'date_transaction','required'=>'required','readonly','maxlength'=>"10"]); !!}
    </div> --}}


    @section('scripts')
        @parent

        {{-- <script src="{{ asset("js/accounting.min.js") }}"></script> --}}

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

    @section('stylesheet')
        @parent

        <link href="{{ asset('css/floating-labels.css') }}" rel="stylesheet">

        <link href="{{ asset('vendor/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker3.css') }}" rel="stylesheet">
    @endsection
