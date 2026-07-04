<div class="form-row">

    <div class="col-3 pr-0">
        <label for="status_annuity" class="m-0 font-weight-bold">Cuenta</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="{{ $icon_menus['concepto_pagos'] ?? '' }} fa-1x"></i>
                </span>
            </div>
            {!! Form::select('status_annuity', ['false' => 'Mensualidades', 'true' => 'Anulidades'], $status_annuity, [
                'class' => 'form-control',
                'id' => 'status_annuity',
                'placeholder' => 'Seleccione',
                'required',
            ]) !!}
        </div>
    </div>

    <div class="col-2 pr-0">
        <label for="finicial" class="m-0 font-weight-bold">Fecha Inicial</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    {{-- <i class="{{ $icon_menus['concepto_pagos'] ?? ''}} fa-1x"></i> --}}
                    <i class="fa fa-calendar" aria-hidden="true"></i>
                </span>
            </div>
            {!! Form::date('finicial', $finicial, ['id' => 'finicial', 'class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-2 pr-0">
        <label for="ffinal" class="m-0 font-weight-bold">Fecha Final</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    {{-- <i class="{{ $icon_menus['concepto_pagos'] ?? ''}} fa-1x"></i> --}}
                    <i class="fa fa-calendar" aria-hidden="true"></i>
                </span>
            </div>
            {!! Form::date('ffinal', $ffinal, ['id' => 'ffinal', 'class' => 'form-control']) !!}
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

    <div class="col-3 pr-2">
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
