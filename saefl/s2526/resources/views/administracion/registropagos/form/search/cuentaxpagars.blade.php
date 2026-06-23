<div class="form-row">

    <div class="col-6 pr-0">
        <label for="mensualidad" class="m-0 font-weight-bold">
            Mensualidad de referencia
            <span class=" font-weight-normal text-muted small">[Periodo mensual]</span>
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="{{ $icon_menus['cuentaxpagars'] ?? '' }} fa-1x"></i>
                </span>
            </div>
            {!! Form::select('mensualidad', $list_cuentaxpagar, $mensualidad, [
                'class' => 'form-control',
                'id' => 'mensualidad',
                'placeholder' => 'Seleccione periodo',
            ]) !!}
        </div>
    </div>

    {{-- 
    <div class="col-3 pr-0">
        <label for="planpago_id" class="m-0 font-weight-bold">Plan de Pago</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="{{ $icon_menus['planpago'] ?? '' }} fa-1x"></i>
                </span>
            </div>
            {!! Form::select('planpago_id', $list_planpago, $planpago_id, [
                'class' => 'form-control',
                'id' => 'planpago_id',
                'placeholder' => 'Todos',
            ]) !!}
        </div>
    </div> 
    --}}

    <div class="col-4 pr-1">
        <label for="grado_id" class="m-0 font-weight-bold">Grado</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="{{ $icon_menus['grado'] ?? '' }} fa-1x"></i>
                </span>
            </div>
            {!! Form::select('grado_id', $list_grado, $grado_id, [
                'class' => 'form-control',
                'id' => 'grado_id',
                'placeholder' => 'Todos',
            ]) !!}
        </div>
    </div>

    <div class="col-2 pr-2">
        <label for="btn-group-actions" class="m-0 font-weight-bold d-block">Acción</label>
        <div class="btn-group  btn-block" id="btn-group-actions">
            <button class="btn btn-primary btn-block" type="submit">
                <i class="fa fa-search" aria-hidden="true"></i>
            </button>
        </div>
    </div>

</div>
