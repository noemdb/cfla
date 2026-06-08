<div class="card first-of-type h-100 bd-callout bd-callout-secondary" id="form-finalizar">
    <div class="card-body" style="min-height: 500px;">
        {{-- <div class="d-flex align-items-center "> --}}
            <h6 class="alert alert-info card-title font-weight-bold text-uppercase">Paso 5: Registrar Pago</h6>
            <div class="d-flex align-items-center justify-content-center text-center h-100" style="min-height: 400px;">
                <div class="font-weight-bold text-muted">
                    Click en <br>
                    <div class="btn-block">
                        <button type="submit" class="btn btn-success btn-block btn-lg shadow" value="Registrar" id="btn-create-registropago">
                            <i class="far fa-save"></i>
                            Registrar Pago
                        </button>
                    </div>
                    para finalizar el asistente
                </div>
            </div>
        {{-- </div> --}}
    </div>
    <fieldset class="form_fieldset py-2 px-1 font-weight-bold">
        <input type="button" class="next-form btn btn-default  w-50 p-0" value="Anterior" data-frm-prev="form-finalizar" data-frm-next="form-cuentaxpagars-adelantado" data-direction="down"/>
        {{-- <input type="button" class="next-form btn btn-light  w-50 p-0" value="Inicio" data-frm-prev="form-finalizar" data-frm-next="form-ingresos" data-direction="down"/> --}}
        {{-- <button type="submit" class="btn-register next-form btn btn-success w-50 p-0 float-right" value="Registrar" data-id="create" id="btn-create-registropago">
            <i class="far fa-save"></i>
            Registrar Pago
        </button> --}}
    </fieldset>
</div>
