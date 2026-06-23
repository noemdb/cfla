<div class="form-row">

    <div class="col-sm-2 pr-0">
        <label for="planpago_id" class="m-0 font-weight-bold">Plan de Pago</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="{{ $icon_menus['planpagos'] ?? 'fas fa-money-bill-wave' }} fa-1x"></i>
                </span>
            </div>
            {!! Form::select('planpago_id', $list_planpago, $planpago_id, [
                'class' => 'form-control',
                'id' => 'planpago_id',
                'placeholder' => 'Todos los planes',
            ]) !!}
        </div>
    </div>

    <div class="col-sm-4 pr-0">
        <label for="cuentaxpagar_id" class="m-0 font-weight-bold">Concepto de Cobro</label>
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
            ]) !!}
        </div>
    </div>

    <div class="col-sm-2 pr-0">
        <label for="number_i_pay" class="m-0 font-weight-bold">Núm.Referencia</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="{{ $icon_menus['banco'] ?? '' }} fa-1x"></i>
                </span>
            </div>
            {!! Form::text('number_i_pay', $number_i_pay, [
                'class' => 'form-control',
                'placeholder' => 'Referencia',
                'id' => 'number_i_pay',
            ]) !!}
        </div>
    </div>

    <div class="col-sm-2 pr-0">
        <label for="ci" class="m-0 font-weight-bold" title="[Estudiante/Representante">Identificador.</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="{{ $icon_menus['estudiante'] ?? '' }} fa-1x"></i>
                </span>
            </div>
            {!! Form::text('ci', $ci, [
                'class' => 'form-control',
                'placeholder' => 'Identificador',
                'id' => 'ci',
                'maxlength' => '20',
            ]) !!}
        </div>
    </div>

    <div class="col-sm-2 pr-0">
        <label for="ci" class="m-0 font-weight-bold"
            title="Plan de Pago/Afect.Inscripción">P.Pago/A.Inscripción</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="{{ $icon_menus['inscripciones'] ?? '' }} fa-1x"></i>
                </span>
            </div>
            {!! Form::select('status_inscription_affects', ['true' => 'SI', 'false' => 'NO'], $status_inscription_affects, [
                'class' => 'form-control',
                'id' => 'status_inscription_affects',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
    </div>


</div>

</div>

<div class="form-row d-flex justify-content-between">
    <div class="flex-grow-1">
        <label for="mensualidad" class="m-0 font-weight-bold">
            Mensualidad ref.
            <span class=" font-weight-normal text-muted small">[Periodo mensual]</span>
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="{{ $icon_menus['calendario'] ?? 'fas fa-calendar-alt' }} fa-1x"></i>
                </span>
            </div>
            {!! Form::select('mensualidad', $list_cuentaxpagar_mensualidad, $mensualidad, [
                'class' => 'form-control',
                'id' => 'mensualidad',
                'placeholder' => 'Seleccione periodo',
            ]) !!}
        </div>
    </div>

    <div class="ml-2">
        <div class="float-right">
            <label for="btn-group-actions" class="m-0 font-weight-bold d-block">Acción</label>
            <div class="btn-group" id="btn-group-actions">
                <button class="btn btn-primary my-2 my-sm-0 w-50" type="submit">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
                <a id="btn_tools" href="#" class="btn btn-info w-50" title="Más opciones..." data-toggle="modal"
                    data-target="#filters">
                    <i class="fas fa-filter" aria-hidden="true"></i>
                </a>

            </div>
        </div>

    </div>
</div>

@include('administracion.registropagos.form.modal.filters')
