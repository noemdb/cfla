<div class="card first-of-type h-100 bd-callout bd-callout-danger" id="form-cuentaxpagars">
    <div class="card-body flex-fill" style="min-height: 500px;">
        <h6 class="alert alert-danger card-title font-weight-bold text-uppercase">Paso 3: <span class=" text-danger"><u>Deuda Venciada</u></span>  por Estudiante</h6>
        <p class="card-text">
            {{-- @include('administracion.registropagos.form.asistent.fields.cuentaxpagars') --}}
            @include('administracion.registropagos.form.asistent.fields.cuentaxpagarsNav')
        </p>
    </div>
    <fieldset class="form_fieldset py-2 px-1 font-weight-bold">
        <input type="button" class="next-form btn btn-light  w-50 p-0" value="Anterior" data-frm-prev="form-cuentaxpagars" data-frm-next="form-recursos" data-direction="down"/>
        <input type="button" class="next-form btn btn-info w-50 p-0 float-right" value="Siguiente" data-frm-prev="form-cuentaxpagars" data-frm-next="form-cuentaxpagars-adelantado" data-direction="up"/>
        {{-- <button type="submit" class="btn-register next-form btn btn-success w-50 p-0 float-right" value="Registrar" data-id="create" id="btn-create-registropago">
            <i class="far fa-save"></i>
            Registrar Pago
        </button> --}}
    </fieldset>
</div>
