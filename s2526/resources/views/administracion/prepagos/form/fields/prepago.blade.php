<div class="form-group pt-1">
    <label for="representant_id" class="m-0 text-muted font-weight-bold">Representante</label>
    <div class="alert alert-secondary">
        {!! Form::select('representant_id', $list_representant, old('representant_id'), [
            'class' => 'form-control',
            'id' => 'representant_id',
            'placeholder' => 'Seleccione',
            'required' => 'required',
        ]) !!}
    </div>
</div>

<div class="form-group pt-1">
    <label for="ingreso_observations" class="m-0 text-muted font-weight-bold">Observaciones</label>
    <div class="alert alert-secondary">
        {!! Form::textarea('ingreso_observations', old('ingreso_observations'), [
            'class' => 'form-control',
            'placeholder' => 'Observaciones',
            'id' => 'ingreso_observations',
            'rows' => '2',
        ]) !!}
    </div>
</div>

<div class="form-group pt-1">
    <label for="comment" class="m-0 text-muted font-weight-bold">Comentarios</label>
    <div class="alert alert-secondary">
        {!! Form::textarea('comment', old('comment'), [
            'class' => 'form-control',
            'placeholder' => 'Comentarios',
            'id' => 'comment',
            'rows' => '2',
        ]) !!}
    </div>
</div>

<div class="form-group pt-1">
    <label for="method_pay_id" class="m-0 text-muted font-weight-bold">Método de Pago</label>
    <div class="alert alert-secondary">
        {{ Form::hidden('method_pay_id', $metodo_pago->id) }}
        {{ $metodo_pago->name ?? '' }}
    </div>
</div>

<div class="form-group pt-1">
    <label for="method_pay_id" class="m-0 text-muted font-weight-bold">Banco receptor del pago</label>
    <div class="alert alert-secondary">
        {{ Form::hidden('banco_id', $mbancario->banco_id) }}
        {{ $mbancario->banco->name ?? '' }}
    </div>
</div>
<div class="form-group pt-1">
    <label for="method_pay_id" class="m-0 text-muted font-weight-bold">Número de la transacción</label>
    <div class="alert alert-secondary">
        {{ Form::hidden('number_i_pay', $mbancario->number_i_pay) }}
        {{ $mbancario->number_i_pay ?? '' }}
    </div>
</div>

<div class="form-group pt-1">
    <label for="date_transaction" class="m-0 text-muted font-weight-bold">Número de la transacción</label>
    <div class="alert alert-secondary">
        {{ Form::hidden('date_transaction', $mbancario->date_transaction) }}
        {{ $mbancario->date_transaction->format('d-m-Y') ?? '' }}
    </div>
</div>

<div class="form-group pt-1">
    <label for="ingreso_ammount" class="m-0 text-muted font-weight-bold">Monto de la operación</label>
    <div class="alert alert-secondary">
        {{ Form::hidden('ingreso_ammount', $mbancario->ingreso_ammount) }}
        {{ f_float($mbancario->ingreso_ammount) }}
    </div>
</div>

<div class="form-group pt-1">
    <label for="ingreso_ammount" class="m-0 text-muted font-weight-bold">Aprobación</label>
    <div class="alert alert-secondary">
        {{ Form::hidden('status_approved', true) }}
        <span class=" text-success font-weight-bold">
            <i class="fa fa-check" aria-hidden="true"></i>
            APROBADA
        </span>
    </div>
</div>
