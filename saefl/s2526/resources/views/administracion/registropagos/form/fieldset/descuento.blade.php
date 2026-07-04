<fieldset class="form_fieldset">
    <h6> Paso 3: Descuentos</h6>
    @foreach ($estudiants as $estudiant)
        <span class="small"><b>{{$estudiant->fullname ?? ''}}</b></span>
        @include('administracion.registropagos.form.fields.descuento')
    @endforeach                                 
    <input type="button" name="previous" class="previous-form btn btn-default w-25 p-0" value="Anterior" />
    <input type="button" class="next-form btn btn-info w-25 p-0 float-right" value="Siguiente" disabled/>
                                    
</fieldset>