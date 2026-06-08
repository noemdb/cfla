<div class="form-group pt-2">
    <label for="method_pay_id" class="m-0">Método de Pago</label>
    {!! Form::select('method_pay_id', $method_pay_list, old('method_pay_id'), [
        'class' => 'form-control',
        'id' => 'method_pay_id',
        'placeholder' => 'Seleccione',
        'required' => 'required',
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
            'required' => 'required',
        ]) !!}
    </div>

    <div class="form-row">
        <div class="col-sm-6">
            <div class="form-group pb-1">
                <label for="date_payment" class="m-0">Fecha de Pago </label>
                @php $date_payment = (Request::is('*edit*')) ? $ingreso->date_payment->format('Y-m-d'):null  @endphp
                {!! Form::date('date_payment', $date_payment, [
                    'id' => 'date_payment',
                    'class' => 'form-control crt_display',
                    'required' => 'required',
                ]) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group pb-1">
                <label for="date_transaction" class="m-0">Fecha en Banco</label>
                @php $date_transaction = (Request::is('*edit*')) ? $ingreso->date_transaction->format('Y-m-d'):null  @endphp
                {!! Form::date('date_transaction', $date_transaction, [
                    'id' => 'date_transaction',
                    'class' => 'form-control crt_display',
                    'required' => 'required',
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
                    'required' => 'required',
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
                        'required' => 'required',
                    ]) !!}
                    {!! Form::select('exchange_ammount', $list_divisas, old('exchange_ammount'), [
                        'class' => 'form-control',
                        'placeholder' => 'Monto Cambiario',
                        'id' => 'exchange_ammount',
                        'readonly',
                    ]) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="ingreso_observations" class="m-0">Observaciones para la trasacción a registrar</label>
        {!! Form::text('ingreso_observations', old('ingreso_observations'), [
            'class' => 'form-control crt_display',
            'placeholder' => 'Observaciones para la trasacción a registrar',
            'id' => 'ingreso_observations',
        ]) !!}
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
            // console.log($(this).val());
            // if ($(this).val().length == 0 ) {
            //     $("#ingreso_ammount").attr("readonly", false); console.log('ingreso_ammount: readonly');
            // }
        });

        $('#date_payment').change(function() {
            var date_payment = $(this).val();
            $('#date_transaction').val(date_payment);
        });

        $(document).ready(function() {
            $('#method_pay_id').change(function(e) {
                var d = new Date();
                if ($(this).val() == 1) {
                    $("#crt_display").hide();
                    $(".crt_display").hide();
                    $("#nav-tab05-tab").tab('show');
                    $("#banco_id option[value='1']").attr("selected", true);
                    $("#number_i_pay").val(d.getTime());
                    $("#ingreso_ammount").val('0');
                    $("#date_transaction").val('{{ $fecha ?? '2000-01-01' }}');
                    $("#date_payment").val('{{ $fecha ?? '2000-01-01' }}');
                    // $("#person_bill_ci").val('0');
                    // $("#person_bill_name").val('0');
                    //console.log(d.getTime());
                } else {
                    $("#crt_display").show();
                    $(".crt_display").show();
                    $("#banco_id option[value='0']").attr("selected", true);
                    $("#number_i_pay").val('');
                    $("#ingreso_ammount").val('');
                    $("#date_transaction").val('');
                    $("#date_payment").val('');
                    // $("#person_bill_ci").val('');
                    // $("#person_bill_name").val('');
                }
            });
        });
    </script>
@endsection
