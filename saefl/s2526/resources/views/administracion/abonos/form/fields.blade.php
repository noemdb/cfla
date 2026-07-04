<div class="form-group pt-2">
    <label for="method_pay_id" class="m-0">Método de Pago.</label>
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
    <label for="number_i_pay" class="m-0">Referencia Bancaria / Número de la transacción</label>
    {!! Form::text('number_i_pay', old('number_i_pay'), [
        'class' => 'form-control',
        'placeholder' => 'Número de la transacción',
        'id' => 'number_i_pay',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group">
    <label for="ingreso_ammount" class="m-0">Monto del Pago <span class="text-muted small">[Decimales separados por
            punto]</span></label>
    {!! Form::text('ingreso_ammount', old('ingreso_ammount'), [
        'class' => 'form-control ',
        'placeholder' => 'Monto del Pago',
        'id' => 'ingreso_ammount',
        'required' => 'required',
    ]) !!}
</div>

<div class="form-group">
    {!! Form::label('status_late_payment', 'Pago Extemporaneo', ['class' => 'm-0 font-weight-bold text-muted']) !!}
    {!! Form::select('status_late_payment', ['false' => 'NO', 'true' => 'SI'], old('status_late_payment'), [
        'class' => 'form-control',
        'id' => 'status_late_payment',
    ]) !!}
</div>

<div class="form-group">
    {!! Form::label('status_late_payment', 'Pago Extemporaneo', ['class' => 'm-0 font-weight-bold text-muted']) !!}
    {!! Form::text('exchange_ammount_late_payment', old('exchange_ammount_late_payment'), [
        'class' => 'form-control ',
        'placeholder' => 'Monto del Pago',
        'id' => 'exchange_ammount_late_payment',
    ]) !!}
</div>

<div class="form-row">
    <div class="col-sm-6">
        <div class="form-group pb-1">
            <label for="date_payment" class="m-0">Fecha de Pago </label>
            @php $date_payment = (!empty($ingreso->date_payment)) ? $ingreso->date_payment->format('Y-m-d'):null  @endphp
            {!! Form::date('date_payment', $date_payment, ['class' => 'form-control', 'required']) !!}
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group pb-1">
            <label for="date_transaction" class="m-0">Fecha en Banco</label>
            @php $date_transaction = (!empty($ingreso->date_transaction)) ? $ingreso->date_transaction->format('Y-m-d'):null  @endphp
            {!! Form::date('date_transaction', $date_transaction, ['class' => 'form-control', 'required']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    <label for="ingreso_observations" class="m-0">Observaciones para la trasacción a registrar</label>
    {!! Form::text('ingreso_observations', old('ingreso_observations'), [
        'class' => 'form-control',
        'placeholder' => 'Observaciones para la trasacción a registrar',
        'id' => 'ingreso_observations',
    ]) !!}
</div>

<div class="form-group">
    <label for="status_matriculations" class="m-0">Estado de la Matrícula</label>
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
            'placeholder' => 'Seleccione el estado de la matrícula',
        ],
    ) !!}
</div>
