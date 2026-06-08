
{{-- <div style="font-size: 0.8rem; font-weight: bold">Efectivo</div> --}}
<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.7rem;margin-bottom:0.1rem; padding-bottom:0.1rem;border:1px solid #ccc">
    @forelse ($ingreso_cashs as $ingreso)
        <tr align="left">
            <th align="left" width="15%" style="padding-left: 0.4rem">Serial. </th>
            <th>{{$ingreso->number_i_pay}}</th>
            <th align="left" width="15%" style="padding-left: 0.4rem">Monto</th>
            <th align="right">{{f_float($ingreso->exchange_ammount)}}</th>
        </tr>
    @empty
        <tr> <td colspan="3">No hay operaciones en efectivo asociado a este pago</td> </tr>
    @endforelse
</table>

<div style="font-size: 0.7rem;text-align:right; border:1px solid #ccc; margin-bottom:0.1rem; padding-bottom:0.1rem;">
    <b>Total efectivo: {{f_float($ammount_ingreso_cashs)}}</b>
</div>
<div style="font-size: 0.7rem; color:#666;text-align:right; border:1px solid #ccc;margin-bottom:0.1rem; padding-bottom:0.1rem;">
    <div>Total Tranferencias Electŕonicas y/o otros medios de pago: <b>{{f_float($ammount_transferencia)}}</b></div>
</div>

