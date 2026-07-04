<div class="form-group mb-1">
    <label for="nom_concepto_pago_id"
        class="m-0 font-weight-bold text-secondary">{{ $list_comment['nom_concepto_pago_id'] ?? '' }}</label>
    {!! Form::select('nom_concepto_pago_id', $list_nom_concepto_pago, $concepto_pago->nom_concepto_pago_id, [
        'class' => 'form-control',
        'id' => 'nom_concepto_pago_id',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>

<div class="form-group">
    <label for="concepto_ammount"
        class="m-0 font-weight-bold text-secondary">{{ $list_comment['concepto_ammount'] ?? '' }}</label>
    {!! Form::text('concepto_ammount', $concepto_pago->concepto_ammount, [
        'maxlength' => '4',
        'class' => 'form-control',
        'placeholder' => 'Abreviación',
        'id' => 'concepto_ammount',
        'required',
    ]) !!}
</div>
<div class="form-group">
    <label for="concepto_description"
        class="m-0 font-weight-bold text-secondary">{{ $list_comment['concepto_description'] ?? '' }}</label>
    {!! Form::text('concepto_description', $concepto_pago->concepto_description, [
        'maxlength' => '4',
        'class' => 'form-control',
        'placeholder' => 'Abreviación',
        'id' => 'concepto_description',
        'required',
    ]) !!}
</div>


<div class="form-group pb-1">
    <label for="status_discount"
        class="m-0 font-weight-bold text-secondary">{{ $list_comment['status_discount'] ?? '' }}</label>
    {!! Form::select('status_discount', ['true' => 'SI', 'false' => 'NO'], $concepto_pago->status_discount, [
        'class' => 'form-control',
        'id' => 'status_discount',
        'placeholder' => 'Seleccione',
    ]) !!}
</div>
