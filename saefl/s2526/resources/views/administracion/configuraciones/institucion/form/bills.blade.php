<label for="format_bill" class="m-0 font-weight-bold text-secondary ">Formato de factura</label>
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

<div class="row pt-2">
    <div class="col">
        @component('administracion.elements.forms.input')
        @slot('name', 'number_fill_contingency')
        @slot('value', $institucion->number_fill_contingency)
        @slot('label', 'Último número de factura de contingencia utilizado')
        @endcomponent
    </div>
    <div class="col">
        @component('administracion.elements.forms.input')
        @slot('name', 'last_number_bill_config')
        @slot('value', $institucion->last_number_bill_config)
        @slot('label', 'Último número de factura utilizado')
        @endcomponent
    </div>
</div>

@component('administracion.elements.forms.check')
@slot('name', 'status_enabled_number_a_bill')
@slot('value', $institucion->status_enabled_number_a_bill)
@slot('label', 'Activar numeración automática de factura')
@endcomponent

@component('administracion.elements.forms.check')
@slot('name', 'status_print_bill_economical')
@slot('value', $institucion->status_print_bill_economical)
@slot('label', 'Imprimir facturas en modo económico')
@endcomponent

@component('administracion.elements.forms.check')
@slot('name', 'status_dont_allow_registration_if_insolvency')
@slot('value', $institucion->status_dont_allow_registration_if_insolvency)
@slot('label', 'No permitir registro de inscripciones en caso de insolvencia')
@endcomponent

@component('administracion.elements.forms.check')
@slot('name', 'status_no_show_info_academic')
@slot('value', $institucion->status_no_show_info_academic)
@slot('label', 'No mostrar información académica en caso de insolvencia')
@endcomponent

@component('administracion.elements.forms.check')
@slot('name', 'status_proof_of_payment')
@slot('value', $institucion->status_proof_of_payment)
@slot('label', 'Permitir registro de pagos con recibos electrónicos')
@endcomponent

@component('administracion.elements.forms.check')
@slot('name', 'status_credit_bills')
@slot('value', $institucion->status_credit_bills)
@slot('label', 'Permitir registro de facturas a crédito')
@endcomponent

@component('administracion.elements.forms.check')
@slot('name', 'status_print_number_bill')
@slot('value', $institucion->status_print_number_bill)
@slot('label', 'Emitir numeración en factura')
@endcomponent

@component('administracion.elements.forms.textarea')
@slot('name', 'observation_default_bill')
@slot('value', $institucion->observation_default_bill)
@slot('rows', '4')
@slot('label', 'Observaciones por defecto en la factura')
@endcomponent
