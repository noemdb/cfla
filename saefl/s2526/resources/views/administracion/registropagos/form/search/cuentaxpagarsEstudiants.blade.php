<div class="form-row">
    <div class="col-6 pr-0">
        <label for="cuentaxpagar_id" class="m-0 font-weight-bold">Concepto
            {{-- <span class=" font-weight-normal text-muted small"> P.Palgo / [Concepto || F.Vencimiento]</span> --}}
        </label>
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
            {{-- 'readonly' --}}
        </div>
    </div>

    <div class="col-4 pr-0">
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
            </button>
        </div>
    </div>

</div>
