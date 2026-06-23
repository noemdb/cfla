@php
    function money ($b,$d,$complete=false) {
        $text = '<span style="color:#242424;"> $ '.f_float($d).'</span>';
        $text = ($complete) ? str_pad(f_float($b),7,'_',STR_PAD_LEFT).' B<span style="text-transform: lowercase">s</span>.  || '.$text: $text ;
        return $text;
    }
@endphp

@php
    $representant = $registro_pago_combinado->representant;
    $registropagos = $registro_pago_combinado->registropagos->sortBy('estudiant_id');
    $estudiants = $representant->estudiants_formaly;

    $ingreso_cashs = $registro_pago_combinado->ingreso_cashs;
    $ammount_ingreso_cashs = $registro_pago_combinado->ammount_ingreso_cashs;

    $cash_changes = $registro_pago_combinado->cash_changes;
    $ammount_cash_changes = $registro_pago_combinado->ammount_cash_changes;

    $registropagos = $registro_pago_combinado->registropagos;
    $ammount_pagado = $registro_pago_combinado->ammount_pagado;
    $ammount_pagado_exchange = $registro_pago_combinado->ammount_pagado_exchange;

    $ammount_transferencia = $ammount_pagado_exchange - $ammount_ingreso_cashs;

    $creditos_generados = $registro_pago_combinado->creditos_generados;
    $ammount_creditos_generados = $registro_pago_combinado->ammount_creditos_generados;
    $ammount_creditos_generados_exchange = $registro_pago_combinado->ammount_creditos_generados_exchange;

    $ammount_ingresos = $registro_pago_combinado->ammount_ingresos;
    $ammount_ingresos = $registro_pago_combinado->ammount_ingresos;
    $ammount_ingresos_exchange = $registro_pago_combinado->ammount_ingresos_exchange;

    $abonos_aplicados = $registro_pago_combinado->abonos_aplicados;
    $ammount_abonos_aplicados = $registro_pago_combinado->ammount_abonos_aplicados;
    $ammount_abonos_aplicados_exchange = $registro_pago_combinado->ammount_abonos_aplicados_exchange;

    $creditos_aplicados = $registro_pago_combinado->creditos_aplicados;
    $ammount_creditos_aplicados = $registro_pago_combinado->ammount_creditos_aplicados;
    $ammount_creditos_aplicados_exchange = $registro_pago_combinado->ammount_creditos_aplicados_exchange;

    $total_ingresos = $ammount_ingresos + $ammount_abonos_aplicados + $ammount_creditos_aplicados;
    $total_ingresos_exchange = $ammount_ingresos_exchange + $ammount_abonos_aplicados_exchange + $ammount_creditos_aplicados_exchange;

@endphp

<div align="center">
<table style="max-width:90%" cellpadding="4" cellspacing="4" style=" font-size:0.7rem;margin-bottom:0.2rem; padding-bottom:0.2rem;border:1px solid #ccc">
    <tr> <td width="100%">@include('administracion.representants.recibo.partials.representant')</td> </tr>
    <tr> <td width="100%">@include('administracion.representants.recibo.partials.bills')</td> </tr>
    <tr> <td width="100%">@include('administracion.representants.recibo.partials.ingresos')</td> </tr>
</table>  
</div>