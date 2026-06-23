<div class="alert-error alert-danger alert-dismissible" role="alert"></div>
@csrf

<div class="form-row">
    <div class="col-sm-6">
        <div class="form-group pt-2">
            <label for="method_pay_id" class="m-0">Método de Pago {{ $request->method_pay_id ?? '' }}</label>
            @php $class_error = ($errors->has('method_pay_id')) ? ' border border-danger rounded ':null ; @endphp
            @php $method_pay_id = (empty($request->method_pay_id)) ? old('method_pay_id') : $request->method_pay_id ; @endphp
            {!! Form::select('method_pay_id', $method_pay_list, $method_pay_id, [
                'class' => 'form-control' . $class_error,
                'id' => 'method_pay_id',
                'placeholder' => 'Seleccione',
            ]) !!}
            @error('method_pay_id')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-6">
        <div class="crt_display">
            <div class="form-group pt-2">
                <label for="banco_id" class="m-0">Banco receptor </label>
                @php $class_error = ($errors->has('banco_id')) ? ' border border-danger rounded ':null ; @endphp
                @php $banco_id = (empty($request->banco_id)) ? old('banco_id') : $request->banco_id ; @endphp
                {!! Form::select('banco_id', $banco_list, $banco_id, [
                    'class' => 'form-control' . $class_error,
                    'id' => 'banco_id',
                    'placeholder' => 'Seleccione',
                ]) !!}
                @error('banco_id')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
</div>


<div id="crt_display" class="crt_display">

    <div class="form-row">
        <div class="col-sm-6">
            <div class="form-group pb-1">
                <label for="date_payment" class="m-0">Fecha de Pago </label>
                {{-- @php $date_payment = (Request::is('*edit*')) ? $ingreso->date_payment->format('Y-m-d'):null  @endphp --}}
                @php $class_error = ($errors->has('date_payment')) ? ' border border-danger rounded ':null ; @endphp
                @php $date_payment = (empty($request->date_payment)) ? old('date_payment') : $request->date_payment ; @endphp
                {!! Form::date('date_payment', $date_payment, [
                    'id' => 'date_payment',
                    'class' => 'form-control crt_display' . $class_error,
                ]) !!}
                @error('date_payment')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group pb-1">
                <label for="date_transaction" class="m-0">Fecha en Banco</label>
                {{-- @php $date_transaction = (Request::is('*edit*')) ? $ingreso->date_transaction->format('Y-m-d'):null  @endphp --}}
                @php $class_error = ($errors->has('date_transaction')) ? ' border border-danger rounded ':null ; @endphp
                @php $date_transaction = (empty($request->date_transaction)) ? old('date_transaction') : $request->date_transaction ; @endphp
                {!! Form::date('date_transaction', $date_transaction, [
                    'id' => 'date_transaction',
                    'class' => 'form-control crt_display' . $class_error,
                ]) !!}
                @error('date_transaction')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="number_i_pay" class="m-0">Referencia/Número </label>
                @php $class_error = ($errors->has('number_i_pay')) ? ' border border-danger rounded ':null ; @endphp
                @php $number_i_pay = (empty($request->number_i_pay)) ? old('number_i_pay') : $request->number_i_pay ; @endphp
                {!! Form::text('number_i_pay', $number_i_pay, [
                    'class' => 'form-control crt_display' . $class_error,
                    'placeholder' => 'Número ',
                    'id' => 'number_i_pay',
                ]) !!}
                @error('number_i_pay')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="ingreso_ammount" class="m-0">Monto <span class="small text-muted">[Decimales separados
                        por punto]</span></label>
                <div class="input-group">
                    @php $class_error = ($errors->has('ingreso_ammount')) ? ' border border-danger rounded ':null ; @endphp
                    @php $ingreso_ammount = (empty($request->ingreso_ammount)) ? old('ingreso_ammount') : $request->ingreso_ammount ; @endphp
                    @php $exchange_ammount = (empty($request->exchange_ammount)) ? old('exchange_ammount') : $request->exchange_ammount ; @endphp
                    {!! Form::text('ingreso_ammount', $ingreso_ammount, [
                        'class' => 'form-control crt_display ' . $class_error,
                        'placeholder' => 'Monto ',
                        'id' => 'ingreso_ammount',
                        'onkeypress' => 'return isNumberKey(event)',
                    ]) !!}
                    {!! Form::text('exchange_ammount', $exchange_ammount, [
                        'class' => 'form-control crt_display ' . $class_error,
                        'placeholder' => 'Monto ',
                        'id' => 'exchange_ammount',
                        'onkeypress' => 'return isNumberKey(event)',
                    ]) !!}
                </div>
                @error('ingreso_ammount')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>


    {{-- --------------------------------------------------- --}}

    <div class="form-row">
        <div class="col-sm-6">
            <div class="form-group pt-2">
                <label for="status_late_payment" class="m-0">Pago Extemporaneo
                    {{ $request->status_late_payment ?? '' }}</label>
                @php $class_error = ($errors->has('status_late_payment')) ? ' border border-danger rounded ':null ; @endphp
                @php $status_late_payment = (empty($request->status_late_payment)) ? old('status_late_payment') : $request->status_late_payment ; @endphp
                {!! Form::select('status_late_payment', ['false' => 'NO', 'true' => 'SI'], $status_late_payment, [
                    'class' => 'form-control' . $class_error,
                    'id' => 'status_late_payment',
                ]) !!}
                @error('status_late_payment')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-sm-6">
            <div class="crt_display">
                <div class="form-group pt-2">
                    <label for="exchange_ammount_late_payment" class="m-0">Monto Pago extemporaneo</label>
                    @php $class_error = ($errors->has('exchange_ammount_late_payment')) ? ' border border-danger rounded ':null ; @endphp
                    @php $exchange_ammount_late_payment = (empty($request->exchange_ammount_late_payment)) ? old('exchange_ammount_late_payment') : $request->exchange_ammount_late_payment ; @endphp
                    {!! Form::text('exchange_ammount_late_payment', $exchange_ammount_late_payment, [
                        'class' => 'form-control ' . $class_error,
                        'placeholder' => 'Monto ',
                        'id' => 'exchange_ammount_late_payment',
                        'onkeypress' => 'return isNumberKey(event)',
                    ]) !!}
                    @error('exchange_ammount_late_payment')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- --------------------------------------------------- --}}


    <div class="form-group">
        <label for="ingreso_observations" class="m-0">Observaciones para la trasacción a registrar</label>
        @php $ingreso_observations = (empty($request->ingreso_observations)) ? old('ingreso_observations') : $request->ingreso_observations ; @endphp
        {!! Form::text('ingreso_observations', $ingreso_observations, [
            'class' => 'form-control crt_display',
            'placeholder' => 'Observaciones para la trasacción a registrar',
            'id' => 'ingreso_observations',
        ]) !!}
    </div>
    <div class="form-group text-right">
        <button type="button" class="btn-incluir btn btn-success btn-sm px-2 mt-2" value="Abonar" data-id="create"
            id="btn-create-registropago">
            <i class="far fa-save"></i>
            Registrar un abono
        </button>
    </div>

</div>

@section('scripts')
    @parent
    <script>
        function isNumberKey(evt) {
            var charCode = evt.which ? evt.which : evt.keyCode;
            return (
                (charCode >= 48 && charCode <= 57) || // 0-9
                charCode === 46 || // punto
                charCode === 8 || // backspace
                charCode === 9 || // tab
                charCode === 37 || // flecha izq
                charCode === 39 // flecha der
            );
        }

        function obtenerTasaCambio(date_payment, callback) {
            if (!date_payment) return;

            var url = '{{ route('administracion.ajax.fill.ExchangeRateAmmount', ['date_payment' => '_date_payment_']) }}';
            url = url.replace('_date_payment_', date_payment);

            $.ajax({
                type: "GET",
                url: url
            }).done(function(data) {
                const tasa = parseFloat(data);
                if (!isNaN(tasa) && tasa > 0) {
                    callback(tasa);
                } else {
                    console.warn("Tasa inválida:", data);
                }
            }).fail(function() {
                console.error("Error al obtener la tasa de cambio");
            });
        }

        function actualizarCampo(campoOrigen, campoDestino, operacion) {
            const valor = parseFloat($(campoOrigen).val());
            const date = $('#date_payment').val();
            if (!isNaN(valor) && date) {
                obtenerTasaCambio(date, function(rate) {
                    let resultado = operacion === 'multiplicar' ? (valor * rate) : (valor / rate);
                    $(campoDestino).val(resultado.toFixed(2));
                });
            }
        }

        $(function() {
            console.log('ready');

            // Sincronización de fechas
            $('#date_payment').on('change', function() {
                const date = $(this).val();
                $('#date_transaction').val(date);

                if ($('#exchange_ammount').val()) {
                    actualizarCampo('#exchange_ammount', '#ingreso_ammount', 'multiplicar');
                } else if ($('#ingreso_ammount').val()) {
                    actualizarCampo('#ingreso_ammount', '#exchange_ammount', 'dividir');
                }
            });

            // Cambio de valores entre campos
            $('#ingreso_ammount').on('input', function() {
                actualizarCampo('#ingreso_ammount', '#exchange_ammount', 'dividir');
            });

            $('#exchange_ammount').on('input', function() {
                actualizarCampo('#exchange_ammount', '#ingreso_ammount', 'multiplicar');
            });

            // Mostrar/ocultar secciones según método de pago
            function toggleCrtDisplay() {
                const selectedValue = $('#method_pay_id').val();
                if (selectedValue == '1') {
                    $('.crt_display').hide();

                    // Establecer valores por defecto
                    $('#banco_id').val('7');

                    const now = new Date();
                    const fechaActual = now.toISOString().split('T')[0]; // YYYY-MM-DD
                    const timestamp = now.getTime(); // Marca de tiempo

                    $('#date_payment').val(fechaActual);
                    $('#date_transaction').val(fechaActual);
                    $('#number_i_pay').val(timestamp);
                    $('#ingreso_ammount').val('0.0001');
                    $('#exchange_ammount').val('0.0001');
                    $('#status_late_payment').val('false');
                    $('#exchange_ammount_late_payment').val('false');
                    $('#ingreso_observations').val('AJT AUTOMATIC SYSTEM');

                } else {
                    $('.crt_display').show();
                }
            }


            $('#method_pay_id').on('change', toggleCrtDisplay);

            // Ejecutar al cargar la página
            toggleCrtDisplay();
        });

        function actualizarDesdeIngreso() {
            const ingreso = parseFloat($('#ingreso_ammount').val());
            const date = $('#date_payment').val();

            if (!isNaN(ingreso) && ingreso > 0 && date) {
                obtenerTasaCambio(date, function(rate) {
                    const exchange = (ingreso / rate).toFixed(2);
                    $('#exchange_ammount').val(exchange);
                });
            }
        }
    </script>
@endsection
