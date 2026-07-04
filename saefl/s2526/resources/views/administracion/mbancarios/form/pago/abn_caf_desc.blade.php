<div class="font-weight-bolder border-bottom py-1 my-1">
    <i class="{{ $icon_menus['registropagos'] ?? '' }} text-success"></i>
    CRÉDITOS A FAVOR
</div>
@include('administracion.mbancarios.form.pago.partials.caf')

<div class="font-weight-bolder border-bottom py-1 my-1">
    <i class="{{ $icon_menus['abonos'] ?? '' }} text-info"></i>
    ABONOS EN TRANSITO
</div>
@include('administracion.mbancarios.form.pago.partials.abn')

<div class="font-weight-bolder border-bottom py-1 my-1">
    <i class="{{ $icon_menus['plan_beneficos'] ?? '' }}"></i>
    DESCUENTOS
</div>
@include('administracion.mbancarios.form.pago.partials.descuentos')
