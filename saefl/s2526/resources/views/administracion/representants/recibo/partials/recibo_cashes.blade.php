
<div style="font-size: 0.8rem; font-weight: bold">Efectivo</div>
<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;border:1px solid #ccc">
    @forelse ($recibo_cashes as $recibo_cash)
        <tr align="left">
            <th align="left" width="15%" style="padding-left: 0.4rem">Serial: </th>
            <th>{{$recibo_cash->serial}}</th>
            <th align="left" width="15%" style="padding-left: 0.4rem">Monto</th>
            <th align="right">{{f_float($recibo_cash->exchange_ammount)}}</th>
        </tr>
    @empty
        <tr> <td colspan="3">No hay operaciones en efectivo asociado a este pago</td> </tr>
    @endforelse
</table>

<div style="font-size: 0.8rem;text-align:right; border:1px solid #ccc; margin-bottom:0.2rem; padding-bottom:0.2rem;">
    <b>Total efectivo: {{f_float($ammount_cashes)}}</b>
</div>

