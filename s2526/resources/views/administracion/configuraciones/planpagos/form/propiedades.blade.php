@component('administracion.elements.forms.input')
    @slot('name', 'number_fill_contingency')
    @slot('value', $institucion->number_fill_contingency)
    @slot('label', 'Último número de factura de contingencia utilizado')
@endcomponent

@component('administracion.elements.forms.input')
    @slot('name', 'last_number_bill_config')
    @slot('value', $institucion->last_number_bill_config)
    @slot('label', 'Último número de factura utilizado')
@endcomponent

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

@component('administracion.elements.forms.check')
    @slot('name', 'status_skip_discount')
    @slot('value', $institucion->status_skip_discount)
    @slot('label', 'Omitir descuentos al generar cuentas por cobrar en lote')
@endcomponent

@component('administracion.elements.forms.check')
    @slot('name', 'status_enabled_inscription_academic')
    @slot('value', $institucion->status_enabled_inscription_academic)
    @slot('label', 'Habilitar opción de inscripción académica simultánea')
@endcomponent

@component('administracion.elements.forms.check')
    @slot('name', 'status_apply_tax')
    @slot('value', $institucion->status_apply_tax)
    @slot('label', 'Activar cobro de impuesto por venta en facturación')
@endcomponent

@component('administracion.elements.forms.input_sm')
    @slot('name', 'concept_islr')
    @slot('value', $institucion->concept_islr)
    @slot('maxlength', '4')
    @slot('size', '4')
    @slot('label', 'Concepto para retención de impuesto sobre la renta en pagos')
@endcomponent

@component('administracion.elements.forms.input_sm')
    @slot('name', 'percent_tax')
    @slot('value', $institucion->percent_tax)
    @slot('maxlength', '4')
    @slot('size', '4')
    @slot('label', 'Porcentaje impuesto por venta')
@endcomponent

<div class="form-group">
    <label for="provider_payonline" class="m-0">Proveedor de servicio para Pagos en Línea</label>
    @php($list_provider_payonline = ['0' => 'NINGUNO', '1' => 'INSTAPAGO'])
    {!! Form::select('provider_payonline', $list_provider_payonline, old('provider_payonline'), [
        'class' => 'form-control',
        'value' => $institucion->provider_payonline,
    ]) !!}
</div>

<div class="form-group">
    <label for="bank_payonline" class="m-0">Banco receptor de Pagos en Línea y a través del POS Virtual</label>
    @php($list_bank_payonline = ['0' => 'NINGUNO', '339' => 'BANCARIBE', '338' => 'BANCO DEL TESORO', '368' => 'CREDITOS AÑO ANTERIOR', '392' => 'PANDCO'])
    {!! Form::select('bank_payonline', $list_bank_payonline, old('bank_payonline'), ['class' => 'form-control']) !!}
</div>

@component('administracion.elements.forms.input_sm')
    @slot('name', 'percent_comission_payonline')
    @slot('value', $institucion->percent_comission_payonline ?? '0.00')
    @slot('maxlength', '4')
    @slot('size', '4')
    @slot('label', 'Porcentaje de comisión por servicio para Pagos en Línea')
@endcomponent

@component('administracion.elements.forms.input_sm')
    @slot('name', 'percent_POSVirtual')
    @slot('value', $institucion->percent_POSVirtual ?? '0.00')
    @slot('maxlength', '4')
    @slot('size', '4')
    @slot('label', 'Porcentaje por servicio para Pagos por POS Virtual')
@endcomponent

@component('administracion.elements.forms.check')
    @slot('name', 'status_exchange_rate')
    @slot('value', $institucion->status_exchange_rate)
    @slot('label', 'Activar cobro por tasa cambio')
@endcomponent

@component('administracion.elements.forms.input_sm')
    @slot('name', 'ammount_exchange_rate')
    @slot('value', $institucion->ammount_exchange_rate)
    @slot('maxlength', '4')
    @slot('size', '4')
    @slot('label', 'Tasa de cambio')
@endcomponent

@component('administracion.elements.forms.textarea')
    @slot('name', 'observation_default_bill')
    @slot('value', $institucion->observation_default_bill)
    @slot('rows', '4')
    @slot('label', 'Observaciones por defecto en la factura')
@endcomponent

@component('administracion.elements.forms.textarea')
    @slot('name', 'observation_default_billing_notice')
    @slot('value', $institucion->observation_default_billing_notice)
    @slot('rows', '4')
    @slot('label', 'Observaciones en avisos de cobro enviados a correos')
@endcomponent

@component('administracion.elements.forms.textarea')
    @slot('name', 'txt_contract_study')
    @slot('value', $institucion->txt_contract_study)
    @slot('rows', '10')
    @slot('label', 'Texto del contrato de servicio')
@endcomponent
