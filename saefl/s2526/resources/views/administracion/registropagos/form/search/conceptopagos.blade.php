<div class="form-row">

    <div class="col-2 pr-0">
        <label for="planpago_id" class="m-0 font-weight-bold">Plan de Pago</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="{{ $icon_menus['planpagos'] ?? '' }} fa-1x"></i>
                </span>
            </div>
            {!! Form::select('planpago_id', $list_planpago, $planpago_id, [
                'class' => 'form-control',
                'id' => 'planpago_id',
                'placeholder' => 'Seleccione',
                'required',
            ]) !!}
        </div>
    </div>

    <div class="col-2 pr-0">
        <label for="cuentaxpagar_id" class="m-0 font-weight-bold">Concepto</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="{{ $icon_menus['cuentaxpagars'] ?? '' }} fa-1x"></i>
                </span>
            </div>
            {!! Form::select('cuentaxpagar_id', $list_cuentaxpagar, $cuentaxpagar_id, [
                'class' => 'form-control',
                'id' => 'cuentaxpagar_id',
                'placeholder' => 'Seleccione',
                'required',
            ]) !!}
        </div>
    </div>

    <div class="col-2 pr-0">
        <label for="concepto_pago_id" class="m-0 font-weight-bold">Cuenta</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="{{ $icon_menus['concepto_pagos'] ?? '' }} fa-1x"></i>
                </span>
            </div>
            {!! Form::select('concepto_pago_id', $list_conceptopago, $concepto_pago_id, [
                'class' => 'form-control',
                'id' => 'concepto_pago_id',
                'placeholder' => 'Seleccione',
                'required',
            ]) !!}
        </div>
    </div>

    <div class="col-2 pr-0">
        <label for="date_payment" class="m-0 font-weight-bold">Fecha de Pago</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    {{-- <i class="{{ $icon_menus['concepto_pagos'] ?? ''}} fa-1x"></i> --}}
                    <i class="fa fa-calendar" aria-hidden="true"></i>
                </span>
            </div>
            {!! Form::date('date_payment', $date_payment, [
                'id' => 'date_payment',
                'class' => 'form-control crt_display',
                'required' => 'required',
            ]) !!}
        </div>
    </div>

    <div class="col-2 pr-0">
        <label for="ci_representant" class="m-0 font-weight-bold">Identificador <span
                class=" font-weight-normal text-muted small">[Representante]</span></label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="{{ $icon_menus['representante'] ?? '' }} fa-1x"></i>
                </span>
            </div>
            {!! Form::text('ci_representant', $ci_representant, [
                'class' => 'form-control',
                'placeholder' => 'Identificador',
                'id' => 'ci_representant',
                'maxlength' => '10',
            ]) !!}
        </div>
    </div>

    <div class="col-2 pr-2">
        <label for="btn-group-actions" class="m-0 font-weight-bold d-block">Acción</label>
        <div class="btn-group  btn-block" id="btn-group-actions">
            <button class="btn btn-primary btn-block" type="submit">
                <i class="fa fa-search" aria-hidden="true"></i>
                Buscar
            </button>
            {{-- <a id="btn_xls" href="#"  class="btn btn-success" >
                <i class="fas fa-file-excel text-light" aria-hidden="true"></i>
            </a> --}}

        </div>
    </div>

</div>

{{-- @include('administracion.registropagos.form.modal.filters') --}}
