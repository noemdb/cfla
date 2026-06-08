<div class="form-group pt-2">
    <label for="method_pay_id" class="m-0">Método de Pago</label>
    {!! Form::select('method_pay_id', $method_pay_list, old('method_pay_id'), [
        'class' => 'form-control',
        'id' => 'method_pay_id',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

{{-- <div id="crt_display" class="crt_display" style="{{(old('banco_id')==1) ? 'display:none;':'display:block;'}}"> --}}
<div id="crt_display" class="crt_display">

    <div class="form-group">
        <label for="banco_id" class="m-0">Banco receptor </label>
        {!! Form::select('banco_id', $banco_list, old('banco_id'), [
            'class' => 'form-control',
            'id' => 'banco_id',
            'placeholder' => 'Seleccione',
        ]) !!}
    </div>

    <div class="form-row">
        <div class="col-sm-6">
            <div class="form-group pb-1">
                <label for="date_payment" class="m-0">Fecha de Pago </label>
                @php $date_payment = (Request::is('*edit*')) ? $ingreso->date_payment->format('Y-m-d'):null  @endphp
                {!! Form::date('date_payment', $date_payment, ['id' => 'date_payment', 'class' => 'form-control crt_display']) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group pb-1">
                <label for="date_transaction" class="m-0">Fecha en Banco</label>
                @php $date_transaction = (Request::is('*edit*')) ? $ingreso->date_transaction->format('Y-m-d'):null  @endphp
                {!! Form::date('date_transaction', $date_transaction, [
                    'id' => 'date_transaction',
                    'class' => 'form-control crt_display',
                ]) !!}
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="number_i_pay" class="m-0">Referencia/Número </label>
                {!! Form::text('number_i_pay', old('number_i_pay'), [
                    'class' => 'form-control crt_display',
                    'placeholder' => 'Número ',
                    'id' => 'number_i_pay',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="ingreso_ammount" class="m-0">Monto <span class="small text-muted">[Decimales separados
                        por punto]</span></label>
                <div class="input-group">
                    {!! Form::text('ingreso_ammount', old('ingreso_ammount'), [
                        'class' => 'form-control crt_display',
                        'placeholder' => 'Monto ',
                        'id' => 'ingreso_ammount',
                    ]) !!}
                    {{-- {!! Form::select('exchange_ammount',$list_divisas,old('exchange_ammount'),['class'=>'form-control','placeholder' => 'Monto Cambiario','id'=>'exchange_ammount','readonly']) !!} --}}

                    {!! Form::text('exchange_ammount', old('exchange_ammount'), [
                        'class' => 'form-control ',
                        'placeholder' => 'Monto Cambiario',
                        'id' => 'exchange_ammount',
                        'readonly',
                    ]) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('status_late_payment', 'Estado pago extemporaneo', [
            'class' => 'm-0 font-weight-bold text-muted',
        ]) !!}
        {!! Form::label('status_late_payment', 'Pago Extemporaneo', ['class' => 'm-0 font-weight-bold text-muted']) !!}
        {!! Form::select('status_late_payment', ['false' => 'NO', 'true' => 'SI'], old('status_late_payment'), [
            'class' => 'form-control',
            'id' => 'status_late_payment',
        ]) !!}
    </div>

    <div class="form-group">
        {!! Form::label('exchange_ammount_late_payment', 'Monto pago extemporaneo, [Decimales separados por punto]', [
            'class' => 'm-0 font-weight-bold text-muted',
        ]) !!}
        {!! Form::label('status_late_payment', 'Pago Extemporaneo, [Decimales separados por punto]', [
            'class' => 'm-0 font-weight-bold text-muted',
        ]) !!}
        {!! Form::text('exchange_ammount_late_payment', old('exchange_ammount_late_payment'), [
            'class' => 'form-control ',
            'placeholder' => 'Monto del Pago',
            'id' => 'exchange_ammount_late_payment',
        ]) !!}
    </div>

    <div class="form-group">
        <label for="ingreso_observations" class="m-0">Observaciones para la trasacción a registrar</label>
        {!! Form::text('ingreso_observations', old('ingreso_observations'), [
            'class' => 'form-control crt_display',
            'placeholder' => 'Observaciones para la trasacción a registrar',
            'id' => 'ingreso_observations',
        ]) !!}
    </div>

    <div class="form-group">
        <label for="status_matriculations" class="m-0">Destinado a Asegureamiento de la Matrícula</label>
        {!! Form::select(
            'status_matriculations',
            [
                '0' => 'NO',
                '1' => 'SI',
            ],
            old('status_matriculations'),
            [
                'class' => 'form-control',
                'id' => 'status_matriculations',
                // 'placeholder' => 'Seleccione'
            ],
        ) !!}
    </div>

</div>

@section('scripts')
    @parent
    <script>
        $('#date_payment').change(function() {
            var date_payment = $('#date_payment').val();
            if (typeof date_payment !== 'undefined' && date_payment) {
                var exchange_ammount = $('#exchange_ammount').val();
                var url =
                    '{{ route('administracion.ajax.fill.ExchangeRateAmmount', ['date_payment' => '_date_payment_']) }}';
                url = url.replace('_date_payment_', date_payment);
                $.ajax({
                        type: "GET",
                        url: url
                    }).done(function(data) {
                        var rate_ammount = data;
                        console.log(data);
                        var ammount = rate_ammount * exchange_ammount;
                        $("#ingreso_ammount").val(ammount);
                        $("#exchange_ammount").attr("readonly",
                        false); //console.log('exchange_ammount:readonly=false');
                    })
                    .fail(function() {
                        console.log("error occured");
                    });
            }
        });

        // $('#exchange_ammount').keyup(function(){
        $('#exchange_ammount').change(function() {
            var date_payment = $('#date_payment').val(); //console.log(date_payment);
            if (typeof date_payment !== 'undefined' && date_payment) {
                var exchange_ammount = $('#exchange_ammount').val();
                var url =
                    '{{ route('administracion.ajax.fill.ExchangeRateAmmount', ['date_payment' => '_date_payment_']) }}';
                url = url.replace('_date_payment_', date_payment);
                $.ajax({
                        type: "GET",
                        url: url
                    }).done(function(data) {
                        var rate_ammount = data; //console.log(data);
                        var ammount = rate_ammount * exchange_ammount;
                        $("#ingreso_ammount").val(ammount).prop('readonly', true);
                        //$("#exchange_ammount").attr("readonly", false);
                    })
                    .fail(function() {
                        console.log("error occured");
                    });
            }
        });

        $('#date_payment').change(function() {
            var date_payment = $(this).val();
            $('#date_transaction').val(date_payment);
        });
    </script>
@endsection
