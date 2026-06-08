<div style="font-size: 0.7rem"><strong>CORRESPONDIENTE A:</strong></div>
<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.7rem;margin-bottom:0.1rem; padding-bottom:0.1rem;border:1px solid #ccc">
    @forelse ($recibo_pagos as $recibo_pago)
        <tr align="left">
            <th align="left" width="15%" style="padding-left: 0.4rem">CONCEPTO:</th>
            <th>{{$recibo_pago->quota ?? ''}}</th>
            <th>{{f_float($recibo_pago->exchange_ammount)}}</th>
        </tr>
    @empty
        {{-- <tr> <td colspan="2">No hay operacionesde vuelto</td> </tr> --}}
    @endforelse

</table>

{{-- <div style="margin-bottom:0.2rem;padding-bottom:0.2rem;font-size: 0.8rem !important;border:1px solid #ccc;">
    <strong>Observaciones:</strong>
    @forelse ($ingreso_cashs as $ingreso)
        {{$ingreso->ingreso_observations}},
    @empty
    @endforelse
</div> --}}

<div style="font-size: 0.7rem;text-align:right;">
    <b>TOTAL PAGADO: {{f_float($ammount_pagos)}}</b>
</div>
