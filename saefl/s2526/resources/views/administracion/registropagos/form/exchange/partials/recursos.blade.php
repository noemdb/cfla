
@php
    $ammount_expire_bill = $representant->ammount_expire_bill;
    $total_credito = ($representant->total_credito>0) ? $representant->total_credito : null ;
    $total_abono = ($representant->total_abono>0) ? $representant->total_abono : null ;
    $saldo_a_favor = $total_credito+$total_abono;

    $exchange_ammount_expire_bill = $representant->exchange_ammount_expire_bill;
    $total_credito_exchange = $representant->total_credito_exchange ;
    $ammount_expire_bill_exchange = ($exchange_rate_current) ? $exchange_rate_current->ammount * $exchange_ammount_expire_bill : null;
    $total_abono_exchange = $representant->total_abono_exchange ;
    $saldo_a_favor_exchange = $total_credito_exchange + $total_abono_exchange;
@endphp
<div class="card border rounded border-info">
    <div class="card-header alert-info">
        <h6 class="card-title p-1 m-1">
            <b>CREDITOS A FAVOR</b><br>
            <span class="badge badge-info">
                Bs. {{f_float($total_credito) ?? ''}} | $ {{ f_float($total_credito_exchange)}}
            </span>
        </h6>
    </div>
    <div class="card-body py-1">
        @includeWhen($total_credito_exchange > 0, 'administracion.registropagos.form.exchange.partials.creditos')
    </div>
</div>
<hr class="py-1 m-1">
<div class="card border rounded border-secondary">
        <div class="card-header alert-secondary">
        <h6 class="card-title p-1 m-1">
            <b>ABONOS EN TRANSITO</b><br>
            <span class="badge badge-secondary">
                Bs. {{f_float($total_abono) ?? ''}} | $ {{ f_float($total_abono_exchange)}}
            </span>
        </h6>
    </div>
    <div class="card-body py-1">
        {{-- @includeWhen($total_abono > 0, 'administracion.registropagos.form.fields.representant.abonos') --}}
        @includeWhen($total_abono > 0, 'administracion.registropagos.form.exchange.partials.abonos')
    </div>
</div>
