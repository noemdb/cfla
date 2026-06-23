@component('administracion.elements.forms.input')
    @slot('name', 'name')
    @slot('value', $institucion->name)
    @slot('label', 'Nombre jurídico de la institución')
@endcomponent

@component('administracion.elements.forms.input')
    @slot('name', 'rif_institution')
    @slot('value', $institucion->rif_institution)
    @slot('label', 'Registro de Información Fiscal')
@endcomponent

@component('administracion.elements.forms.input')
    @slot('name', 'email_institution')
    @slot('value',  $institucion->email_institution)
    @slot('label', 'Correo electrónico administrativo')
@endcomponent

@component('administracion.elements.forms.input')
    @slot('name', 'password')
    @slot('label', 'Contraseña de correo electrónico')
@endcomponent

<div class="form-group">
    <label for="format_bill" class="m-0">Formato de factura</label>
    <select class="form-control" name="format_bill" id="format_bill">
        <option value="2">Carta</option>
        <option value="8">Carta con membrete</option>
        <option value="4">Doble sobre carta vertical</option>
        <option value="11">Doble sobre carta horizontal</option>
        <option value="1">Media carta</option>
        <option value="10">Media carta titulos blancos</option>
        <option value="7">Talonario sobre hoja blanca</option>
        <option value="16">Talonario preimpreso</option>
        <option value="5">Tipo ticket</option>
        <option value="12">Media carta en impresora matriz de punto</option>
        <option value="14">Doble sobre carta vertical en impresora matriz de punto</option>
        <option value="13">Papel contiuo media carta sobre impresora matriz de punto</option>
    </select>

</div>
