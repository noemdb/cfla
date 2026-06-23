<fieldset class="form_fieldset pt-2">
    <h6>Paso 3: Deuda pendiente</h6>
    @include('administracion.registropagos.form.exchange.partials.cuentaxpagars')
    <hr>
    <input type="button" name="previous" class="previous-form btn btn-default  w-25 p-0" value="Anterior"/>
    {{-- <input type="button" class="btn-incluir btn btn-secondary p-0" style="width:49%" value="Continuar" disabled/> --}}
    <input type="button" class="next-form btn btn-info w-25 p-0 float-right" value="Siguiente" />
    {{-- <input type="submit" name="submit" class="submit btn btn-success w-25 p-0" value="Guardar"/>                                 --}}
</fieldset>
