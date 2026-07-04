<fieldset class="form_fieldset pt-2">
    <h6>Paso 4: Resumen y inalización del Registro de Pago</h6>
    @include('administracion.registropagos.form.exchange.ingreso')
    <hr>
    <input type="button" name="previous" class="previous-form btn btn-default  w-25 p-0" value="Anterior" />
    {{-- <input type="button" class="btn-incluir btn btn-secondary p-0" style="width:49%" value="Continuar" disabled/> --}}
    <input type="button" class="next-form btn btn-info w-25 p-0 float-right" value="Siguiente" disabled/>
    {{-- <input type="submit" name="submit" class="submit btn btn-success w-25 p-0" value="Guardar"/>                                 --}}
</fieldset>


<button type="submit" class="submit btn btn-success p-2 mt-2 btn-block" value="Registrar" data-id="create" id="btn-create-registropago">
    <i class="far fa-save"></i>
    Finalizar
</button>