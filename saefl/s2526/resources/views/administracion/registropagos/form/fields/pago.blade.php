<div class="form-group pt-2">
    <label for="method_pay_id" class="m-0">Método de Pago</label>
    {!! Form::select('method_pay_id', $method_pay_list, old('method_pay_id'), [
        'class' => 'form-control',
        'id' => 'method_pay_id',
        'placeholder' => 'Seleccione',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="banco_id" class="m-0">Banco receptor del pago</label>
    {!! Form::select('banco_id', $banco_list, old('banco_id'), [
        'class' => 'form-control',
        'id' => 'banco_id',
        'placeholder' => 'Seleccione',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="seccion_id" class="m-0">Número de la transacción</label>
    {!! Form::text('number_i_pay', old('number_i_pay'), [
        'class' => 'form-control',
        'placeholder' => 'Número de la transacción',
        'id' => 'observations',
        'required' => 'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="pagos_ammount" class="m-0">Monto del Pago [Decimales separados por punto]</label>
    {!! Form::text('pagos_ammount', old('pagos_ammount'), [
        'class' => 'form-control',
        'placeholder' => 'Monto del Pago',
        'id' => 'pagos_ammount',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group pb-1">
    <label for="date_transaction" class="m-0">Fecha de la transacción</label>
    {!! Form::text('date_transaction', old('date_transaction'), [
        'class' => 'form-control datepicker',
        'placeholder' => 'Fecha de la transacción',
        'id' => 'date_birth',
        'required' => 'required',
        'readonly',
        'maxlength' => '10',
    ]) !!}
</div>

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('#pagos_ammount').keyup(function(e) {
                // e.preventDefault();
                var number = accounting.formatMoney($(this).val(), "", 2, ".", ",");
                $("#span_pagos_ammount").text(number); //console.log($(this).val());console.log();
            });
            //fin del evento
        });

        $(document).ready(function() {
            $('#pagos_ammount').focusout(function(e) {
                // e.preventDefault();
                // var valor = $(this).val().replace(".","");//console.log('valor: '+valor);
                // var decimal = valor.substr(-2);//console.log('Decimal: '+decimal);
                // var len = valor.length - 2;
                // var entero = valor.substr(0,len);//console.log('Entero: '+entero);

                // var number = entero+'.'+decimal;//console.log('number: '+number);
                // var number = valor.substr(0,(valor.length - 2))+'.'+valor.substr(-2);//console.log('number: '+number);

                // var number = accounting.formatMoney($(this).val(), "", 2, ".", ",");
                // $(this).val(number);           
            });
            //fin del evento
        });
    </script>
@endsection

@section('scripts')
    @parent

    <script src="{{ asset('js/accounting.min.js') }}"></script>

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
