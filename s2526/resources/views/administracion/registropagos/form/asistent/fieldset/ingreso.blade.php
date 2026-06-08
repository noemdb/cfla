<div class="card h-100 bd-callout bd-callout-primary" id="form-ingresos">
    <div class="card-body flex-fill py-1" style="min-height: 500px;">
        <h6 class="alert alert-primary card-title font-weight-bold text-uppercase">Paso 1: Datos de la operación</h6>
        <p class="card-text">
            @include('administracion.registropagos.form.asistent.fields.ingreso')
        </p>
    </div>
    <fieldset class="form_fieldset py-1 px-1 font-weight-bold">
        <input type="button" name="previous" class="previous-form btn btn-light  w-50 p-0" value="" disabled/>
        <input type="button" class="next-form btn btn-info w-50 p-0 float-right" value="Siguiente" data-frm-next="form-recursos" data-frm-prev="form-ingresos" data-direction="up" />
    </fieldset>
</div>

