<div class="form-row">

    <div class="col-10 pr-0">
        <label for="cuentaxpagar_id" class="m-0 font-weight-bold">
            Mensualidad de referencia
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">
                    <i class="{{ $icon_menus['cuentaxpagars'] ?? '' }} fa-1x"></i>
                </span>
            </div>
            {!! Form::select('mensualidad', $list_cuentaxpagar, $mensualidad, [
                'class' => 'form-control',
                'id' => 'cuentaxpagar_id',
                'required' => 'required',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
    </div>

    <div class="col-2 pr-0">

        <label for="btn-group-actions" class="m-0 font-weight-bold d-block">Acción</label>

        <div class="btn-group" id="btn-group-actions">

            <button class="btn btn-primary" type="submit">
                <i class="fa fa-search" aria-hidden="true"></i>
                Buscar
            </button>
            <button class="btn btn-light btn-legend" type="button">
                <i class="fa fa-info-circle" aria-hidden="true"></i>
            </button>

        </div>

    </div>

</div>
