<div class="card first-of-type h-100 bd-callout bd-callout-success" id="form-cuentaxpagars-adelantado">
    <div class="card-body flex-fill" style="min-height: 500px;">
        <h6 class="alert alert-success card-title font-weight-bold text-uppercase">Paso 4: <span class="text-success"><u>Pago Adelantado</u></span> por Estudiante</h6>
        <p class="card-text">
            @include('administracion.registropagos.form.asistent.fields.adelantadosNav')
        </p>
    </div>
    <fieldset class="form_fieldset py-2 px-1 font-weight-bold">
        <input type="button" class="next-form btn btn-light  w-50 p-0" value="Anterior" data-frm-prev="form-cuentaxpagars-adelantado" data-frm-next="form-cuentaxpagars" data-direction="down"/>
        <input type="button" class="next-form btn btn-info w-50 p-0 float-right" value="Siguiente" data-frm-prev="form-cuentaxpagars-adelantado" data-frm-next="form-finalizar" data-direction="up"/>
    </fieldset>
</div>
