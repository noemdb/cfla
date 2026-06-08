<div class="card first-of-type h-100 shadow bd-callout bd-callout-info" id="form-recursos">
    <div class="card-body flex-fill" style="min-height: 500px;">
        <h6 class="alert alert-secondary card-title font-weight-bold text-uppercase">Paso 2: SALDO A FAVOR</h6>
        <div class="px-2">
            @include('administracion.registropagos.form.asistent.fields.recursos')
        </div>
    </div>
    <fieldset class="form_fieldset py-2 px-1 font-weight-bold">
        <input type="button" class="next-form btn btn-light  w-50 p-0" value="Anterior" data-frm-prev="form-recursos" data-frm-next="form-ingresos" data-direction="down"/>
        <input type="button" class="next-form btn btn-info w-50 p-0 float-right" value="Siguiente" data-frm-prev="form-recursos" data-frm-next="form-cuentaxpagars" data-direction="up"/>
    </fieldset>
</div>
