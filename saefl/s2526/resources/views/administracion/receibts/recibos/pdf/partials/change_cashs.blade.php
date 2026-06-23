
<table width="100%" cellpadding="0" cellspacing="0" style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;border:1px solid #ccc">
    @forelse ($cash_changes as $caf)
        <tr align="left">
            <th align="left" width="15%" style="padding-left: 0.4rem">Monto</th>
            <th>{{f_float($caf->exchange_ammount)}}</th>
        </tr>
    @empty
        {{-- <tr> <td colspan="2">No hay operacionesde vuelto</td> </tr> --}}
    @endforelse
</table>

<div style="font-size: 0.8rem;text-align:right; border:1px solid #ccc">
    <b>Total vuelto: {{f_float($cash_changes->sum('exchange_ammount'))}}</b>
</div>
